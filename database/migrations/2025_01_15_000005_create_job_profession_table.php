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
        Schema::create('job_profession', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('profession_id');
            $table->boolean('is_primary')->default(false); // Mark primary profession for job
            $table->integer('relevance_score')->default(100); // How relevant this profession is (0-100)
            $table->timestamps();

            // Indexes
            $table->index(['job_id', 'profession_id']);
            $table->index(['profession_id', 'job_id']);
            $table->index('is_primary');
            $table->unique(['job_id', 'profession_id']);

            // Foreign keys
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_profession');
    }
};
