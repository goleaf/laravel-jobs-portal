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
            $table->string('start_time')->nullable()->after('description');
            $table->string('end_time')->nullable()->after('start_time');
            $table->integer('duration_hours')->nullable()->after('end_time');
            $table->boolean('is_active')->default(true)->after('duration_hours');
            $table->boolean('is_flexible')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_shifts', function (Blueprint $table) {
            //
        });
    }
};
