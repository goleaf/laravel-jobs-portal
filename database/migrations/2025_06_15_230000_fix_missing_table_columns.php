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
        // Add missing columns to header_sliders table
        Schema::table('header_sliders', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('target')->default('_self');
            $table->string('css_class')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
        });

        // Add deleted_at to inquiries table
        Schema::table('inquiries', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('header_sliders', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'sub_title', 'description', 'button_text', 'button_url',
                'image_url', 'is_featured', 'sort_order', 'target', 'css_class',
                'metadata', 'published_at', 'expires_at',
            ]);
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
