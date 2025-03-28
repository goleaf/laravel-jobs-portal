<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload a file to the specified disk and path.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string $disk
     * @return string The file path
     */
    public function upload(UploadedFile $file, string $path = 'uploads', string $disk = 'public'): string
    {
        $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $filePath = $path . '/' . $fileName;
        
        Storage::disk($disk)->put($filePath, file_get_contents($file));
        
        return $filePath;
    }
    
    /**
     * Get the URL for a file
     *
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function getUrl(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }
    
    /**
     * Delete a file
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        
        return false;
    }
    
    /**
     * Move a file from one location to another
     *
     * @param string $oldPath
     * @param string $newPath
     * @param string $disk
     * @return bool
     */
    public function move(string $oldPath, string $newPath, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($oldPath)) {
            $contents = Storage::disk($disk)->get($oldPath);
            if (Storage::disk($disk)->put($newPath, $contents)) {
                return Storage::disk($disk)->delete($oldPath);
            }
        }
        
        return false;
    }
    
    /**
     * Copy a file from one location to another
     *
     * @param string $oldPath
     * @param string $newPath
     * @param string $disk
     * @return bool
     */
    public function copy(string $oldPath, string $newPath, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($oldPath)) {
            $contents = Storage::disk($disk)->get($oldPath);
            return Storage::disk($disk)->put($newPath, $contents);
        }
        
        return false;
    }
    
    /**
     * Get the file size
     *
     * @param string $path
     * @param string $disk
     * @return int
     */
    public function getSize(string $path, string $disk = 'public'): int
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->size($path);
        }
        
        return 0;
    }
    
    /**
     * Check if a file exists
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }
} 