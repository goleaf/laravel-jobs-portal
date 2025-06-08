# Migration from Spatie Media Library to Laravel File Handling

This document outlines the steps needed to migrate from the Spatie Media Library package to native Laravel file handling.

## 1. Database Migration

Create a new migration to add a file path column to each model that currently uses the Media Library:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // For User model
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image_path')->nullable();
        });

        // For Post model
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For JobCategory model
        Schema::table('job_categories', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For Company model
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo_path')->nullable();
        });

        // For Candidate model
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('resume_path')->nullable();
        });

        // For Testimonial model
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For FrontSetting model
        Schema::table('front_settings', function (Blueprint $table) {
            $table->string('header_logo_path')->nullable();
            $table->string('footer_logo_path')->nullable();
        });

        // For HeaderSlider model
        Schema::table('header_sliders', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For BrandingSliders model
        Schema::table('branding_sliders', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For ImageSlider model
        Schema::table('image_sliders', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For CmsServices model
        Schema::table('cms_services', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });
    }

    public function down()
    {
        // For User model
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_image_path');
        });

        // For Post model
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For JobCategory model
        Schema::table('job_categories', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For Company model
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });

        // For Candidate model
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('resume_path');
        });

        // For Testimonial model
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For FrontSetting model
        Schema::table('front_settings', function (Blueprint $table) {
            $table->dropColumn('header_logo_path');
            $table->dropColumn('footer_logo_path');
        });

        // For HeaderSlider model
        Schema::table('header_sliders', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For BrandingSliders model
        Schema::table('branding_sliders', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For ImageSlider model
        Schema::table('image_sliders', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For CmsServices model
        Schema::table('cms_services', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
```

## 2. Create a File Service

Create a new service class to handle file uploads:

```php
<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Upload a file to storage.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $disk
     * @param string|null $filename
     * @return string
     */
    public function uploadFile(UploadedFile $file, string $folder, string $disk = 'public', ?string $filename = null): string
    {
        $filename = $filename ?? Str::random(40) . '.' . $file->getClientOriginalExtension();
        
        return $file->storeAs(
            $folder,
            $filename,
            $disk
        );
    }

    /**
     * Get the URL for a file.
     *
     * @param string|null $path
     * @param string $disk
     * @return string|null
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
     *
     * @param string|null $path
     * @param string $disk
     * @return bool
     */
    public function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }
}
```

## 3. Update Models

Remove HasMedia and InteractsWithMedia traits, and implement file handling methods:

Example for User model:

```php
<?php

namespace App\Models;

use App\Services\FileService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class User extends Model
{
    protected $fillable = [
        // ... existing fillable fields
        'profile_image_path',
    ];

    /**
     * Upload a profile image for the user.
     *
     * @param UploadedFile $file
     * @return void
     */
    public function uploadProfileImage(UploadedFile $file): void
    {
        // Delete old file if exists
        if ($this->profile_image_path) {
            app(FileService::class)->deleteFile($this->profile_image_path);
        }

        $path = app(FileService::class)->uploadFile(
            $file,
            'users/profile_images',
            'public'
        );

        $this->update(['profile_image_path' => $path]);
    }

    /**
     * Get the profile image URL.
     *
     * @return string|null
     */
    public function getProfileImageUrl(): ?string
    {
        if (empty($this->profile_image_path)) {
            return asset('assets/img/infyom-logo.png');
        }

        return app(FileService::class)->getFileUrl($this->profile_image_path);
    }
}
```

## 4. Update Controllers

Update controllers to use the new file handling methods:

```php
public function update(Request $request, User $user)
{
    $data = $request->validated();

    if ($request->hasFile('profile_image')) {
        $user->uploadProfileImage($request->file('profile_image'));
    }

    $user->update($data);

    return redirect()->route('users.show', $user)
        ->with('success', 'User updated successfully.');
}
```

## 5. Update Blade Templates

Replace all Media Library related references in Blade templates:

From:
```blade
<img src="{{ (!empty($user->media[0]))? $user->media[0]->getFullUrl() : asset('assets/img/infyom-logo.png') }}" alt="Profile Image">
```

To:
```blade
<img src="{{ $user->getProfileImageUrl() }}" alt="Profile Image">
```

## 6. Data Migration

Create an Artisan command to migrate existing media to the new file structure:

```php
<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Post;
use App\Services\FileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MigrateMediaLibrary extends Command
{
    protected $signature = 'media:migrate';

    protected $description = 'Migrate media from Spatie Media Library to native Laravel storage';

    public function handle()
    {
        $this->info('Starting media migration...');

        $this->migrateUserMedia();
        $this->migratePostMedia();
        // Add more model migrations...

        $this->info('Media migration completed!');
    }

    private function migrateUserMedia()
    {
        $this->info('Migrating user media...');
        $mediaItems = Media::where('model_type', User::class)->get();

        foreach ($mediaItems as $media) {
            $user = User::find($media->model_id);

            if (!$user) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                $newPath = 'users/profile_images/' . $media->file_name;
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
                
                $user->update(['profile_image_path' => $newPath]);
                $this->info("Migrated media for user #{$user->id}");
            }
        }
    }

    private function migratePostMedia()
    {
        // Similar implementation for Post model
    }
}
```

## 7. Clean Up

After migrating all data, you can:

1. Remove all Media Library related code
2. Remove the Media Library tables from the database
3. Remove the custom path generator and other Media Library configurations 