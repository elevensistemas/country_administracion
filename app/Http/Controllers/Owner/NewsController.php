<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Lot;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index()
    {
        $user = auth()->user();

        // Base query: published and not internal
        $query = News::where(function ($q) {
                $q->where('is_published', true)
                  ->orWhere('status', 'published');
            })
            ->where('visibility', '!=', 'internal');

        // If not administrative role, filter by target lots
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            $userLotIds = $user->functionalUnits()->pluck('lot_id')->filter()->unique()->toArray();
            $ownerLotIds = Lot::whereHas('owner', function ($oq) use ($user) {
                $oq->where('email', $user->email);
            })->pluck('id')->toArray();
            $myLotIds = array_unique(array_merge($userLotIds, $ownerLotIds));

            $query->where(function ($targetQuery) use ($myLotIds) {
                $targetQuery->where('recipients_type', 'all')
                    ->orWhere(function ($sq) use ($myLotIds) {
                        $sq->where('recipients_type', 'specific_lots')
                           ->whereHas('lots', function ($lq) use ($myLotIds) {
                               $lq->whereIn('lots.id', $myLotIds);
                           });
                    });
            });
        }

        $news = $query->orderByRaw('COALESCE(published_at, publish_date, created_at) DESC')
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

        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isSuperAdmin() && $news->recipients_type === 'specific_lots') {
            $userLotIds = $user->functionalUnits()->pluck('lot_id')->filter()->unique()->toArray();
            $ownerLotIds = Lot::whereHas('owner', function ($oq) use ($user) {
                $oq->where('email', $user->email);
            })->pluck('id')->toArray();
            $myLotIds = array_unique(array_merge($userLotIds, $ownerLotIds));

            $allowed = $news->lots()->whereIn('lots.id', $myLotIds)->exists();
            if (!$allowed) {
                abort(403, 'Este comunicado no está dirigido a su lote.');
            }
        }

        return view('owner.news.show', compact('news'));
    }
}
