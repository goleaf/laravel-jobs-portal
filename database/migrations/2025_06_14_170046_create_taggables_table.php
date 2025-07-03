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
        Schema::create('taggables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->morphs('taggable'); // taggable_id, taggable_type
            $table->foreignId('taxonomy_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->json('meta')->nullable(); // Additional relationship metadata
            $table->timestamps();

            $table->unique(['term_id', 'taggable_id', 'taggable_type']);
            $table->index(['taxonomy_id', 'term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taggables');
    }
};
