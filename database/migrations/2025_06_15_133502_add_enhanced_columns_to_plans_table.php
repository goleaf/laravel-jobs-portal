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
        Schema::table('plans', function (Blueprint $table) {
            // Add enhanced columns that the Plan model expects
            $table->boolean('is_active')->default(true)->after('is_trial_plan');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->boolean('priority_support')->default(false)->after('is_featured');
            $table->boolean('analytics_access')->default(false)->after('priority_support');
            $table->integer('max_featured_jobs')->default(0)->after('analytics_access');
            $table->integer('duration_days')->default(30)->after('max_featured_jobs');
            
            // Add indexes for frequently used columns
            $table->index('is_active');
            $table->index('is_featured');
            $table->index(['is_active', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropIndex(['plans_is_active_index']);
            $table->dropIndex(['plans_is_featured_index']);
            $table->dropIndex(['plans_is_active_is_featured_index']);
            
            $table->dropColumn([
                'is_active',
                'is_featured',
                'priority_support',
                'analytics_access',
                'max_featured_jobs',
                'duration_days',
            ]);
        });
    }
};
