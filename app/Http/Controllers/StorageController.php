<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
        
        // Build possible file paths (for shared hosting compatibility)
        $possiblePaths = [
            // Standard Laravel path
            storage_path('app/public/' . $path),
            // Alternative paths for shared hosting
            base_path('storage/app/public/' . $path),
            public_path('storage/' . $path),
            // Direct path resolution (for shared hosting)
            realpath(storage_path('app/public')) . '/' . $path,
            // If app is in subdirectory
            dirname(base_path()) . '/storage/app/public/' . $path,
        ];
        
        $filePath = null;
        
        // Try each possible path
        foreach ($possiblePaths as $testPath) {
            if (file_exists($testPath) && is_file($testPath)) {
                $filePath = $testPath;
                Log::info('StorageController: Found file', [
                    'path' => $testPath,
                    'requested' => $path,
                ]);
                break;
            }
        }
        
        // Log for debugging
        Log::info('StorageController: Attempting to serve image', [
            'requested_path' => $path,
            'found_file' => $filePath,
            'storage_path' => storage_path('app/public'),
            'base_path' => base_path(),
            'public_path' => public_path(),
            'all_tested_paths' => $possiblePaths,
        ]);
        
        if (!$filePath || !file_exists($filePath)) {
            Log::warning('StorageController: File not found in any location', [
                'requested_path' => $path,
                'tested_paths' => $possiblePaths,
            ]);
            abort(404, 'Image not found: ' . $path);
        }
        
        if (!is_file($filePath)) {
            Log::warning('StorageController: Path is not a file', ['path' => $filePath]);
            abort(404, 'Path is not a file');
        }
        
        // Security: Only allow image files
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            Log::warning('StorageController: File type not allowed', [
                'extension' => $extension,
                'path' => $filePath,
            ]);
            abort(403, 'File type not allowed');
        }
        
        $mimeType = mime_content_type($filePath);
        
        if (!$mimeType) {
            // Fallback mime types
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }
        
        Log::info('StorageController: Serving file', [
            'path' => $filePath,
            'mime_type' => $mimeType,
        ]);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
