<?php

namespace Tests\Unit\Services;

use App\Services\ResponseHandlerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

class ResponseHandlerServiceTest extends TestCase
{
    /**
     * Data provider for handleResponse tests.
     *
     * @return array
     */
    public static function responseProvider(): array
    {
        return [
            'API request' => [
                'requestUri' => '/api/some-endpoint',
                'data' => [ // Complex data structure
                    ['item1' => 'value1', 'item2' => 'value2'],
                    ['item3' => 'value3']
                ],
                'expectedResponseClass' => JsonResponse::class,
                'expectedView' => null,
                'expectedCompact' => null,
            ],
            'View response with data' => [
                'requestUri' => '/some-endpoint',
                'data' => [ // Complex data structure
                    'view' => 'sample-view',
                    'compact' => ['key1' => 'value1', 'key2' => 'value2']
                ],
                'expectedResponseClass' => View::class,
                'expectedView' => 'sample-view',
                'expectedCompact' => ['key1' => 'value1', 'key2' => 'value2'],
            ],
            'Default view response without data' => [
                'requestUri' => '/some-endpoint',
                'data' => [], // No view data
                'expectedResponseClass' => View::class,
                'expectedView' => 'default-view',
                'expectedCompact' => [],
            ],
        ];
    }

    /**
     * Test handleResponse method using data provider.
     *
     * @dataProvider responseProvider
     * @param string $requestUri
     * @param mixed $data
     * @param string $expectedResponseClass
     * @param string|null $expectedView
     * @param array|null $expectedCompact
     * @return void
     */
    public function testHandleResponse(string $requestUri, mixed $data, string $expectedResponseClass, ?string $expectedView, ?array $expectedCompact)
    {
        // Mock Request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('is')->with('api/*')->andReturn(str_starts_with($requestUri, '/api/'));

        // Mock ViewFactory
        $viewFactory = Mockery::mock(\Illuminate\View\Factory::class);
        if ($expectedView) {
            $viewFactory->shouldReceive('make')
                ->with($expectedView, $expectedCompact ?? [], [])
                ->andReturn(Mockery::mock(View::class));
        }

        // Bind the view factory mock
        $this->app->instance('view', $viewFactory);

        // Create an instance of ResponseHandlerService
        $service = new ResponseHandlerService();

        // Act
        $response = $service->handleResponse($data, $request);

        // Assert
        $this->assertInstanceOf($expectedResponseClass, $response);

        if ($expectedView) {
            $viewFactory->shouldHaveReceived('make')->with($expectedView, $expectedCompact ?? [], []);
        }
    }
}
