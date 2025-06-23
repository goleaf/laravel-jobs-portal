<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BasicJobsSeeder extends Seeder
{
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            // Create basic companies for job dependencies
            $companies = [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'industry_id' => 1,
                    'ownership_type_id' => 1,
                    'company_size_id' => 1,
                    'established_in' => 2020,
                    'details' => 'Technology company focused on software development',
                    'website' => 'https://techcorp.com',
                    'location' => 'New York, NY',
                    'is_featured' => 0,
                    'unique_id' => 'TC001',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'user_id' => 2,
                    'industry_id' => 1,
                    'ownership_type_id' => 1,
                    'company_size_id' => 2,
                    'established_in' => 2018,
                    'details' => 'Innovation-focused technology solutions',
                    'website' => 'https://innovation.com',
                    'location' => 'San Francisco, CA',
                    'is_featured' => 1,
                    'unique_id' => 'IL002',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3,
                    'user_id' => 3,
                    'industry_id' => 1,
                    'ownership_type_id' => 1,
                    'company_size_id' => 1,
                    'established_in' => 2019,
                    'details' => 'Future-oriented systems and solutions',
                    'website' => 'https://future.com',
                    'location' => 'Austin, TX',
                    'is_featured' => 0,
                    'unique_id' => 'FS003',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($companies as $company) {
                DB::table('companies')->updateOrInsert(['id' => $company['id']], $company);
            }

            // Create basic jobs for test dependencies
            $jobs = [
                [
                    'id' => 1,
                    'job_id' => 'JOB001',
                    'job_title' => 'Software Developer',
                    'description' => 'Develop software applications',
                    'company_id' => 1,
                    'job_category_id' => 1,
                    'job_type_id' => 1,
                    'salary_from' => 50000,
                    'salary_to' => 80000,
                    'currency_id' => 1,
                    'salary_period_id' => 1,
                    'country_id' => 1,
                    'state_id' => 1,
                    'city_id' => 1,
                    'is_freelance' => 0,
                    'career_level_id' => 1,
                    'functional_area_id' => 1,
                    'job_shift_id' => 1,
                    'degree_level_id' => 1,
                    'position' => 5,
                    'job_expiry_date' => now()->addDays(30),
                    'no_preference' => 0,
                    'hide_salary' => 0,
                    'experience' => '2-5 years',
                    'is_featured' => 0,
                    'status' => 1,
                    'is_default' => 0,
                    'is_created_by_admin' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'job_id' => 'JOB002',
                    'job_title' => 'Project Manager',
                    'description' => 'Manage software projects',
                    'company_id' => 2,
                    'job_category_id' => 1,
                    'job_type_id' => 1,
                    'salary_from' => 60000,
                    'salary_to' => 90000,
                    'currency_id' => 1,
                    'salary_period_id' => 1,
                    'country_id' => 1,
                    'state_id' => 2,
                    'city_id' => 2,
                    'is_freelance' => 0,
                    'career_level_id' => 1,
                    'functional_area_id' => 1,
                    'job_shift_id' => 1,
                    'degree_level_id' => 1,
                    'position' => 3,
                    'job_expiry_date' => now()->addDays(45),
                    'no_preference' => 0,
                    'hide_salary' => 0,
                    'experience' => '5+ years',
                    'is_featured' => 1,
                    'status' => 1,
                    'is_default' => 0,
                    'is_created_by_admin' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($jobs as $job) {
                DB::table('jobs')->updateOrInsert(['id' => $job['id']], $job);
            }

            // Create basic posts for test dependencies
            $posts = [
                [
                    'id' => 1,
                    'title' => 'Welcome to Our Job Portal',
                    'description' => 'This is our first blog post about job opportunities.',
                    'created_by' => 1,
                    'is_default' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'title' => 'Career Tips for Success',
                    'description' => 'Learn how to advance your career with these tips.',
                    'created_by' => 1,
                    'is_default' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($posts as $post) {
                DB::table('posts')->updateOrInsert(['id' => $post['id']], $post);
            }
        });
    }
}
