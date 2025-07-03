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
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->index(); // Official profession code (e.g., '1111', '2211')
            $table->unsignedBigInteger('category_id');
            $table->string('isco_code', 10)->nullable(); // International Standard Classification of Occupations
            $table->string('skill_level', 20)->nullable(); // e.g., 'High', 'Medium', 'Low'
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();

            // Indexes
            $table->index(['category_id', 'is_active']);
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('isco_code');
            $table->index(['skill_level', 'is_active']);

            // Foreign key
            $table->foreign('category_id')->references('id')->on('profession_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professions');
    }
}; 