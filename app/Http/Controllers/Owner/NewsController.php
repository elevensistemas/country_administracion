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
        $news = News::where('is_published', true)
            ->orderBy('publish_date', 'desc')
            ->paginate(10);

        return view('owner.news.index', compact('news'));
    }

    /**
     * Show news details.
     */
    public function show(News $news)
    {
        if (!$news->is_published) {
            abort(403, 'No tienes permiso para ver esta novedad.');
        }

        return view('owner.news.show', compact('news'));
    }
}
