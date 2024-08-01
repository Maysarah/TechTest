<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Image;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleService
{
    protected ArticleImageService $imageService;

    /**
     * Create a new instance of the ArticleService.
     *
     * @param ArticleImageService $imageService
     */
    public function __construct(ArticleImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Retrieve all articles with their associated images.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getArticlesWithImages()
    {
        return Article::with('images')->get();
    }

    /**
     * Create a new article and store associated images.
     *
     * @param array $data
     * @return Article
     */
    public function createArticle(array $data): Article
    {
        return DB::transaction(function () use ($data) {
            $article = Article::create($data);

            if (isset($data['images'])) {
                $this->imageService->storeImages($article, $data['images']);
            }

            return $article;
        });
    }

    /**
     * Retrieve an article by ID with its images, using a stored procedure.
     *
     * @param int $articleId
     * @return array
     */
    public function getArticleByIdWithImages(int $articleId): array
    {
        // Fetch article data using stored procedure
        $articleData = $this->getArticleById($articleId);

        // Find the article from the database and load images
        $article = Article::with('images')->find($articleId);

        // Handle case where the article is not found
        if (!$article) {
            return [
                'id' => $articleId, // Include the ID to support the edit link
                'title' => 'Article not found',
                'content' => 'No content available',
                'images' => []
            ];
        }

        return [
            'id' => $article->id,
            'title' => $articleData['title'],
            'content' => $articleData['content'],
            'images' => $article->images->toArray()
        ];
    }

    /**
     * Update an existing article and handle image updates and deletions.
     *
     * @param Article $article
     * @param array $data
     * @return Article
     */
    public function updateArticle(Article $article, array $data): Article
    {
        return DB::transaction(function () use ($article, $data) {
            // Update article details
            $article->update([
                'title' => $data['title'],
                'content' => $data['content'],
            ]);

            // Handle image deletions
            if (isset($data['images_to_delete'])) {
                $this->deleteImages($data['images_to_delete']);
            }

            // Handle new image uploads
            if (isset($data['images'])) {
                $this->imageService->storeImages($article, $data['images']);
            }

            return $article;
        });
    }

    /**
     * Delete an article and its associated images.
     *
     * @param Article $article
     * @return void
     */
    public function deleteArticle(Article $article): void
    {
        DB::transaction(function () use ($article) {
            // Delete images associated with the article
            $this->imageService->deleteImages($article->images->pluck('id')->toArray());

            // Delete the article
            $article->delete();
        });
    }

    /**
     * Delete images based on their IDs.
     *
     * @param array $imageIds
     * @return void
     */
    public function deleteImages(array $imageIds): void
    {
        DB::transaction(function () use ($imageIds) {
            foreach ($imageIds as $imageId) {
                $image = Image::find($imageId);
                if ($image) {
                    // Delete image file from storage
                    Storage::disk('s3')->delete($image->path);

                    // Delete image record from the database
                    $image->delete();
                }
            }
        });
    }

    /**
     * Fetch article data using a stored procedure.
     *
     * @param int $articleId
     * @return array
     */
    public function getArticleById(int $articleId): array
    {
        try {
            // Execute the stored procedure
            DB::statement('CALL GetArticleById(?, @p_article_title, @p_article_content)', [$articleId]);

            // Retrieve the output parameters
            $result = DB::select('SELECT @p_article_title AS title, @p_article_content AS content');

            // Return the result as an associative array
            return [
                'title' => $result[0]->title ?? 'Article not found',
                'content' => $result[0]->content ?? 'No content available',
            ];
        } catch (QueryException $e) {
            // Handle any query exceptions
            return [
                'title' => 'Error',
                'content' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
}
