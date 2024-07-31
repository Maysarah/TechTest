<?php

namespace Tests\Unit\Services;

use App\Services\ArticleValidationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class ArticleValidationServiceTest extends TestCase
{
    /**
     * Test that valid data passes validation.
     *
     * @return void
     */
    public function testValidDataPassesValidation()
    {
        // Create a mock for the Validator
        $validator = Mockery::mock(\Illuminate\Contracts\Validation\Validator::class);

        // Set up the expected behavior of the mock
        $validator->shouldReceive('fails')->andReturn(false);
        $validator->shouldReceive('validated')->andReturn([
            'title' => 'Sample Title',
            'content' => 'This is the content of the article.',
            'images' => [],
        ]);

        // Mock the Validator facade
        Validator::shouldReceive('make')
            ->once()
            ->with(
                [
                    'title' => 'Sample Title',
                    'content' => 'This is the content of the article.',
                    'images' => [],
                ],
                [
                    'title' => 'required|max:100',
                    'content' => 'required',
                    'images.*' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
                ]
            )
            ->andReturn($validator);

        $service = new ArticleValidationService();

        $data = [
            'title' => 'Sample Title',
            'content' => 'This is the content of the article.',
            'images' => [], // Valid as images are optional
        ];

        $validated = $service->validate($data);

        $this->assertEquals($data, $validated);
    }

    /**
     * Test that invalid data fails validation.
     *
     * @return void
     */
    public function testInvalidDataFailsValidation()
    {
        // Create a mock for the Validator
        $validator = Mockery::mock(\Illuminate\Contracts\Validation\Validator::class);

        // Set up the expected behavior of the mock
        $validator->shouldReceive('fails')->andReturn(true);
        $validator->shouldReceive('errors')->andReturn(Mockery::mock(['all' => ['Validation failed']]));

        // Mock the Validator facade
        Validator::shouldReceive('make')
            ->once()
            ->with(
                [
                    'title' => '', // Title is required
                    'content' => '', // Content is required
                    'images' => [
                        'invalid_file_type' => 'not-an-image.txt',
                    ],
                ],
                [
                    'title' => 'required|max:100',
                    'content' => 'required',
                    'images.*' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
                ]
            )
            ->andReturn($validator);

        $service = new ArticleValidationService();

        $data = [
            'title' => '', // Title is required
            'content' => '', // Content is required
            'images' => [
                'invalid_file_type' => 'not-an-image.txt',
            ],
        ];

        $this->expectException(ValidationException::class);

        $service->validate($data);
    }

    /**
     * Test that data with invalid image fails validation.
     *
     * @return void
     */
    public function testInvalidImageFailsValidation()
    {
        // Create a mock for the Validator
        $validator = Mockery::mock(\Illuminate\Contracts\Validation\Validator::class);

        // Set up the expected behavior of the mock
        $validator->shouldReceive('fails')->andReturn(true);
        $validator->shouldReceive('errors')->andReturn(Mockery::mock(['all' => ['Invalid image file']]));

        // Mock the Validator facade
        Validator::shouldReceive('make')
            ->once()
            ->with(
                [
                    'title' => 'Valid Title',
                    'content' => 'Valid content.',
                    'images' => [
                        'invalid_image' => 'invalid_image.txt',
                    ],
                ],
                [
                    'title' => 'required|max:100',
                    'content' => 'required',
                    'images.*' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
                ]
            )
            ->andReturn($validator);

        $service = new ArticleValidationService();

        $data = [
            'title' => 'Valid Title',
            'content' => 'Valid content.',
            'images' => [
                'invalid_image' => 'invalid_image.txt',
            ],
        ];

        $this->expectException(ValidationException::class);

        $service->validate($data);
    }

    /**
     * Test that data with valid image passes validation.
     *
     * @return void
     */
    public function testValidImagePassesValidation()
    {
        // Create a mock for the Validator
        $validator = Mockery::mock(\Illuminate\Contracts\Validation\Validator::class);

        // Set up the expected behavior of the mock
        $validator->shouldReceive('fails')->andReturn(false);
        $validator->shouldReceive('validated')->andReturn([
            'title' => 'Valid Title',
            'content' => 'Valid content.',
            'images' => [
                'valid_image' => 'path/to/valid_image.jpg',
            ],
        ]);

        // Mock the Validator facade
        Validator::shouldReceive('make')
            ->once()
            ->with(
                [
                    'title' => 'Valid Title',
                    'content' => 'Valid content.',
                    'images' => [
                        'valid_image' => 'path/to/valid_image.jpg',
                    ],
                ],
                [
                    'title' => 'required|max:100',
                    'content' => 'required',
                    'images.*' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
                ]
            )
            ->andReturn($validator);

        $service = new ArticleValidationService();

        $data = [
            'title' => 'Valid Title',
            'content' => 'Valid content.',
            'images' => [
                'valid_image' => 'path/to/valid_image.jpg',
            ],
        ];

        $validated = $service->validate($data);

        $this->assertArrayHasKey('images', $validated);
        $this->assertCount(1, $validated['images']);
    }
}
