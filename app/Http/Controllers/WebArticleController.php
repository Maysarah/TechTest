<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequest;
use App\Services\ImageService;

class WebArticleController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $articles = Article::with('images')->get(); // Include images when fetching articles
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(StoreArticleRequest $request)
    {
        // Validate and create the article
        $article = Article::create($request->only(['title', 'content']));

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->imageService->storeImages($article, $request->file('images'));
        }

        return redirect()->route('articles.index')->with('success', 'Article created successfully.');
    }

    public function show(Article $article)
    {
        $article->load('images'); // Include images when showing an article
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        $article->load('images'); // Include images when editing an article
        return view('articles.edit', compact('article'));
    }

    public function update(StoreArticleRequest $request, Article $article)
    {
        // Update the article
        $article->update($request->only(['title', 'content']));

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->imageService->storeImages($article, $request->file('images'));
        }

        return redirect()->route('articles.index')->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article)
    {
        // Delete associated images from S3
        $this->imageService->deleteImages($article->images);

        // Delete the article
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Article deleted successfully.');
    }
}
