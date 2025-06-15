<?php

use App\Models\Candidate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only proceed if media table exists (it was dropped in a later migration)
        if (!Schema::hasTable('media')) {
            return;
        }

        // Get all media items for candidates (resumes)
        $mediaItems = DB::table('media')
            ->where('model_type', 'App\Models\Candidate')
            ->get()
        ;

        foreach ($mediaItems as $media) {
            $candidate = DB::table('candidates')->where('id', $media->model_id)->first();

            if (!$candidate) {
                continue;
            }

            $mediaPath = $media->id.'/'.$media->file_name;
            $sourcePath = storage_path('app/public/'.$mediaPath);

            // Skip if file doesn't exist
            if (!file_exists($sourcePath)) {
                continue;
            }

            // Define new path
            $collection = ('candidate_resumes' === $media->collection_name) ? 'candidates/resumes' : 'candidates/images';
            $newPath = $collection.'/'.$media->file_name;

            // Copy file to new location
            if (file_exists($sourcePath)) {
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
            }

            // Update candidate record
            if ('candidate_resumes' === $media->collection_name) {
                DB::table('candidates')
                    ->where('id', $candidate->id)
                    ->update(['resume_path' => $newPath])
                ;
            } else {
                DB::table('candidates')
                    ->where('id', $candidate->id)
                    ->update(['image_path' => $newPath])
                ;
            }
        }

        // Get all profile images from users and copy to candidate records
        $userMedia = DB::table('media')
            ->where('model_type', 'App\Models\User')
            ->where('collection_name', 'profile')
            ->get()
        ;

        foreach ($userMedia as $media) {
            $user = DB::table('users')->where('id', $media->model_id)->first();

            if (!$user || 'App\Models\Candidate' !== $user->owner_type) {
                continue;
            }

            $candidate = DB::table('candidates')->where('id', $user->owner_id)->first();

            if (!$candidate) {
                continue;
            }

            $mediaPath = $media->id.'/'.$media->file_name;
            $sourcePath = storage_path('app/public/'.$mediaPath);

            // Skip if file doesn't exist
            if (!file_exists($sourcePath)) {
                continue;
            }

            // Define new path
            $newPath = 'candidates/images/'.$media->file_name;

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
                ->update(['image_path' => $newPath])
            ;
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
