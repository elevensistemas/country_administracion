<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index()
    {
        $news = News::where(function ($q) {
                $q->where('is_published', true)
                  ->orWhere('status', 'published');
            })
            ->where('visibility', '!=', 'internal')
            ->orderByRaw('COALESCE(published_at, publish_date, created_at) DESC')
            ->paginate(10);

        return view('owner.news.index', compact('news'));
    }

    /**
     * Show news details.
     */
    public function show(News $news)
    {
        if (!$news->is_published && $news->status !== 'published') {
            abort(403, 'No tienes permiso para ver esta novedad.');
        }

        if ($news->visibility === 'internal') {
            abort(403, 'Esta novedad es de visibilidad interna.');
        }

        return view('owner.news.show', compact('news'));
    }
}
