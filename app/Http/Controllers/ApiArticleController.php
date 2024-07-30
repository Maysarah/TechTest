<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequestApi;

class ApiArticleController extends Controller
{
    /**
     * Display a listing of the articles.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $articles = Article::all();
        return response()->json($articles);
    }

    /**
     * Store a newly created article in storage.
     *
     * @param StoreArticleRequestApi $request
     * @return JsonResponse
     */
    public function store(StoreArticleRequestApi $request)
    {
        // Create the article
        $article = Article::create($request->only(['title', 'content']));

        // Return the created article as JSON
        return response()->json($article, 201);
    }

    /**
     * Display the specified article.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function show(Article $article)
    {
        return response()->json($article);
    }

    /**
     * Update the specified article in storage.
     *
     * @param StoreArticleRequestApi $request
     * @param Article $article
     * @return JsonResponse
     */
    public function update(StoreArticleRequestApi $request, Article $article)
    {
        $article->update($request->only(['title', 'content']));
        return response()->json($article);
    }

    /**
     * Remove the specified article from storage.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json(null, 204);
    }
}
