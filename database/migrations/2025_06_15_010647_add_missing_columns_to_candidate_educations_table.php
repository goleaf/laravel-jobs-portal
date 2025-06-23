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
        Schema::table('candidate_educations', function (Blueprint $table) {
            // Add missing columns to match CandidateEducation model
            $table->decimal('grade_percentage', 5, 2)->nullable()->after('year')->comment('Grade percentage (0-100)');
            $table->string('field_of_study', 100)->nullable()->after('grade_percentage')->comment('Field of study/major');
            $table->text('description')->nullable()->after('field_of_study')->comment('Additional description');
            $table->boolean('is_verified')->default(false)->after('description')->comment('Whether education is verified');

            // Add indexes for performance
            $table->index('is_verified');
            $table->index('grade_percentage');
            $table->index('field_of_study');
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_educations', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['candidate_educations_is_verified_index']);
            $table->dropIndex(['candidate_educations_grade_percentage_index']);
            $table->dropIndex(['candidate_educations_field_of_study_index']);
            $table->dropIndex(['candidate_educations_year_index']);

            // Drop added columns
            $table->dropColumn([
                'grade_percentage',
                'field_of_study',
                'description',
                'is_verified',
            ]);
        });
    }
};
