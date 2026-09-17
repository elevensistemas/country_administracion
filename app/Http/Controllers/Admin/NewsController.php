<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\Notification;
use App\Mail\NewsPublishedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        $data = $request->except(['notify_portal', 'send_email']);
        $data['user_id'] = auth()->id();
        $data['is_published'] = ($request->status === 'published');
        
        if ($request->status === 'published') {
            $data['published_at'] = $request->filled('published_at') ? $request->published_at : now();
            $data['publish_date'] = $data['published_at'];
        }

        $news = News::create($data);

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
            $msg .= ' Se enviaron las notificaciones por portal y correo electrónico a los propietarios.';
        }

        return redirect()->route('admin.news.index')->with('success', $msg);
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

        $wasPublished = ($news->status === 'published');

        $data = $request->except(['notify_portal', 'send_email']);
        $data['is_published'] = ($request->status === 'published');
        
        if ($request->status === 'published') {
            if (!$news->published_at) {
                $data['published_at'] = $request->filled('published_at') ? $request->published_at : now();
                $data['publish_date'] = $data['published_at'];
            }
        }

        $news->update($data);

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
        $recipients = User::where(function ($q) {
                $q->whereIn('relationship_type', ['owner', 'tenant', 'board'])
                  ->orWhereHas('roles', function ($rq) {
                      $rq->whereIn('name', ['owner', 'tenant', 'board']);
                  });
            })
            ->where('status', 'active')
            ->get();

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
