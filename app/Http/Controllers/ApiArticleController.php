<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequestApi;

class ApiArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        return response()->json($articles);
    }

    public function store(StoreArticleRequestApi $request)
    {
        // Create the article
        $article = Article::create($request->only(['title', 'content']));

        // Return the created article as JSON
        return response()->json($article, 201);
    }

    public function show(Article $article)
    {
        return response()->json($article);
    }

    public function update(StoreArticleRequestApi $request, Article $article)
    {
        $article->update($request->only(['title', 'content']));
        return response()->json($article);
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json(null, 204);
    }
}
