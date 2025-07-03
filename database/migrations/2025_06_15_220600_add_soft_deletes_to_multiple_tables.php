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
        // Add deleted_at to favourite_companies
        Schema::table('favourite_companies', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to favourite_jobs
        Schema::table('favourite_jobs', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to files
        Schema::table('files', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favourite_companies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('favourite_jobs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
