<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SkillsSeeder extends Seeder
{
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            // Clear existing skills
            DB::table('skills')->truncate();
            
            // Enhanced skills data from infy-jobs.sql with additional modern skills
            $skills = [
                ['id' => 1, 'name' => 'Computer Skills', 'description' => 'Computer operating and software skills', 'is_default' => true],
                ['id' => 2, 'name' => 'Communication Skills', 'description' => 'Verbal and written communication abilities', 'is_default' => true],
                ['id' => 3, 'name' => 'Customer Service Skills', 'description' => 'Customer service and support skills', 'is_default' => true],
                ['id' => 4, 'name' => 'Interpersonal Skills', 'description' => 'Ability to work effectively with others', 'is_default' => true],
                ['id' => 5, 'name' => 'Leadership Skills', 'description' => 'Leading teams and driving results', 'is_default' => true],
                ['id' => 6, 'name' => 'Management Skills', 'description' => 'Project and people management', 'is_default' => true],
                ['id' => 7, 'name' => 'Problem-solving Skills', 'description' => 'Analytical and critical thinking', 'is_default' => true],
                ['id' => 8, 'name' => 'Time Management Skills', 'description' => 'Organizing and prioritizing tasks', 'is_default' => true],
                
                // Additional modern skills
                ['id' => 9, 'name' => 'Digital Marketing', 'description' => 'Online marketing and social media expertise', 'is_default' => true],
                ['id' => 10, 'name' => 'Data Analysis', 'description' => 'Data interpretation and statistical analysis', 'is_default' => true],
                ['id' => 11, 'name' => 'Web Development', 'description' => 'Frontend and backend web development', 'is_default' => true],
                ['id' => 12, 'name' => 'Mobile Development', 'description' => 'iOS and Android app development', 'is_default' => true],
                ['id' => 13, 'name' => 'Cloud Computing', 'description' => 'AWS, Azure, and cloud infrastructure', 'is_default' => true],
                ['id' => 14, 'name' => 'Machine Learning', 'description' => 'AI and machine learning algorithms', 'is_default' => true],
                ['id' => 15, 'name' => 'Cybersecurity', 'description' => 'Information security and risk management', 'is_default' => true],
            ];

            foreach ($skills as $skill) {
                DB::table('skills')->updateOrInsert(
                    ['id' => $skill['id']],
                    array_merge($skill, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        });
    }
} 