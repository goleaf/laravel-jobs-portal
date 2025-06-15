<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Upload a file to storage.
     */
    public function uploadFile(UploadedFile $file, string $folder, string $disk = 'public', ?string $filename = null): string
    {
        $filename = $filename ?? Str::random(40).'.'.$file->getClientOriginalExtension();

        return $file->storeAs(
            $folder,
            $filename,
            $disk
        );
    }

    /**
     * Get the URL for a file.
     */
    public function getFileUrl(?string $path, string $disk = 'public'): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Delete a file from storage.
     */
    public function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }
}
