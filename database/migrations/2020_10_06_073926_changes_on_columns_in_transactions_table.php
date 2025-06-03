<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Only drop foreign key if not using SQLite
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign('transactions_subscription_id_foreign');
            }
            
            // Drop column works for all database drivers
            if (Schema::hasColumn('transactions', 'subscription_id')) {
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
