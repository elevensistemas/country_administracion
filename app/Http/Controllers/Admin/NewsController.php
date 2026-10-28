<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\Lot;
use App\Models\Notification;
use App\Mail\NewsPublishedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of news.
     */
    public function index(Request $request)
    {
        $query = News::with('lots');

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

        $news = $query->orderByRaw('COALESCE(published_at, publish_date, created_at) DESC')->paginate(10)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    /**
     * Show form to create news.
     */
    public function create()
    {
        $lots = Lot::with('owner')->orderByRaw('CAST(number AS UNSIGNED), number ASC')->get();
        return view('admin.news.create', compact('lots'));
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
            'recipients_type' => 'required|string|in:all,specific_lots',
            'lot_ids' => 'nullable|array',
            'lot_ids.*' => 'exists:lots,id',
            'published_at' => 'nullable|date',
            'file' => 'nullable|file|max:25600',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->input('recipients_type') === 'specific_lots' && empty($request->input('lot_ids'))) {
            return back()->withInput()->withErrors(['lot_ids' => 'Debe seleccionar al menos un lote para el alcance específico.']);
        }

        $data = $request->except(['notify_portal', 'send_email', 'file', 'image', 'lot_ids']);
        $data['user_id'] = auth()->id();
        $data['is_published'] = ($request->status === 'published');
        $data['recipients_type'] = $request->input('recipients_type', 'all');
        
        if ($request->status === 'published') {
            $data['published_at'] = $request->filled('published_at') ? $request->published_at : now();
            $data['publish_date'] = $data['published_at'];
        }

        // Upload attachment file
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('news', 'public');
        }

        // Upload cover image
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('news_images', 'public');
        }

        $news = News::create($data);

        // Sync targeted lots
        if ($news->recipients_type === 'specific_lots' && $request->filled('lot_ids')) {
            $news->lots()->sync($request->input('lot_ids'));
        }

        // Notify owners/residents if published
        if ($news->status === 'published' && $news->visibility === 'public') {
            $this->notifyOwners(
                $news,
                $request->boolean('notify_portal', true),
                $request->boolean('send_email', true)
            );
        }

        $msg = 'Novedad creada correctamente.';
        if ($news->status === 'published' && $request->boolean('send_email', true)) {
            $msg .= ' Se enviaron las notificaciones por portal y correo electrónico a los destinatarios.';
        }

        return redirect()->route('admin.news.index')->with('success', $msg);
    }

    /**
     * Show edit form.
     */
    public function edit(News $news)
    {
        $news->load('lots');
        $lots = Lot::with('owner')->orderByRaw('CAST(number AS UNSIGNED), number ASC')->get();
        $selectedLotIds = $news->lots->pluck('id')->toArray();
        return view('admin.news.edit', compact('news', 'lots', 'selectedLotIds'));
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
            'recipients_type' => 'required|string|in:all,specific_lots',
            'lot_ids' => 'nullable|array',
            'lot_ids.*' => 'exists:lots,id',
            'published_at' => 'nullable|date',
            'file' => 'nullable|file|max:25600',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->input('recipients_type') === 'specific_lots' && empty($request->input('lot_ids'))) {
            return back()->withInput()->withErrors(['lot_ids' => 'Debe seleccionar al menos un lote para el alcance específico.']);
        }

        $wasPublished = ($news->status === 'published');

        $data = $request->except(['notify_portal', 'send_email', 'file', 'image', 'remove_file', 'remove_image', 'lot_ids']);
        $data['is_published'] = ($request->status === 'published');
        $data['recipients_type'] = $request->input('recipients_type', 'all');
        
        if ($request->status === 'published') {
            if (!$news->published_at) {
                $data['published_at'] = $request->filled('published_at') ? $request->published_at : now();
                $data['publish_date'] = $data['published_at'];
            }
        }

        // Handle attachment file
        if ($request->boolean('remove_file')) {
            if ($news->file_path && Storage::disk('public')->exists($news->file_path)) {
                Storage::disk('public')->delete($news->file_path);
            }
            $data['file_path'] = null;
        } elseif ($request->hasFile('file')) {
            if ($news->file_path && Storage::disk('public')->exists($news->file_path)) {
                Storage::disk('public')->delete($news->file_path);
            }
            $data['file_path'] = $request->file('file')->store('news', 'public');
        }

        // Handle cover image
        if ($request->boolean('remove_image')) {
            if ($news->image_path && Storage::disk('public')->exists($news->image_path)) {
                Storage::disk('public')->delete($news->image_path);
            }
            $data['image_path'] = null;
        } elseif ($request->hasFile('image')) {
            if ($news->image_path && Storage::disk('public')->exists($news->image_path)) {
                Storage::disk('public')->delete($news->image_path);
            }
            $data['image_path'] = $request->file('image')->store('news_images', 'public');
        }

        $news->update($data);

        // Sync targeted lots
        if ($news->recipients_type === 'specific_lots' && $request->filled('lot_ids')) {
            $news->lots()->sync($request->input('lot_ids'));
        } else {
            $news->lots()->detach();
        }

        // Send notifications if newly published or explicitly requested
        if ($news->status === 'published' && $news->visibility === 'public') {
            if (!$wasPublished || $request->boolean('send_email') || $request->boolean('notify_portal')) {
                $this->notifyOwners(
                    $news,
                    $request->boolean('notify_portal', false),
                    $request->boolean('send_email', false)
                );
            }
        }

        return redirect()->route('admin.news.index')->with('success', 'Novedad actualizada correctamente.');
    }

    /**
     * Remove news from system.
     */
    public function destroy(News $news)
    {
        if ($news->file_path && Storage::disk('public')->exists($news->file_path)) {
            Storage::disk('public')->delete($news->file_path);
        }
        if ($news->image_path && Storage::disk('public')->exists($news->image_path)) {
            Storage::disk('public')->delete($news->image_path);
        }

        $news->lots()->detach();
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'Novedad eliminada correctamente.');
    }

    /**
     * Helper to dispatch in-app notifications and emails to owners/residents.
     */
    private function notifyOwners(News $news, bool $notifyPortal, bool $sendEmail): void
    {
        if (!$notifyPortal && !$sendEmail) {
            return;
        }

        // Target all owner, tenant, and board users
        $query = User::where(function ($q) {
                $q->whereIn('relationship_type', ['owner', 'tenant', 'board'])
                  ->orWhereHas('roles', function ($rq) {
                      $rq->whereIn('name', ['owner', 'tenant', 'board']);
                  });
            })
            ->where('status', 'active');

        // Filter by targeted lots if specific
        if ($news->recipients_type === 'specific_lots') {
            $targetLotIds = $news->lots()->pluck('lots.id')->toArray();
            if (!empty($targetLotIds)) {
                $query->where(function ($uq) use ($targetLotIds) {
                    $uq->whereHas('functionalUnits', function ($fuq) use ($targetLotIds) {
                        $fuq->whereIn('lot_id', $targetLotIds);
                    })
                    ->orWhereIn('email', function ($eq) use ($targetLotIds) {
                        $eq->select('email')
                           ->from('owners')
                           ->join('lots', 'lots.current_owner_id', '=', 'owners.id')
                           ->whereIn('lots.id', $targetLotIds)
                           ->whereNotNull('email');
                    });
                });
            }
        }

        $recipients = $query->get();

        foreach ($recipients as $user) {
            // 1. In-App Notification (Campanita)
            if ($notifyPortal) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Nueva Novedad: ' . $news->title,
                    'message' => Str::limit($news->summary ?: strip_tags($news->content), 120),
                    'type' => 'news',
                    'link' => route('owner.news.show', $news->id),
                ]);
            }

            // 2. Email Notification
            if ($sendEmail && !empty($user->email)) {
                try {
                    Mail::to($user->email)->send(new NewsPublishedMail($news, $user));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("No se pudo enviar email de novedad a {$user->email}: " . $e->getMessage());
                }
            }
        }
    }
}
