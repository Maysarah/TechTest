<?php


namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ArticleController;
use App\Services\ArticleService;
use App\Services\ResponseHandlerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    /**
     * @dataProvider apiRequestDataProvider
     */
    public function testIndexApiRequest(Request $request, JsonResponse $expectedResponse, Collection $mockArticles)
    {
        // Mock services
        $articleServiceMock = Mockery::mock(ArticleService::class);
        $responseHandlerMock = Mockery::mock(ResponseHandlerService::class);

        // Set up expectations for ArticleService
        $articleServiceMock->shouldReceive('getArticlesWithImages')
            ->once()
            ->andReturn($mockArticles);

        // Set up expectations for ResponseHandlerService
        $responseHandlerMock->shouldReceive('handleResponse')
            ->once()
            ->with(
                Mockery::type(Collection::class),
                Mockery::type(Request::class)
            )
            ->andReturn($expectedResponse);

        $controller = new ArticleController($articleServiceMock, $responseHandlerMock);

        $response = $controller->index($request);

        // Assert the response content
        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        $this->assertCount($mockArticles->count(), $responseData['data']);
        foreach ($responseData['data'] as $index => $article) {
            $this->assertArrayHasKey('id', $article);
            $this->assertEquals($mockArticles[$index]['id'], $article['id']);
            $this->assertEquals($mockArticles[$index]['title'], $article['title']);
        }
    }

    /**
     * @dataProvider webRequestDataProvider
     */
    public function testIndexWebRequest(Request $request, $expectedViewName, $expectedViewData, Collection $mockArticles)
    {
        // Mock services
        $articleServiceMock = Mockery::mock(ArticleService::class);
        $responseHandlerMock = Mockery::mock(ResponseHandlerService::class);

        // Set up expectations for ArticleService
        $articleServiceMock->shouldReceive('getArticlesWithImages')
            ->once()
            ->andReturn($mockArticles);

        // Mock the view response
        $viewMock = Mockery::mock(View::class);
        $viewMock->shouldReceive('name')
            ->once()
            ->andReturn($expectedViewName);
        $viewMock->shouldReceive('getData')
            ->once()
            ->andReturn($expectedViewData);

        // Set up expectations for ResponseHandlerService
        $responseHandlerMock->shouldReceive('handleResponse')
            ->once()
            ->with(
                Mockery::type('array'),
                Mockery::type(Request::class)
            )
            ->andReturn($viewMock);

        $controller = new ArticleController($articleServiceMock, $responseHandlerMock);

        $response = $controller->index($request);

        // Assert the view response
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals($expectedViewName, $response->name());
        $viewData = $response->getData();
        $this->assertArrayHasKey('articles', $viewData);
        $this->assertCount($mockArticles->count(), $viewData['articles']);
    }

    public static function apiRequestDataProvider(): array
    {
        $mockArticles = collect([
            [
                'id' => 41,
                'title' => 'First Article',
                'content' => 'This is the content of the first article.',
                'created_at' => '2024-07-30 16:34:55',
                'images' => [],
            ],
            // other articles...
        ]);

        return [
            'valid API request' => [
                Request::create('/api/articles', 'GET'),
                new JsonResponse([
                    'data' => $mockArticles->toArray(),
                ]),
                $mockArticles,
            ],
        ];
    }

    public static function webRequestDataProvider(): array
    {
        $mockArticles = collect([
            [
                'id' => 41,
                'title' => 'First Article',
                'content' => 'This is the content of the first article.',
                'created_at' => '2024-07-30 16:34:55',
                'images' => [],
            ],
            // other articles...
        ]);

        return [
            'valid web request' => [
                Request::create('/articles', 'GET'),
                'articles.index',
                ['articles' => $mockArticles->toArray()],
                $mockArticles,
            ],
        ];
    }


}
