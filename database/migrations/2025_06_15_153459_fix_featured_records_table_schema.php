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
        Schema::table('featured_records', function (Blueprint $table) {
            // Add is_active column if it doesn't exist
            if (!Schema::hasColumn('featured_records', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            
            // Add indexes for better performance
            $table->index(['owner_id', 'owner_type'], 'featured_records_owner_index');
            $table->index(['start_time', 'end_time'], 'featured_records_time_index');
            $table->index('is_active', 'featured_records_is_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('featured_records', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('featured_records_owner_index');
            $table->dropIndex('featured_records_time_index');
            $table->dropIndex('featured_records_is_active_index');
            
            // Drop column if exists
            if (Schema::hasColumn('featured_records', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
