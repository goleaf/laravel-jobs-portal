<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add nullable columns first
        Schema::table('job_types', function (Blueprint $table) {
            $table->string('slug', 191)->nullable()->after('name');
            $table->string('icon', 50)->nullable()->after('description');
            $table->string('color', 7)->nullable()->after('icon');
            $table->boolean('is_active')->default(true)->after('color');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->integer('sort_order')->default(0)->after('is_featured');
            $table->string('meta_title', 191)->nullable()->after('sort_order');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');
            $table->unsignedBigInteger('views_count')->default(0)->after('meta_keywords');
            $table->unsignedBigInteger('jobs_count')->default(0)->after('views_count');
            $table->json('settings')->nullable()->after('jobs_count');
            $table->json('extra_attributes')->nullable()->after('settings');
        });

        // Step 2: Populate slug values for existing records
        $jobTypes = DB::table('job_types')->get();
        foreach ($jobTypes as $jobType) {
            $slug = Str::slug($jobType->name);
            $originalSlug = $slug;
            $counter = 1;
            
            // Ensure slug uniqueness
            while (DB::table('job_types')->where('slug', $slug)->where('id', '!=', $jobType->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            DB::table('job_types')->where('id', $jobType->id)->update(['slug' => $slug]);
        }

        // Step 3: Add unique constraint to slug after populating data
        Schema::table('job_types', function (Blueprint $table) {
            $table->unique('slug');
        });

        // Step 4: Add indexes for performance
        Schema::table('job_types', function (Blueprint $table) {
            $table->index(['is_active', 'is_featured'], 'job_types_active_featured_index');
            $table->index(['sort_order', 'name'], 'job_types_sort_name_index');
            $table->index(['is_default', 'is_active'], 'job_types_default_active_index');
            $table->index('jobs_count', 'job_types_jobs_count_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_types', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('job_types_active_featured_index');
            $table->dropIndex('job_types_sort_name_index');
            $table->dropIndex('job_types_default_active_index');
            $table->dropIndex('job_types_jobs_count_index');
            
            // Drop unique constraint
            $table->dropUnique(['slug']);
            
            // Drop columns
            $table->dropColumn([
                'slug',
                'icon',
                'color',
                'is_active',
                'is_featured',
                'sort_order',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'views_count',
                'jobs_count',
                'settings',
                'extra_attributes'
            ]);
        });
    }
};
