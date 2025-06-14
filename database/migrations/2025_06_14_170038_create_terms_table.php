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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // For UI display
            $table->string('icon')->nullable(); // Icon class or path
            $table->string('image')->nullable(); // Image path
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('meta')->nullable(); // Additional metadata
            
            // Hierarchical support
            $table->foreignId('parent_id')->nullable()->constrained('terms')->onDelete('cascade');
            $table->integer('level')->default(0);
            $table->string('path')->nullable(); // For quick hierarchy queries
            
            // Usage statistics
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['taxonomy_id', 'slug']);
            $table->index(['taxonomy_id', 'is_active']);
            $table->index(['parent_id', 'sort_order']);
            $table->index('usage_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
