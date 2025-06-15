<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Core Job Portal Models
        $tables = [
            'candidates',
            'job_categories', 
            'job_types',
            'job_applications',
            'job_shifts',
            'candidate_education',
            'candidate_experiences',
            'skills',
            'posts',
            'cms_services',
            'faqs',
            'testimonials',
            'email_templates',
            'news_letters',
            'notification_settings',
            'settings',
            'front_settings',
            'env_settings',
            'custom_medias',
            'image_sliders',
            'header_sliders',
            'branding_sliders',
            'countries',
            'states',
            'cities',
            'industries',
            'functional_areas',
            'reported_jobs',
            'reported_to_companies',
            'reported_to_candidates',
            'plans',
            'featured_records',
            'salary_currencies',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'settings')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->json('settings')->nullable()->after('updated_at');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'candidates',
            'job_categories', 
            'job_types',
            'job_applications',
            'job_shifts',
            'candidate_education',
            'candidate_experiences',
            'skills',
            'posts',
            'cms_services',
            'faqs',
            'testimonials',
            'email_templates',
            'news_letters',
            'notification_settings',
            'settings',
            'front_settings',
            'env_settings',
            'custom_medias',
            'image_sliders',
            'header_sliders',
            'branding_sliders',
            'countries',
            'states',
            'cities',
            'industries',
            'functional_areas',
            'reported_jobs',
            'reported_to_companies',
            'reported_to_candidates',
            'plans',
            'featured_records',
            'salary_currencies',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'settings')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('settings');
                });
            }
        }
    }
};
