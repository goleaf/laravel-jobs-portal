<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Jobs table indexes
        Schema::table('jobs', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index(['company_id', 'status']);
            $table->index(['job_category_id', 'status']);
            $table->index(['location', 'status']);
            $table->index(['job_type', 'status']);
            $table->index(['experience_level', 'status']);
            $table->index(['salary_min', 'salary_max']);
            $table->fullText(['title', 'description']);
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index(['email_verified_at']);
            $table->index(['is_active', 'created_at']);
            $table->index(['user_type', 'is_active']);
            $table->index(['last_login_at']);
        });

        // Companies table indexes
        Schema::table('companies', function (Blueprint $table) {
            $table->index(['is_active', 'created_at']);
            $table->index(['industry_id', 'is_active']);
            $table->index(['location', 'is_active']);
            $table->fullText(['name', 'description']);
        });

        // Job applications table indexes
        Schema::table('job_applications', function (Blueprint $table) {
            $table->index(['job_id', 'status']);
            $table->index(['candidate_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['job_category_id', 'status']);
            $table->dropIndex(['location', 'status']);
            $table->dropIndex(['job_type', 'status']);
            $table->dropIndex(['experience_level', 'status']);
            $table->dropIndex(['salary_min', 'salary_max']);
            $table->dropFullText(['title', 'description']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email_verified_at']);
            $table->dropIndex(['is_active', 'created_at']);
            $table->dropIndex(['user_type', 'is_active']);
            $table->dropIndex(['last_login_at']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'created_at']);
            $table->dropIndex(['industry_id', 'is_active']);
            $table->dropIndex(['location', 'is_active']);
            $table->dropFullText(['name', 'description']);
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex(['job_id', 'status']);
            $table->dropIndex(['candidate_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};