<?php

namespace Tests\Unit\Services;

use App\Models\Article;
use App\Services\ArticleService;
use App\Services\ArticleImageService;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class ArticleServiceTest extends TestCase
{
    private ArticleService $articleService;
    private Mockery\MockInterface $imageService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock of ArticleImageService
        $this->imageService = Mockery::mock(ArticleImageService::class);
        // Create an instance of ArticleService with the mocked image service
        $this->articleService = new ArticleService($this->imageService);
    }

    public function testGetArticlesWithImages()
    {
        // Mock the `Article` model
        $articleMock = Mockery::mock('alias:' . Article::class);

        // Define the behavior for the `with` method
        $articleMock->shouldReceive('with')
            ->with('images')
            ->andReturnSelf(); // Return self to allow chaining

        // Define the behavior for the `get` method
        $articleMock->shouldReceive('get')
            ->andReturn(new Collection([$articleMock])); // Return a collection containing the mock

        // Call the service method
        $articles = $this->articleService->getArticlesWithImages();

        // Assert that the result is a collection with one item
        $this->assertCount(1, $articles);
        $this->assertInstanceOf(Article::class, $articles->first());
    }



    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
