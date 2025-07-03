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
        Schema::table('countries', function (Blueprint $table) {
            // Add missing columns to match enhanced Country model
            $table->string('iso_code', 2)->nullable()->after('short_code');
            $table->string('currency', 10)->nullable()->after('phone_code');
            $table->boolean('is_active')->default(true)->after('currency');
            $table->boolean('is_default')->default(false)->after('is_active');
            $table->boolean('is_featured')->default(false)->after('is_default');
            $table->string('flag_url')->nullable()->after('is_featured');
            $table->string('region', 100)->nullable()->after('flag_url');
            $table->string('continent', 50)->nullable()->after('region');
            $table->integer('population')->nullable()->after('continent');
            $table->float('area_km2')->nullable()->after('population');
            $table->string('capital', 100)->nullable()->after('area_km2');
            $table->string('timezone', 50)->nullable()->after('capital');
            $table->json('languages')->nullable()->after('timezone');

            // Add soft deletes support
            $table->softDeletes()->after('updated_at');

            // Add indexes for performance
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('is_default');
            $table->index('region');
            $table->index('continent');
            $table->index(['is_active', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['countries_is_active_index']);
            $table->dropIndex(['countries_is_featured_index']);
            $table->dropIndex(['countries_is_default_index']);
            $table->dropIndex(['countries_region_index']);
            $table->dropIndex(['countries_continent_index']);
            $table->dropIndex(['countries_is_active_is_featured_index']);

            // Drop soft deletes
            $table->dropSoftDeletes();

            // Drop added columns
            $table->dropColumn([
                'iso_code',
                'currency',
                'is_active',
                'is_default',
                'is_featured',
                'flag_url',
                'region',
                'continent',
                'population',
                'area_km2',
                'capital',
                'timezone',
                'languages',
            ]);
        });
    }
};
