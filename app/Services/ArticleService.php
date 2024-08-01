<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ArticleService
{
    protected ArticleImageService $imageService;

    public function __construct(ArticleImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getArticlesWithImages() {
        return Article::with('images')->get();
    }
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

    public function getArticleByIdWithImages(int $articleId): array
    {
        // Fetch article data using stored procedure
        $articleData = $this->getArticleById($articleId);

        // Find the article from the database and load images
        $article = Article::with('images')->where('id', $articleId)->first();

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



    public function updateArticle(Article $article, array $data): Article
    {
        return DB::transaction(function () use ($article, $data) {
            $article->update($data);

            if (isset($data['images'])) {
                $this->imageService->storeImages($article, $data['images']);
            }

            return $article;
        });
    }

    public function deleteArticle(Article $article): void
    {
        DB::transaction(function () use ($article) {
            $this->imageService->deleteImages($article->images);
            $article->delete();
        });
    }

    public function getArticleById(int $articleId): array
    {
        try {
            // Execute the stored procedure
            DB::statement('CALL GetArticleById(?, @p_article_title, @p_article_content)', [$articleId]);

            // Retrieve the output parameters
            $result = DB::select('SELECT @p_article_title AS title, @p_article_content AS content');

            // Handle the case where no result is found
            if (empty($result)) {
                return [
                    'title' => 'Article not found',
                    'content' => 'No content available',
                ];
            }

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
