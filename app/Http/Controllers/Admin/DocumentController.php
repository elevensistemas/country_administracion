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
        $query = Document::with(['category', 'versions.user']);

        if ($request->filled('category_id')) {
            $query->where('document_category_id', $request->input('category_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('title')->paginate(10)->withQueryString();

        $categories = DocumentCategory::all();

        return view('admin.documents.index', compact('documents', 'categories'));
    }

    /**
     * Store new document with version 1.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:documents,code',
            'title' => 'required|string|max:255',
            'document_category_id' => 'required|exists:document_categories,id',
            'description' => 'nullable|string',
            'is_public' => 'required|boolean',
            'file' => 'required|file|max:10240', // 10MB limit
        ]);

        DB::transaction(function () use ($request) {
            $doc = Document::create([
                'document_category_id' => $request->document_category_id,
                'code' => $request->code,
                'title' => $request->title,
                'description' => $request->description,
                'is_public' => $request->is_public,
                'status' => 'active',
                'current_version' => 1,
            ]);

            // Save file version
            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            DocumentVersion::create([
                'document_id' => $doc->id,
                'version_number' => 1,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'user_id' => auth()->id(),
                'change_log' => 'Versión inicial cargada administrativamente.',
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
            'file' => 'required|file|max:10240',
            'change_log' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $document) {
            $nextVersionNum = $document->current_version + 1;

            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $nextVersionNum,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'user_id' => auth()->id(),
                'change_log' => $request->change_log,
            ]);

            $document->update([
                'current_version' => $nextVersionNum,
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
            abort(404, 'El archivo solicitado no existe.');
        }

        return response()->download($path, $version->file_name);
    }

    /**
     * Archive document.
     */
    public function archive(Document $document)
    {
        $document->update(['status' => 'archived']);
        return redirect()->route('admin.documents.index')->with('success', 'Documento archivado.');
    }
}
