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
        Schema::create('profession_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->index(); // e.g., '1', '11', '111'
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('level')->default(1); // 1=main category, 2=subcategory, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Additional data like icons, colors, etc.
            $table->timestamps();

            // Indexes
            $table->index(['parent_id', 'is_active']);
            $table->index(['level', 'sort_order']);
            $table->index('is_active');

            // Foreign key
            $table->foreign('parent_id')->references('id')->on('profession_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profession_categories');
    }
};
