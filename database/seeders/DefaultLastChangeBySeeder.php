<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DefaultLastChangeBySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = getSuperAdmin()->id;
        
        // Check if the tables exist and have the required columns before updating
        if (Schema::hasTable('jobs') && Schema::hasColumn('jobs', 'last_change')) {
            // Use withTrashed() to avoid issues with SoftDeletes trait
            Job::withTrashed()->whereNull('last_change')->update(['last_change' => $adminId]);
        }
        
        if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'last_change')) {
            Company::whereNull('last_change')->update(['last_change' => $adminId]);
        }
        
        if (Schema::hasTable('candidates') && Schema::hasColumn('candidates', 'last_change')) {
            Candidate::whereNull('last_change')->update(['last_change' => $adminId]);
        }
        
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'approved_id')) {
            Transaction::whereNull('approved_id')->where('is_approved', '!=', Transaction::PENDING)->update(['approved_id' => $adminId]);
        }
    }
}
