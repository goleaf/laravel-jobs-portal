<?php

/**
 * Fix Performance Indexes Migration
 * This script creates a corrected migration based on actual database schema
 */

// First, let's rollback the failed migration
exec('php artisan migrate:rollback --step=1');

// Remove the failed migration file
$failedMigration = glob('database/migrations/*_add_performance_indexes.php')[0] ?? null;
if ($failedMigration && file_exists($failedMigration)) {
    unlink($failedMigration);
    echo "✅ Removed failed migration file\n";
}

// Create corrected migration content
$migrationContent = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users table optimization (using actual columns)
        Schema::table('users', function (Blueprint $table) {
            // Check if indexes don't already exist before adding
            $indexes = Schema::getConnection()->getDoctrineSchemaManager()
                ->listTableIndexes('users');
                
            if (!isset($indexes['users_email_verified_at_index'])) {
                $table->index(['email_verified_at']);
            }
            if (!isset($indexes['users_created_at_index'])) {
                $table->index(['created_at']);
            }
            if (!isset($indexes['users_is_active_index'])) {
                $table->index(['is_active']);
            }
            if (!isset($indexes['users_owner_type_owner_id_index'])) {
                $table->index(['owner_type', 'owner_id']);
            }
        });

        // Jobs table optimization
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $indexes = Schema::getConnection()->getDoctrineSchemaManager()
                    ->listTableIndexes('jobs');
                    
                if (!isset($indexes['jobs_status_index'])) {
                    $table->index(['status']);
                }
                if (!isset($indexes['jobs_featured_index'])) {
                    $table->index(['featured']);
                }
                if (!isset($indexes['jobs_job_expiry_date_index'])) {
                    $table->index(['job_expiry_date']);
                }
                if (!isset($indexes['jobs_company_id_index'])) {
                    $table->index(['company_id']);
                }
                if (!isset($indexes['jobs_created_at_index'])) {
                    $table->index(['created_at']);
                }
                if (Schema::hasColumns('jobs', ['salary_from', 'salary_to'])) {
                    if (!isset($indexes['jobs_salary_range_index'])) {
                        $table->index(['salary_from', 'salary_to'], 'jobs_salary_range_index');
                    }
                }
                if (Schema::hasColumns('jobs', ['country_id', 'state_id', 'city_id'])) {
                    if (!isset($indexes['jobs_location_index'])) {
                        $table->index(['country_id', 'state_id', 'city_id'], 'jobs_location_index');
                    }
                }
            });
        }

        // Companies table optimization
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                $indexes = Schema::getConnection()->getDoctrineSchemaManager()
                    ->listTableIndexes('companies');
                    
                if (Schema::hasColumn('companies', 'is_active')) {
                    if (!isset($indexes['companies_is_active_index'])) {
                        $table->index(['is_active']);
                    }
                }
                if (!isset($indexes['companies_is_featured_index'])) {
                    $table->index(['is_featured']);
                }
                if (!isset($indexes['companies_user_id_index'])) {
                    $table->index(['user_id']);
                }
                if (!isset($indexes['companies_created_at_index'])) {
                    $table->index(['created_at']);
                }
            });
        }

        // Job applications optimization
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $indexes = Schema::getConnection()->getDoctrineSchemaManager()
                    ->listTableIndexes('job_applications');
                    
                if (!isset($indexes['job_applications_job_id_index'])) {
                    $table->index(['job_id']);
                }
                if (!isset($indexes['job_applications_candidate_id_index'])) {
                    $table->index(['candidate_id']);
                }
                if (Schema::hasColumn('job_applications', 'status')) {
                    if (!isset($indexes['job_applications_status_index'])) {
                        $table->index(['status']);
                    }
                }
                if (!isset($indexes['job_applications_created_at_index'])) {
                    $table->index(['created_at']);
                }
            });
        }

        // Candidates optimization
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) {
                $indexes = Schema::getConnection()->getDoctrineSchemaManager()
                    ->listTableIndexes('candidates');
                    
                if (!isset($indexes['candidates_user_id_index'])) {
                    $table->index(['user_id']);
                }
                if (Schema::hasColumn('candidates', 'is_active')) {
                    if (!isset($indexes['candidates_is_active_index'])) {
                        $table->index(['is_active']);
                    }
                }
                if (!isset($indexes['candidates_created_at_index'])) {
                    $table->index(['created_at']);
                }
            });
        }
    }

    public function down(): void
    {
        // Users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email_verified_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['owner_type', 'owner_id']);
        });

        // Jobs table
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropIndex(['status']);
                $table->dropIndex(['featured']);
                $table->dropIndex(['job_expiry_date']);
                $table->dropIndex(['company_id']);
                $table->dropIndex(['created_at']);
                $table->dropIndex('jobs_salary_range_index');
                $table->dropIndex('jobs_location_index');
            });
        }

        // Companies table
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
                $table->dropIndex(['is_featured']);
                $table->dropIndex(['user_id']);
                $table->dropIndex(['created_at']);
            });
        }

        // Job applications
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->dropIndex(['job_id']);
                $table->dropIndex(['candidate_id']);
                $table->dropIndex(['status']);
                $table->dropIndex(['created_at']);
            });
        }

        // Candidates
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
                $table->dropIndex(['is_active']);
                $table->dropIndex(['created_at']);
            });
        }
    }
};
PHP;

// Create corrected migration
$timestamp = date('Y_m_d_His');
$filename = "database/migrations/{$timestamp}_add_corrected_performance_indexes.php";

file_put_contents($filename, $migrationContent);
echo "✅ Created corrected migration: {$filename}\n";

// Run the corrected migration
exec('php artisan migrate', $output, $return_var);

if ($return_var === 0) {
    echo "✅ Migration executed successfully\n";
    foreach ($output as $line) {
        echo "  $line\n";
    }
} else {
    echo "❌ Migration failed\n";
    foreach ($output as $line) {
        echo "  $line\n";
    }
}

echo "\n🚀 Database performance optimization completed!\n"; 