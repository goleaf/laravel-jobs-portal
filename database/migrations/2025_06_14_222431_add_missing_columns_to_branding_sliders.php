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
        Schema::table('branding_sliders', function (Blueprint $table) {
            // Add missing columns that the model expects
            if (!Schema::hasColumn('branding_sliders', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('branding_sliders', 'link_url')) {
                $table->string('link_url', 500)->nullable();
            }
            if (!Schema::hasColumn('branding_sliders', 'button_text')) {
                $table->string('button_text', 100)->nullable();
            }
            if (!Schema::hasColumn('branding_sliders', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
            if (!Schema::hasColumn('branding_sliders', 'start_date')) {
                $table->datetime('start_date')->nullable();
            }
            if (!Schema::hasColumn('branding_sliders', 'end_date')) {
                $table->datetime('end_date')->nullable();
            }
            if (!Schema::hasColumn('branding_sliders', 'meta')) {
                $table->json('meta')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branding_sliders', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'link_url', 
                'button_text',
                'is_featured',
                'start_date',
                'end_date',
                'meta'
            ]);
        });
    }
};
