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
            $table->timestamp('featured_start_date')->nullable()->after('featured_until');
            $table->timestamp('featured_end_date')->nullable()->after('featured_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('featured_records', function (Blueprint $table) {
            $table->dropColumn(['featured_start_date', 'featured_end_date']);
        });
    }
};
