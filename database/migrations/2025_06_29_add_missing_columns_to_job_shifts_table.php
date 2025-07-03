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
        Schema::table('job_shifts', function (Blueprint $table) {
            if (! Schema::hasColumn('job_shifts', 'start_time')) {
                $table->string('start_time')->nullable()->after('is_default');
            }
            if (! Schema::hasColumn('job_shifts', 'end_time')) {
                $table->string('end_time')->nullable()->after('start_time');
            }
            if (! Schema::hasColumn('job_shifts', 'duration_hours')) {
                $table->integer('duration_hours')->nullable()->after('end_time');
            }
            if (! Schema::hasColumn('job_shifts', 'is_flexible')) {
                $table->boolean('is_flexible')->default(false)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_shifts', function (Blueprint $table) {
            if (Schema::hasColumn('job_shifts', 'start_time')) {
                $table->dropColumn('start_time');
            }
            if (Schema::hasColumn('job_shifts', 'end_time')) {
                $table->dropColumn('end_time');
            }
            if (Schema::hasColumn('job_shifts', 'duration_hours')) {
                $table->dropColumn('duration_hours');
            }
            if (Schema::hasColumn('job_shifts', 'is_flexible')) {
                $table->dropColumn('is_flexible');
            }
        });
    }
};
