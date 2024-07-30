<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequestWeb;
use App\Models\Article;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WebArticleController extends Controller
{
    protected $imageService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\ImageService  $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the articles.
     *
     * @return View
     */
    public function index()
    {
        $articles = Article::with('images')->get(); // Include images when fetching articles
        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article.
     *
     * @return View
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created article in storage.
     *
     * @param  \App\Http\Requests\StoreArticleRequestWeb  $request
     * @return RedirectResponse
     */
    public function store(StoreArticleRequestWeb $request)
    {
        // Validate and create the article
        $article = Article::create($request->only(['title', 'content']));

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->imageService->storeImages($article, $request->file('images'));
        }

        return redirect()->route('articles.index')->with('success', 'Article created successfully.');
    }

    /**
     * Display the specified article.
     *
     * @param Article $article
     * @return View
     */
    public function show(Article $article)
    {
        $article->load('images'); // Include images when showing an article
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified article.
     *
     * @param Article $article
     * @return View
     */
    public function edit(Article $article)
    {
        $article->load('images'); // Include images when editing an article
        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified article in storage.
     *
     * @param  \App\Http\Requests\StoreArticleRequestWeb  $request
     * @param Article $article
     * @return RedirectResponse
     */
    public function update(StoreArticleRequestWeb $request, Article $article)
    {
        // Update the article
        $article->update($request->only(['title', 'content']));

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->imageService->storeImages($article, $request->file('images'));
        }

        return redirect()->route('articles.index')->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified article from storage.
     *
     * @param Article $article
     * @return RedirectResponse
     */
    public function destroy(Article $article)
    {
        // Delete associated images from S3
        $this->imageService->deleteImages($article->images);

        // Delete the article
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Article deleted successfully.');
    }
}
