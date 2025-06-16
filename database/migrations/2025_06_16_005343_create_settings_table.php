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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Core setting data
            $table->string('key')->unique()->index();
            $table->longText('value')->nullable();
            
            // Enhanced metadata from Habr patterns
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'array', 'json', 'object'])
                  ->default('string');
            $table->string('group')->default('general')->index();
            $table->text('description')->nullable();
            
            // Access control
            $table->boolean('is_public')->default(false)->index();
            
            // Validation and defaults
            $table->json('validation_rules')->nullable();
            $table->text('default_value')->nullable();
            
            // Audit trail
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['group', 'is_public']);
            $table->index(['type', 'group']);
            
            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
