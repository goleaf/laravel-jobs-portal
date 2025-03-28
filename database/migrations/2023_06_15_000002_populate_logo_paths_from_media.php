<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            if (!$company->user) {
                continue;
            }
            
            $media = Media::where('model_type', User::class)
                ->where('model_id', $company->user->id)
                ->where('collection_name', User::PROFILE)
                ->first();
            
            if (!$media) {
                continue;
            }
            
            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                $newPath = 'companies/logos/' . $media->file_name;
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
                
                $company->update(['logo_path' => $newPath]);
                echo "Migrated logo for company #{$company->id}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration cannot be reversed without data loss
    }
}; 