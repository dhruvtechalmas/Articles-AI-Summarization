<?php

namespace App\Http\Controllers\Api;

use App\Events\ArticleStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Jobs\ProcessArticleJob;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Articles fetched successfully.',
            'data' => $articles,
        ]);
    }

    //     public function index()
    // {
    //     $articles = Article::latest()->paginate(10);

    //     return view('articles.index', compact('articles'));
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $article = Article::where('url', $request->url)->first();

        if ($article) {
            return response()->json([
                'success' => false,
                'message' => 'Article already exists.',
                'data' => $article,
            ], 409);
        }

        
        $article = Article::create([
            'title' => $request->title,
            'url' => $request->url,
            'status' => 'processing',
        ]);

          event(new ArticleStatusUpdated($article));

        ProcessArticleJob::dispatch($article);

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully.',
            'data' => $article,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return response()->json([
            'success' => true,
            'message' => 'Article fetched successfully.',
            'data' => [
                'id' => $article->id,
                'title' => $article->title,
                'url' => $article->url,
                // 'content' => $article->content,
                'summary' => $article->summary,
                'key_points' => $article->key_points,
                'status' => $article->status,
                'created_at' => $article->created_at,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest  $request, Article $article)
    {
        $article = Article::where('url', $request->url)->first();

        $article->update([
            'title' => $request->title,
            'url' => $request->url,
            'status' => 'processing',
        ]);

        ProcessArticleJob::dispatch($article);

        return response()->json([
            'success' => true,
            'message' => 'Article Updated successfully.',
            'data' => $article,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully.',
        ]);
    }
}
