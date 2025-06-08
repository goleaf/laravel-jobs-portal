<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set unlimited memory and time for this migration
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        
        Log::info('Starting optimized candidate paths migration');

        // Only proceed if media table exists
        if (!Schema::hasTable('media')) {
            Log::info('Media table does not exist, skipping migration');
            return;
        }

        try {
            // Process candidate media in chunks to avoid memory exhaustion
            $this->processMediaInChunks();
            
            // Process user profile images in chunks
            $this->processUserMediaInChunks();
            
            Log::info('Completed optimized candidate paths migration');
        } catch (Exception $e) {
            Log::error('Migration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processMediaInChunks(): void
    {
        $chunkSize = 50; // Process 50 records at a time
        $offset = 0;
        
        do {
            $mediaItems = DB::table('media')
                ->where('model_type', 'App\\Models\\Candidate')
                ->limit($chunkSize)
                ->offset($offset)
                ->get();

            if ($mediaItems->isEmpty()) {
                break;
            }

            foreach ($mediaItems as $media) {
                $this->processMediaItem($media);
            }

            $offset += $chunkSize;
            
            // Force garbage collection to free memory
            gc_collect_cycles();
            
        } while ($mediaItems->count() === $chunkSize);
    }

    private function processUserMediaInChunks(): void
    {
        $chunkSize = 50; // Process 50 records at a time
        $offset = 0;
        
        do {
            $userMedia = DB::table('media')
                ->where('model_type', 'App\\Models\\User')
                ->where('collection_name', 'profile')
                ->limit($chunkSize)
                ->offset($offset)
                ->get();

            if ($userMedia->isEmpty()) {
                break;
            }

            foreach ($userMedia as $media) {
                $this->processUserMediaItem($media);
            }

            $offset += $chunkSize;
            
            // Force garbage collection to free memory
            gc_collect_cycles();
            
        } while ($userMedia->count() === $chunkSize);
    }

    private function processMediaItem($media): void
    {
        try {
            $candidate = DB::table('candidates')->where('id', $media->model_id)->first();

            if (!$candidate) {
                return;
            }

            $mediaPath = $media->id.'/'.$media->file_name;
            $sourcePath = storage_path('app/public/'.$mediaPath);

            // Skip if file doesn't exist
            if (!file_exists($sourcePath)) {
                return;
            }

            // Define new path
            $collection = ($media->collection_name === 'candidate_resumes') ? 'candidates/resumes' : 'candidates/images';
            $newPath = $collection.'/'.$media->file_name;

            // Use Storage::copy instead of file_get_contents for better memory management
            if (Storage::disk('public')->exists($mediaPath)) {
                Storage::disk('public')->copy($mediaPath, $newPath);
            }

            // Update candidate record
            if ($media->collection_name === 'candidate_resumes') {
                DB::table('candidates')
                    ->where('id', $candidate->id)
                    ->update(['resume_path' => $newPath]);
            } else {
                DB::table('candidates')
                    ->where('id', $candidate->id)
                    ->update(['image_path' => $newPath]);
            }
        } catch (Exception $e) {
            Log::warning('Failed to process media item ID: ' . $media->id . ' - ' . $e->getMessage());
        }
    }

    private function processUserMediaItem($media): void
    {
        try {
            $user = DB::table('users')->where('id', $media->model_id)->first();

            if (!$user || $user->owner_type !== 'App\\Models\\Candidate') {
                return;
            }

            $candidate = DB::table('candidates')->where('id', $user->owner_id)->first();

            if (!$candidate) {
                return;
            }

            $mediaPath = $media->id.'/'.$media->file_name;
            $sourcePath = storage_path('app/public/'.$mediaPath);

            // Skip if file doesn't exist
            if (!file_exists($sourcePath)) {
                return;
            }

            // Define new path
            $newPath = 'candidates/images/'.$media->file_name;

            // Use Storage::copy instead of file_get_contents for better memory management
            if (Storage::disk('public')->exists($mediaPath)) {
                Storage::disk('public')->copy($mediaPath, $newPath);
            }

            // Update candidate record
            DB::table('candidates')
                ->where('id', $candidate->id)
                ->update(['image_path' => $newPath]);
        } catch (Exception $e) {
            Log::warning('Failed to process user media item ID: ' . $media->id . ' - ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed as this is a data migration
    }
}; 