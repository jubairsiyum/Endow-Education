<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get the URL for a file stored in the public disk
     * This works for shared hosting without symlink
     * 
     * @param string|null $path
     * @return string|null
     */
    public static function url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Check if symlink exists (local development)
        if (file_exists(public_path('storage'))) {
            return asset('storage/' . $path);
        }

        // For shared hosting without symlink, use route to serve files
        return route('storage.serve', ['path' => $path]);
    }

    /**
     * Check if a file exists in storage
     * 
     * @param string|null $path
     * @return bool
     */
    public static function exists(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk('public')->exists($path);
    }

    /**
     * Get file content from storage
     * 
     * @param string $path
     * @return string|null
     */
    public static function get(string $path): ?string
    {
        if (!self::exists($path)) {
            return null;
        }

        return Storage::disk('public')->get($path);
    }

    /**
     * Get MIME type of a file
     * 
     * @param string $path
     * @return string|null
     */
    public static function mimeType(string $path): ?string
    {
        if (!self::exists($path)) {
            return null;
        }

        return Storage::disk('public')->mimeType($path);
    }
}
