<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Post;
use App\Models\Company;
use App\Models\Candidate;
use App\Models\JobCategory;
use App\Models\Testimonial;
use App\Models\FrontSetting;
use App\Models\HeaderSlider;
use App\Models\BrandingSlider;
use App\Models\ImageSlider;
use App\Models\CmsServices;
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
        $this->migrateCompanyMedia();
        $this->migrateCandidateMedia();
        $this->migrateJobCategoryMedia();
        $this->migratePostMedia();
        $this->migrateTestimonialMedia();
        $this->migrateFrontSettingMedia();
        $this->migrateHeaderSliderMedia();
        $this->migrateBrandingSliderMedia();
        $this->migrateImageSliderMedia();
        $this->migrateCmsServicesMedia();

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

    private function migrateCompanyMedia()
    {
        $this->info('Migrating company media...');
        $mediaItems = Media::where('model_type', Company::class)->get();

        foreach ($mediaItems as $media) {
            $company = Company::find($media->model_id);

            if (!$company) {
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
                $this->info("Migrated logo for company #{$company->id}");
            }
        }
    }

    private function migrateCandidateMedia()
    {
        $this->info('Migrating candidate media...');
        $mediaItems = Media::where('model_type', Candidate::class)->get();

        foreach ($mediaItems as $media) {
            $candidate = Candidate::find($media->model_id);

            if (!$candidate) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                // Check the collection name to determine if it's a resume or image
                if ($media->collection_name === 'resumes') {
                    $newPath = 'candidates/resumes/' . $media->file_name;
                    $candidate->update(['resume_path' => $newPath]);
                    $this->info("Migrated resume for candidate #{$candidate->id}");
                } else {
                    $newPath = 'candidates/images/' . $media->file_name;
                    $candidate->update(['image_path' => $newPath]);
                    $this->info("Migrated image for candidate #{$candidate->id}");
                }
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
            }
        }
    }
    
    private function migrateJobCategoryMedia()
    {
        $this->info('Migrating job category media...');
        $mediaItems = Media::where('model_type', JobCategory::class)->get();

        foreach ($mediaItems as $media) {
            $category = JobCategory::find($media->model_id);

            if (!$category) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                $newPath = 'job_categories/images/' . $media->file_name;
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
                
                $category->update(['image_path' => $newPath]);
                $this->info("Migrated image for job category #{$category->id}");
            }
        }
    }

    private function migratePostMedia()
    {
        $this->info('Migrating post media...');
        $mediaItems = Media::where('model_type', Post::class)->get();

        foreach ($mediaItems as $media) {
            $post = Post::find($media->model_id);

            if (!$post) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                $newPath = 'posts/images/' . $media->file_name;
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
                
                $post->update(['image_path' => $newPath]);
                $this->info("Migrated image for post #{$post->id}");
            }
        }
    }

    private function migrateTestimonialMedia()
    {
        $this->info('Migrating testimonial media...');
        $mediaItems = Media::where('model_type', Testimonial::class)->get();

        foreach ($mediaItems as $media) {
            $testimonial = Testimonial::find($media->model_id);

            if (!$testimonial) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                $newPath = 'testimonials/images/' . $media->file_name;
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
                
                $testimonial->update(['image_path' => $newPath]);
                $this->info("Migrated image for testimonial #{$testimonial->id}");
            }
        }
    }

    private function migrateFrontSettingMedia()
    {
        $this->info('Migrating front settings media...');
        $mediaItems = Media::where('model_type', FrontSetting::class)->get();

        foreach ($mediaItems as $media) {
            $setting = FrontSetting::find($media->model_id);

            if (!$setting) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                // Check the collection name to determine if it's header or footer logo
                if ($media->collection_name === 'header_logo') {
                    $newPath = 'front_settings/header_logo/' . $media->file_name;
                    $setting->update(['header_logo_path' => $newPath]);
                    $this->info("Migrated header logo for front setting #{$setting->id}");
                } else {
                    $newPath = 'front_settings/footer_logo/' . $media->file_name;
                    $setting->update(['footer_logo_path' => $newPath]);
                    $this->info("Migrated footer logo for front setting #{$setting->id}");
                }
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
            }
        }
    }

    private function migrateHeaderSliderMedia()
    {
        $this->info('Migrating header slider media...');
        $mediaItems = Media::where('model_type', HeaderSlider::class)->get();

        foreach ($mediaItems as $media) {
            $slider = HeaderSlider::find($media->model_id);

            if (!$slider) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                $newPath = 'header_sliders/images/' . $media->file_name;
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
                
                $slider->update(['image_path' => $newPath]);
                $this->info("Migrated image for header slider #{$slider->id}");
            }
        }
    }

    private function migrateBrandingSliderMedia()
    {
        $this->info('Migrating branding slider media...');
        $mediaItems = Media::where('model_type', BrandingSlider::class)->get();

        foreach ($mediaItems as $media) {
            $slider = BrandingSlider::find($media->model_id);

            if (!$slider) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                $newPath = 'branding_sliders/images/' . $media->file_name;
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
                
                $slider->update(['image_path' => $newPath]);
                $this->info("Migrated image for branding slider #{$slider->id}");
            }
        }
    }

    private function migrateImageSliderMedia()
    {
        $this->info('Migrating image slider media...');
        $mediaItems = Media::where('model_type', ImageSlider::class)->get();

        foreach ($mediaItems as $media) {
            $slider = ImageSlider::find($media->model_id);

            if (!$slider) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                $newPath = 'image_sliders/images/' . $media->file_name;
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
                
                $slider->update(['image_path' => $newPath]);
                $this->info("Migrated image for image slider #{$slider->id}");
            }
        }
    }

    private function migrateCmsServicesMedia()
    {
        $this->info('Migrating CMS services media...');
        $mediaItems = Media::where('model_type', CmsServices::class)->get();

        foreach ($mediaItems as $media) {
            $service = CmsServices::find($media->model_id);

            if (!$service) {
                continue;
            }

            $sourcePath = $media->getPath();
            
            if (file_exists($sourcePath)) {
                $newPath = 'cms_services/images/' . $media->file_name;
                
                Storage::disk('public')->put(
                    $newPath,
                    file_get_contents($sourcePath)
                );
                
                $service->update(['image_path' => $newPath]);
                $this->info("Migrated image for CMS service #{$service->id}");
            }
        }
    }
} 