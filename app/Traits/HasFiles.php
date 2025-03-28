<?php

namespace App\Traits;

use App\Models\File;
use App\Services\FileUploadService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

trait HasFiles
{
    /**
     * Get all files.
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    /**
     * Get files from a specific collection.
     *
     * @param string $collectionName
     * @return Collection
     */
    public function getFiles(string $collectionName = 'default'): Collection
    {
        return $this->files()->where('collection_name', $collectionName)->orderBy('order_column')->get();
    }

    /**
     * Get the first file from a collection.
     *
     * @param string $collectionName
     * @return File|null
     */
    public function getFirstFile(string $collectionName = 'default'): ?File
    {
        return $this->files()->where('collection_name', $collectionName)->orderBy('order_column')->first();
    }

    /**
     * Get the URL of the first file.
     *
     * @param string $collectionName
     * @return string|null
     */
    public function getFirstFileUrl(string $collectionName = 'default'): ?string
    {
        $file = $this->getFirstFile($collectionName);
        return $file ? $file->getUrl() : null;
    }

    /**
     * Add a file to the model.
     *
     * @param UploadedFile $file
     * @param string $collectionName
     * @param array $customProperties
     * @return File
     */
    public function addFile(UploadedFile $file, string $collectionName = 'default', array $customProperties = []): File
    {
        $fileUploadService = app(FileUploadService::class);
        $path = $fileUploadService->upload($file, 'uploads/' . $collectionName);
        
        $fileModel = new File([
            'collection_name' => $collectionName,
            'name' => $file->getClientOriginalName(),
            'file_name' => basename($path),
            'mime_type' => $file->getMimeType(),
            'disk' => 'public',
            'path' => $path,
            'size' => $file->getSize(),
            'custom_properties' => $customProperties,
            'order_column' => $this->files()->where('collection_name', $collectionName)->max('order_column') + 1,
        ]);
        
        $this->files()->save($fileModel);
        
        return $fileModel;
    }
    
    /**
     * Add a file from a path.
     *
     * @param string $path
     * @param string $collectionName
     * @param array $customProperties
     * @return File
     */
    public function addFileFromPath(string $path, string $collectionName = 'default', array $customProperties = []): File
    {
        $fileUploadService = app(FileUploadService::class);
        
        if (!$fileUploadService->exists($path)) {
            throw new \Exception("File does not exist at path: {$path}");
        }
        
        $fileModel = new File([
            'collection_name' => $collectionName,
            'name' => basename($path),
            'file_name' => basename($path),
            'mime_type' => mime_content_type($path),
            'disk' => 'public',
            'path' => $path,
            'size' => $fileUploadService->getSize($path),
            'custom_properties' => $customProperties,
            'order_column' => $this->files()->where('collection_name', $collectionName)->max('order_column') + 1,
        ]);
        
        $this->files()->save($fileModel);
        
        return $fileModel;
    }

    /**
     * Delete all files in a collection.
     *
     * @param string $collectionName
     * @return void
     */
    public function clearFiles(string $collectionName = 'default'): void
    {
        $fileUploadService = app(FileUploadService::class);
        
        $this->getFiles($collectionName)->each(function (File $file) use ($fileUploadService) {
            $fileUploadService->delete($file->path);
            $file->delete();
        });
    }

    /**
     * Delete a specific file.
     *
     * @param File $file
     * @return void
     */
    public function deleteFile(File $file): void
    {
        $fileUploadService = app(FileUploadService::class);
        $fileUploadService->delete($file->path);
        $file->delete();
    }
} 