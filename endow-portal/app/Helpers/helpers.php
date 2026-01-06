<?php

use App\Helpers\StorageHelper;

if (!function_exists('storage_url')) {
    /**
     * Get storage URL that works with or without symlink
     * Use this instead of asset('storage/...')
     * 
     * @param string|null $path
     * @return string|null
     */
    function storage_url(?string $path): ?string
    {
        return StorageHelper::url($path);
    }
}
