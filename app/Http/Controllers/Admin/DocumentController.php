<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents.
     */
    public function index(Request $request)
    {
        $query = Document::with(['category', 'versions.uploader']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->input('visibility'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'archived') {
                $query->where('is_archived', true);
            } elseif ($request->input('status') === 'active') {
                $query->where('is_archived', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('name')->paginate(10)->withQueryString();
        $categories = DocumentCategory::orderBy('display_name')->get();

        return view('admin.documents.index', compact('documents', 'categories'));
    }

    /**
     * Store new document with version 1.0.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:document_categories,id',
            'description' => 'nullable|string',
            'visibility' => 'required|string|in:public,owners_only,board_only,internal',
            'version' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'file' => 'required|file|max:25600', // 25MB limit
        ]);

        DB::transaction(function () use ($request) {
            $doc = Document::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'visibility' => $request->visibility,
                'is_archived' => false,
            ]);

            // Save file version
            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            DocumentVersion::create([
                'document_id' => $doc->id,
                'version' => $request->input('version', '1.0'),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
                'notes' => $request->input('notes', 'Versión inicial cargada administrativamente.'),
            ]);
        });

        return redirect()->route('admin.documents.index')->with('success', 'Documento registrado y publicado correctamente.');
    }

    /**
     * Store new version of existing document.
     */
    public function storeVersion(Request $request, Document $document)
    {
        $request->validate([
            'version' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
            'file' => 'required|file|max:25600',
        ]);

        DB::transaction(function () use ($request, $document) {
            $lastVersion = $document->versions()->orderBy('id', 'desc')->first();
            $versionStr = $request->input('version');
            if (empty($versionStr)) {
                $lastNum = $lastVersion ? floatval($lastVersion->version) : 1.0;
                $versionStr = number_format($lastNum + 0.1, 1);
            }

            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            DocumentVersion::create([
                'document_id' => $document->id,
                'version' => $versionStr,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
                'notes' => $request->input('notes', 'Nueva versión cargada administrativamente.'),
            ]);
        });

        return redirect()->route('admin.documents.index')->with('success', 'Nueva versión de documento cargada correctamente.');
    }

    /**
     * Download document version.
     */
    public function downloadVersion(DocumentVersion $version)
    {
        $path = storage_path('app/public/' . $version->file_path);
        
        if (!file_exists($path)) {
            $path = public_path('storage/' . $version->file_path);
        }

        if (!file_exists($path)) {
            $path = storage_path('app/' . $version->file_path);
        }

        if (!file_exists($path)) {
            abort(404, 'El archivo solicitado no existe en el disco.');
        }

        return response()->download($path, $version->file_name);
    }

    /**
     * Archive document.
     */
    public function archive(Document $document)
    {
        $document->update(['is_archived' => true]);
        return redirect()->route('admin.documents.index')->with('success', 'Documento archivado correctamente.');
    }

    /**
     * Unarchive document.
     */
    public function unarchive(Document $document)
    {
        $document->update(['is_archived' => false]);
        return redirect()->route('admin.documents.index')->with('success', 'Documento restaurado.');
    }

    /**
     * Delete document.
     */
    public function destroy(Document $document)
    {
        foreach ($document->versions as $version) {
            if ($version->file_path && Storage::disk('public')->exists($version->file_path)) {
                Storage::disk('public')->delete($version->file_path);
            }
        }
        $document->delete();
        return redirect()->route('admin.documents.index')->with('success', 'Documento eliminado.');
    }
}
