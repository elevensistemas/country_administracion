<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of news.
     */
    public function index(Request $request)
    {
        $query = News::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $news = $query->orderBy('published_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    /**
     * Show form to create news.
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Store new announcement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string|in:draft,published,archived',
            'visibility' => 'required|string|in:public,internal',
            'published_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        
        if ($request->status === 'published' && !$request->filled('published_at')) {
            $data['published_at'] = now();
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Novedad creada correctamente.');
    }

    /**
     * Show edit form.
     */
    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update news details.
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string|in:draft,published,archived',
            'visibility' => 'required|string|in:public,internal',
            'published_at' => 'nullable|date',
        ]);

        $data = $request->all();
        
        if ($request->status === 'published' && !$news->published_at) {
            $data['published_at'] = now();
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Novedad actualizada correctamente.');
    }

    /**
     * Remove news from system.
     */
    public function destroy(News $news)
    {
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'Novedad eliminada correctamente.');
    }
}
