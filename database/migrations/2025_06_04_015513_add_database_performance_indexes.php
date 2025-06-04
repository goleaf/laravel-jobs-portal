<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Helper function to check if index exists
        $indexExists = function ($table, $indexName) {
            try {
                $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
                return !empty($indexes);
            } catch (Exception $e) {
                return false;
            }
        };

        // Helper function to check if column exists
        $columnExists = function ($table, $columnName) {
            try {
                $columns = DB::select("DESCRIBE {$table}");
                $columnNames = array_map(fn($col) => $col->Field, $columns);
                return in_array($columnName, $columnNames);
            } catch (Exception $e) {
                return false;
            }
        };

        // Jobs table - only add indexes for columns that exist
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) use ($indexExists, $columnExists) {
                if ($columnExists('jobs', 'status') && $columnExists('jobs', 'created_at') && !$indexExists('jobs', 'jobs_status_created_at_index')) {
                    $table->index(['status', 'created_at'], 'jobs_status_created_at_index');
                }
                if ($columnExists('jobs', 'company_id') && !$indexExists('jobs', 'jobs_company_id_index')) {
                    $table->index('company_id', 'jobs_company_id_index');
                }
                if ($columnExists('jobs', 'salary_from') && $columnExists('jobs', 'salary_to') && !$indexExists('jobs', 'jobs_salary_range_index')) {
                    $table->index(['salary_from', 'salary_to'], 'jobs_salary_range_index');
                }
                if ($columnExists('jobs', 'expires_on') && !$indexExists('jobs', 'jobs_expires_on_index')) {
                    $table->index('expires_on', 'jobs_expires_on_index');
                }
                if ($columnExists('jobs', 'is_featured') && !$indexExists('jobs', 'jobs_is_featured_index')) {
                    $table->index('is_featured', 'jobs_is_featured_index');
                }
                if ($columnExists('jobs', 'job_category_id') && !$indexExists('jobs', 'jobs_job_category_id_index')) {
                    $table->index('job_category_id', 'jobs_job_category_id_index');
                }
            });
        }

        // Companies table
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) use ($indexExists, $columnExists) {
                if ($columnExists('companies', 'user_id') && !$indexExists('companies', 'companies_user_id_index')) {
                    $table->index('user_id', 'companies_user_id_index');
                }
                if ($columnExists('companies', 'is_active') && !$indexExists('companies', 'companies_is_active_index')) {
                    $table->index('is_active', 'companies_is_active_index');
                }
                if ($columnExists('companies', 'industry_id') && !$indexExists('companies', 'companies_industry_id_index')) {
                    $table->index('industry_id', 'companies_industry_id_index');
                }
            });
        }

        // Candidates table
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) use ($indexExists, $columnExists) {
                if ($columnExists('candidates', 'user_id') && !$indexExists('candidates', 'candidates_user_id_index')) {
                    $table->index('user_id', 'candidates_user_id_index');
                }
                if ($columnExists('candidates', 'career_level_id') && !$indexExists('candidates', 'candidates_career_level_id_index')) {
                    $table->index('career_level_id', 'candidates_career_level_id_index');
                }
            });
        }

        // Job applications table
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) use ($indexExists, $columnExists) {
                if ($columnExists('job_applications', 'job_id') && $columnExists('job_applications', 'candidate_id') && !$indexExists('job_applications', 'job_applications_job_candidate_index')) {
                    $table->index(['job_id', 'candidate_id'], 'job_applications_job_candidate_index');
                }
                if ($columnExists('job_applications', 'status') && !$indexExists('job_applications', 'job_applications_status_index')) {
                    $table->index('status', 'job_applications_status_index');
                }
                if ($columnExists('job_applications', 'created_at') && !$indexExists('job_applications', 'job_applications_created_at_index')) {
                    $table->index('created_at', 'job_applications_created_at_index');
                }
            });
        }

        echo "Performance indexes added successfully based on existing table structure.\n";
    }

    public function down(): void
    {
        // Rollback indexes - only drop if they exist
        $indexExists = function ($table, $indexName) {
            try {
                $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
                return !empty($indexes);
            } catch (Exception $e) {
                return false;
            }
        };

        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('jobs', 'jobs_status_created_at_index')) {
                    $table->dropIndex('jobs_status_created_at_index');
                }
                if ($indexExists('jobs', 'jobs_company_id_index')) {
                    $table->dropIndex('jobs_company_id_index');
                }
                if ($indexExists('jobs', 'jobs_salary_range_index')) {
                    $table->dropIndex('jobs_salary_range_index');
                }
                if ($indexExists('jobs', 'jobs_expires_on_index')) {
                    $table->dropIndex('jobs_expires_on_index');
                }
                if ($indexExists('jobs', 'jobs_is_featured_index')) {
                    $table->dropIndex('jobs_is_featured_index');
                }
                if ($indexExists('jobs', 'jobs_job_category_id_index')) {
                    $table->dropIndex('jobs_job_category_id_index');
                }
            });
        }

        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('companies', 'companies_user_id_index')) {
                    $table->dropIndex('companies_user_id_index');
                }
                if ($indexExists('companies', 'companies_is_active_index')) {
                    $table->dropIndex('companies_is_active_index');
                }
                if ($indexExists('companies', 'companies_industry_id_index')) {
                    $table->dropIndex('companies_industry_id_index');
                }
            });
        }

        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('candidates', 'candidates_user_id_index')) {
                    $table->dropIndex('candidates_user_id_index');
                }
                if ($indexExists('candidates', 'candidates_career_level_id_index')) {
                    $table->dropIndex('candidates_career_level_id_index');
                }
            });
        }

        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('job_applications', 'job_applications_job_candidate_index')) {
                    $table->dropIndex('job_applications_job_candidate_index');
                }
                if ($indexExists('job_applications', 'job_applications_status_index')) {
                    $table->dropIndex('job_applications_status_index');
                }
                if ($indexExists('job_applications', 'job_applications_created_at_index')) {
                    $table->dropIndex('job_applications_created_at_index');
                }
            });
        }
    }
};