<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Serve files from storage disk for shared hosting.
     */
    public function show(string $path): BinaryFileResponse
    {
        // Decode path
        $cleanPath = ltrim(urldecode($path), '/');

        // Prevent path traversal
        if (str_contains($cleanPath, '..')) {
            abort(403);
        }

        $filePath = storage_path('app/public/' . $cleanPath);
        if (!file_exists($filePath)) {
            $filePath = storage_path('app/' . $cleanPath);
        }

        if (!file_exists($filePath) || is_dir($filePath)) {
            abort(404, 'File not found on disk: ' . $cleanPath);
        }

        $mime = mime_content_type($filePath) ?: 'application/octet-stream';

        return new BinaryFileResponse($filePath, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
