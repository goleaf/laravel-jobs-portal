<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Check if companies table exists and has required columns
        if (!Schema::hasTable('companies')) {
            return;
        }

        if (!Schema::hasColumn('companies', 'deleted_at')) {
            return;
        }

        if (!Schema::hasColumn('companies', 'logo_path')) {
            return;
        }

        try {
            $companies = Company::all();

            foreach ($companies as $company) {
                if (!$company->user) {
                    continue;
                }

                $media = Media::where('model_type', User::class)
                    ->where('model_id', $company->user->id)
                    ->where('collection_name', User::PROFILE)
                    ->first()
                ;

                if (!$media) {
                    continue;
                }

                $sourcePath = $media->getPath();

                if (file_exists($sourcePath)) {
                    $newPath = 'companies/logos/'.$media->file_name;

                    Storage::disk('public')->put(
                        $newPath,
                        file_get_contents($sourcePath)
                    );

                    $company->update(['logo_path' => $newPath]);
                    echo "Migrated logo for company #{$company->id}\n";
                }
            }
        } catch (Exception $e) {
            // Silently fail if there are any issues during migration
            // This is a data migration that can be run manually if needed
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // This migration cannot be reversed without data loss
    }
};
