<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\DB;

class ArticleService
{
    protected ArticleImageService $imageService;

    public function __construct(ArticleImageService $imageService)
    {
        $this->imageService = $imageService;
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
}
