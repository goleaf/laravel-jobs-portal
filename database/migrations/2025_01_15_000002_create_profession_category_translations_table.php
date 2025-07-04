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
        Schema::create('profession_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profession_category_id');
            $table->string('locale', 10)->index(); // Language code (en, lt, ru, etc.)
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['profession_category_id', 'locale']);
            $table->unique(['profession_category_id', 'locale']);

            // Foreign key
            $table->foreign('profession_category_id')->references('id')->on('profession_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profession_category_translations');
    }
};
