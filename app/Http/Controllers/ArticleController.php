<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use App\Services\ArticleService;
use App\Services\ResponseHandlerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ArticleController extends Controller
{
    protected ArticleService $articleService;
    protected ResponseHandlerService $responseHandlerService;

    public function __construct(ArticleService $articleService, ResponseHandlerService $responseHandlerService)
    {
        $this->articleService = $articleService;
        $this->responseHandlerService = $responseHandlerService;
    }

    /**
     * Display a listing of the articles.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function index(Request $request): JsonResponse|View
    {
        // Fetch articles with images
         $articles = $this->articleService->getArticlesWithImages();

        // Pass articles directly to handleResponse
        return $this->responseHandlerService->handleResponse(
            $request->is('api/*')
                ? $articles
                : ['view' => 'articles.index', 'compact' => compact('articles')],
            $request
        );
    }

    /**
     * Show the form for creating a new article.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function create(Request $request): JsonResponse|View
    {
        // Return the view for creating a new article
        return $this->responseHandlerService->handleResponse([
            'view' => 'articles.create',
            'compact' => [] // No additional data needed for the create form
        ], $request);
    }

    /**
     * Store a newly created article in storage.
     *
     * @param StoreArticleRequest $request
     * @return JsonResponse|View
     */
    public function store(StoreArticleRequest $request): JsonResponse|View
    {
        // Create a new article
        $article = $this->articleService->createArticle($request->validated());

        // Refresh the list of articles and return the view or JSON response
        return $this->responseHandlerService->handleResponse([
            'view' => 'articles.index',
            'compact' => [
                'articles' => Article::with('images')->get(), // Refresh the list of articles
                'message' => 'Article created successfully.'
            ]
        ], $request);
    }

    /**
     * Display the specified article.
     *
     * @param Article $article
     * @param Request $request
     * @return JsonResponse|View
     */
    public function show(Article $article, Request $request): JsonResponse|View
    {
        // Load images for the specified article
        $article->load('images');

        // Return the view or JSON response with the article data
        return $this->responseHandlerService->handleResponse([
            'view' => 'articles.show',
            'compact' => compact('article')
        ], $request);
    }

    /**
     * Show the form for editing the specified article.
     *
     * @param Article $article
     * @param Request $request
     * @return JsonResponse|View
     */
    public function edit(Article $article, Request $request): JsonResponse|View
    {
        // Load images for the specified article
        $article->load('images');

        // Return the view or JSON response for editing the article
        return $this->responseHandlerService->handleResponse([
            'view' => 'articles.edit',
            'compact' => compact('article')
        ], $request);
    }

    /**
     * Update the specified article in storage.
     *
     * @param StoreArticleRequest $request
     * @param Article $article
     * @return JsonResponse|View
     */
    public function update(StoreArticleRequest $request, Article $article): JsonResponse|View
    {
        // Update the article
        $article = $this->articleService->updateArticle($article, $request->validated());

        // Refresh the list of articles and return the view or JSON response
        return $this->responseHandlerService->handleResponse([
            'view' => 'articles.index',
            'compact' => [
                'articles' => Article::with('images')->get(), // Refresh the list of articles
                'message' => 'Article updated successfully.'
            ]
        ], $request);
    }

    /**
     * Remove the specified article from storage.
     *
     * @param Article $article
     * @param Request $request
     * @return JsonResponse|View
     */
    public function destroy(Article $article, Request $request): JsonResponse|View
    {
        // Delete the article
        $this->articleService->deleteArticle($article);

        // Refresh the list of articles and return the view or JSON response
        return $this->responseHandlerService->handleResponse([
            'view' => 'articles.index',
            'compact' => [
                'articles' => Article::with('images')->get(), // Refresh the list of articles
                'message' => 'Article deleted successfully.'
            ]
        ], $request);
    }
}
