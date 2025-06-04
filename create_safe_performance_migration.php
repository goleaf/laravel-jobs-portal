<?php

/**
 * Create Safe Performance Indexes Migration
 * 
 * This script creates a migration that safely adds performance indexes
 * without conflicts with existing indexes.
 */

$migrationContent = <<<'EOF'
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
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        };

        // Jobs table performance indexes
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('jobs', 'jobs_status_created_at_index')) {
                    $table->index(['status', 'created_at'], 'jobs_status_created_at_index');
                }
                if (!$indexExists('jobs', 'jobs_company_id_is_active_index')) {
                    $table->index(['company_id', 'is_active'], 'jobs_company_id_is_active_index');
                }
                if (!$indexExists('jobs', 'jobs_salary_range_index')) {
                    $table->index(['salary_from', 'salary_to'], 'jobs_salary_range_index');
                }
                if (!$indexExists('jobs', 'jobs_expires_on_index')) {
                    $table->index('expires_on', 'jobs_expires_on_index');
                }
                if (!$indexExists('jobs', 'jobs_is_featured_index')) {
                    $table->index('is_featured', 'jobs_is_featured_index');
                }
            });
        }

        // Companies table performance indexes
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('companies', 'companies_is_active_index')) {
                    $table->index('is_active', 'companies_is_active_index');
                }
                if (!$indexExists('companies', 'companies_industry_id_index')) {
                    $table->index('industry_id', 'companies_industry_id_index');
                }
            });
        }

        // Candidates table performance indexes
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('candidates', 'candidates_is_active_index')) {
                    $table->index('is_active', 'candidates_is_active_index');
                }
                if (!$indexExists('candidates', 'candidates_career_level_id_index')) {
                    $table->index('career_level_id', 'candidates_career_level_id_index');
                }
            });
        }

        // Job applications performance indexes
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('job_applications', 'job_applications_job_candidate_index')) {
                    $table->index(['job_id', 'candidate_id'], 'job_applications_job_candidate_index');
                }
                if (!$indexExists('job_applications', 'job_applications_status_index')) {
                    $table->index('status', 'job_applications_status_index');
                }
                if (!$indexExists('job_applications', 'job_applications_created_at_index')) {
                    $table->index('created_at', 'job_applications_created_at_index');
                }
            });
        }

        // Job categories performance indexes
        if (Schema::hasTable('job_categories')) {
            Schema::table('job_categories', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('job_categories', 'job_categories_is_active_index')) {
                    $table->index('is_active', 'job_categories_is_active_index');
                }
            });
        }

        echo "Performance indexes added successfully.\n";
    }

    public function down(): void
    {
        // Helper function to check if index exists before dropping
        $indexExists = function ($table, $indexName) {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        };

        // Drop indexes if they exist
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('jobs', 'jobs_status_created_at_index')) {
                    $table->dropIndex('jobs_status_created_at_index');
                }
                if ($indexExists('jobs', 'jobs_company_id_is_active_index')) {
                    $table->dropIndex('jobs_company_id_is_active_index');
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
            });
        }
        
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) use ($indexExists) {
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
                if ($indexExists('candidates', 'candidates_is_active_index')) {
                    $table->dropIndex('candidates_is_active_index');
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
        
        if (Schema::hasTable('job_categories')) {
            Schema::table('job_categories', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('job_categories', 'job_categories_is_active_index')) {
                    $table->dropIndex('job_categories_is_active_index');
                }
            });
        }
    }
};
EOF;

$migrationFile = 'database/migrations/' . date('Y_m_d_His') . '_add_safe_performance_indexes.php';
file_put_contents($migrationFile, $migrationContent);

echo "✅ Safe performance indexes migration created: $migrationFile\n";
echo "🚀 Ready to run: php artisan migrate\n"; 