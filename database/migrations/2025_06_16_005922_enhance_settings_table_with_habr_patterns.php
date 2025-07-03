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
        Schema::table('settings', function (Blueprint $table) {
            // Add new columns from Habr patterns
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'array', 'json', 'object'])
                ->default('string')->after('value');
            $table->string('group')->default('general')->index()->after('type');
            $table->text('description')->nullable()->after('group');

            // Access control
            $table->boolean('is_public')->default(false)->index()->after('description');

            // Validation and defaults
            $table->json('validation_rules')->nullable()->after('is_public');
            $table->text('default_value')->nullable()->after('validation_rules');

            // Audit trail
            $table->unsignedBigInteger('created_by')->nullable()->after('default_value');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            // Additional indexes for performance
            $table->index(['group', 'is_public']);
            $table->index(['type', 'group']);

            // Make key unique if not already
            $table->unique('key');
        });

        // Add foreign key constraints if users table exists
        if (Schema::hasTable('users')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex(['group', 'is_public']);
            $table->dropIndex(['type', 'group']);
            $table->dropUnique(['key']);

            // Drop added columns
            $table->dropColumn([
                'type',
                'group',
                'description',
                'is_public',
                'validation_rules',
                'default_value',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
