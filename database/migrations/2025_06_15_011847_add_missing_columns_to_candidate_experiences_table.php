<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidate_experiences', function (Blueprint $table) {
            // Add missing columns to match CandidateExperience model
            $table->string('job_level', 50)->nullable()->after('description')->comment('Job level (Junior, Senior, Manager, etc.)');
            $table->string('employment_type', 50)->nullable()->after('job_level')->comment('Employment type (Full-time, Part-time, Contract, etc.)');
            $table->decimal('salary', 10, 2)->nullable()->after('employment_type')->comment('Salary amount');
            $table->boolean('is_verified')->default(false)->after('salary')->comment('Whether experience is verified');

            // Add indexes for performance
            $table->index('job_level');
            $table->index('employment_type');
            $table->index('is_verified');
            $table->index('salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_experiences', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['candidate_experiences_job_level_index']);
            $table->dropIndex(['candidate_experiences_employment_type_index']);
            $table->dropIndex(['candidate_experiences_is_verified_index']);
            $table->dropIndex(['candidate_experiences_salary_index']);

            // Drop added columns
            $table->dropColumn([
                'job_level',
                'employment_type',
                'salary',
                'is_verified',
            ]);
        });
    }
};
