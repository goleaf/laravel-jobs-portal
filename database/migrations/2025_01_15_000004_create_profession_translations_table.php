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
        Schema::create('profession_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profession_id');
            $table->string('locale', 10)->index(); // Language code (en, lt, ru, etc.)
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->json('skills_required')->nullable(); // Array of required skills in this language
            $table->json('education_requirements')->nullable(); // Education level requirements in this language
            $table->timestamps();

            // Indexes
            $table->index(['profession_id', 'locale']);
            $table->unique(['profession_id', 'locale']);

            // Foreign key
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profession_translations');
    }
};
