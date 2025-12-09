<?php

if (!function_exists('getImageUrl')) {
    /**
     * Get image URL with fallback for hosting servers
     *
     * @param  string|null  $path
     * @return string|null
     */
    function getImageUrl($path)
    {
        if (empty($path)) {
            return null;
        }
        
        // If already a full URL, return as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // Remove leading slash if present
        $path = ltrim($path, '/');
        
        // Check if symlink exists and is valid
        $symlinkPath = public_path('storage');
        if (file_exists($symlinkPath) && is_link($symlinkPath)) {
            // Symlink exists, use asset()
            return asset('storage/' . $path);
        }
        
        // Check if file exists in public/storage (direct access)
        if (file_exists($symlinkPath . '/' . $path)) {
            return asset('storage/' . $path);
        }
        
        // Fallback to route-based URL
        return route('storage.image', ['path' => $path]);
    }
}

