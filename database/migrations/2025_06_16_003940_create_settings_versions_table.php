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
        Schema::create('settings_versions', function (Blueprint $table) {
            $table->id();
            $table->uuid('version_id')->unique()->index();

            // Model information
            $table->string('model_type');
            $table->string('model_id');
            $table->index(['model_type', 'model_id']);

            // Version information
            $table->integer('version_number')->default(1);
            $table->string('change_type')->default('update'); // create, update, delete, rollback
            $table->text('change_reason')->nullable();
            $table->json('change_summary')->nullable(); // Summary of what changed

            // Settings data
            $table->json('settings_data'); // Full settings snapshot
            $table->json('previous_settings')->nullable(); // Previous version for comparison
            $table->json('changed_keys')->nullable(); // Array of keys that changed

            // Metadata
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('source')->default('api'); // api, admin, system, migration
            $table->string('user_agent')->nullable();
            $table->ipAddress('ip_address')->nullable();

            // Performance and validation
            $table->boolean('is_active')->default(true);
            $table->boolean('is_validated')->default(true);
            $table->json('validation_errors')->nullable();
            $table->integer('size_bytes')->nullable(); // Size of settings data

            // Audit and compliance
            $table->timestamp('created_at');
            $table->timestamp('expires_at')->nullable(); // For data retention
            $table->string('checksum', 64)->nullable(); // Data integrity verification

            // Indexes for performance
            $table->index(['model_type', 'model_id', 'version_number']);
            $table->index(['user_id', 'created_at']);
            $table->index(['change_type', 'created_at']);
            $table->index('is_active');
            $table->index('created_at');

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_versions');
    }
};
