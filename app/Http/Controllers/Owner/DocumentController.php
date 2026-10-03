<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of public documents.
     */
    public function index(Request $request)
    {
        $query = Document::where('is_archived', false)
            ->whereIn('visibility', ['public', 'owners_only'])
            ->with(['category', 'versions']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('owner.documents.index', compact('documents'));
    }

    /**
     * Download public document version.
     */
    public function downloadVersion(DocumentVersion $version)
    {
        $doc = $version->document;
        if ($doc->is_archived || !in_array($doc->visibility, ['public', 'owners_only'])) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        $path = storage_path('app/public/' . $version->file_path);
        
        if (!file_exists($path)) {
            $path = public_path('storage/' . $version->file_path);
        }

        if (!file_exists($path)) {
            abort(404, 'El archivo solicitado no existe.');
        }

        return response()->download($path, $version->file_name);
    }
}
