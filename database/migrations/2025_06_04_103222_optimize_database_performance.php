<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add performance indexes for jobs table
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                if (!Schema::hasIndex('jobs', 'jobs_status_index')) {
                    $table->index('status');
                }
                if (!Schema::hasIndex('jobs', 'jobs_created_at_index')) {
                    $table->index('created_at');
                }
                if (!Schema::hasIndex('jobs', 'jobs_company_id_index')) {
                    $table->index('company_id');
                }
                if (!Schema::hasIndex('jobs', 'jobs_status_created_at_index')) {
                    $table->index(['status', 'created_at']);
                }
            });
        }
        
        // Add performance indexes for companies table
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (!Schema::hasIndex('companies', 'companies_is_featured_index')) {
                    $table->index('is_featured');
                }
                if (!Schema::hasIndex('companies', 'companies_created_at_index')) {
                    $table->index('created_at');
                }
            });
        }
        
        // Add performance indexes for users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'user_type') && !Schema::hasIndex('users', 'users_user_type_index')) {
                    $table->index('user_type');
                }
                if (!Schema::hasIndex('users', 'users_created_at_index')) {
                    $table->index('created_at');
                }
            });
        }
    }
    
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['company_id']);
            $table->dropIndex(['status', 'created_at']);
        });
        
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['user_type']);
            $table->dropIndex(['created_at']);
        });
    }
};