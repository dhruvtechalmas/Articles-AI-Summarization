<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Jobs\ProcessArticleJob;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->paginate(10);

        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(StoreArticleRequest $request)
    {

        $article = Article::where('url', $request->url)->first();

        if ($article) {
            return redirect()->back()->with([
                'summary' => $article->summary,
                'message' => 'Summary already exists.',
            ]);
        }
        
        $article = Article::create([

            'title' => $request->title,

            'url' => $request->url,

            'status' => 'pending',

        ]);

        ProcessArticleJob::dispatch($article);

        // dd(env('OPENAI_API_KEY'));
        return redirect()
            ->route('articles.index')
            ->with('success', 'Article created successfully.');
    }

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }
    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $article->update([

            'title' => $request->title,

            'url' => $request->url,

        ]);

        return redirect()
            ->route('articles.index')
            ->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()
            ->route('articles.index')
            ->with('success', 'Article deleted successfully.');
    }
}