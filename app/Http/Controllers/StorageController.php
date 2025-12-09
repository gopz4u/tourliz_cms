<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Serve images from storage when symlink doesn't exist
     *
     * @param  string  $path
     * @return \Illuminate\Http\Response
     */
    public function image($path)
    {
        // Security: Prevent directory traversal
        $path = str_replace('..', '', $path);
        $path = ltrim($path, '/');
        
        $filePath = storage_path('app/public/' . $path);
        
        if (!file_exists($filePath) || !is_file($filePath)) {
            abort(404);
        }
        
        // Security: Only allow image files
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            abort(403, 'File type not allowed');
        }
        
        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}

