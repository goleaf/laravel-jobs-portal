<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all media items for candidates (resumes)
        $mediaItems = DB::table('media')
            ->where('model_type', 'App\\Models\\Candidate')
            ->get();

        foreach ($mediaItems as $media) {
            $candidate = DB::table('candidates')->where('id', $media->model_id)->first();
            
            if (!$candidate) {
                continue;
            }
            
            $mediaPath = $media->id . '/' . $media->file_name;
            $sourcePath = storage_path('app/public/'. $mediaPath);
            
            // Skip if file doesn't exist
            if (!file_exists($sourcePath)) {
                continue;
            }
            
            // Define new path
            $collection = ($media->collection_name === 'candidate_resumes') ? 'candidates/resumes' : 'candidates/images';
            $newPath = $collection . '/' . $media->file_name;
            
            // Copy file to new location
            if (file_exists($sourcePath)) {
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
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
        }
        
        // Get all profile images from users and copy to candidate records
        $userMedia = DB::table('media')
            ->where('model_type', 'App\\Models\\User')
            ->where('collection_name', 'profile')
            ->get();
            
        foreach ($userMedia as $media) {
            $user = DB::table('users')->where('id', $media->model_id)->first();
            
            if (!$user || $user->owner_type !== 'App\\Models\\Candidate') {
                continue;
            }
            
            $candidate = DB::table('candidates')->where('id', $user->owner_id)->first();
            
            if (!$candidate) {
                continue;
            }
            
            $mediaPath = $media->id . '/' . $media->file_name;
            $sourcePath = storage_path('app/public/'. $mediaPath);
            
            // Skip if file doesn't exist
            if (!file_exists($sourcePath)) {
                continue;
            }
            
            // Define new path
            $newPath = 'candidates/images/' . $media->file_name;
            
            // Copy file to new location
            if (file_exists($sourcePath)) {
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
            }
            
            // Update candidate record
            DB::table('candidates')
                ->where('id', $candidate->id)
                ->update(['image_path' => $newPath]);
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