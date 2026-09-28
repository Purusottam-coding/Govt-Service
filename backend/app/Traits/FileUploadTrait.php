<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait FileUploadTrait
{
    /**
     * Upload a single file to specified directory and disk.
     */
    public function uploadFile(UploadedFile $file, string $directory = 'uploads', string $disk = 'public'): string
    {
        return $file->store($directory, $disk);
    }

    /**
     * Delete a file from disk if it exists.
     */
    public function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
