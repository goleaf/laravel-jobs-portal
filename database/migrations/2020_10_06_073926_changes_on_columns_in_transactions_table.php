<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip this migration for SQLite to avoid foreign key issues
        if ('sqlite' === DB::getDriverName()) {
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            // Check if foreign key exists before trying to drop it
            if (Schema::hasColumn('transactions', 'subscription_id')) {
                // Try to drop foreign key if it exists
                try {
                    $table->dropForeign('transactions_subscription_id_foreign');
                } catch (Exception $e) {
                    // Ignore if foreign key doesn't exist
                }

                // Drop the column
                $table->dropColumn('subscription_id');
            }

            // Add new columns if they don't exist
            if (!Schema::hasColumn('transactions', 'owner_id')) {
                $table->unsignedInteger('owner_id');
            }

            if (!Schema::hasColumn('transactions', 'owner_type')) {
                $table->string('owner_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Remove the added columns
            if (Schema::hasColumn('transactions', 'owner_id')) {
                $table->dropColumn('owner_id');
            }

            if (Schema::hasColumn('transactions', 'owner_type')) {
                $table->dropColumn('owner_type');
            }

            // Add back subscription_id column
            if (!Schema::hasColumn('transactions', 'subscription_id')) {
                $table->unsignedBigInteger('subscription_id')->nullable();
            }
        });
    }
};
