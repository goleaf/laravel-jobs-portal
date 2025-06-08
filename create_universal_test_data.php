<?php

/**
 * Universal Test Data Generator
 * Creates sample data for Universal API testing
 */

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Company;
use App\Models\Job;
use App\Models\Skill;
use App\Models\JobCategory;

echo "🌱 UNIVERSAL TEST DATA GENERATOR\n";
echo "=" . str_repeat("=", 40) . "\n\n";

try {
    // Initialize Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    echo "✅ Laravel Application Bootstrapped\n\n";

    // Create test users
    echo "👤 Creating Test Users...\n";
    $users = [];
    for ($i = 1; $i <= 3; $i++) {
        $user = User::create([
            'name' => "Universal User $i",
            'email' => "user$i@universal.dev",
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $users[] = $user;
        echo "   • Created: {$user->name} (ID: {$user->id})\n";
    }

    // Create test companies
    echo "\n🏢 Creating Test Companies...\n";
    $companies = [];
    foreach ($users as $index => $user) {
        $company = Company::create([
            'name' => "Universal Company " . ($index + 1),
            'email' => "company" . ($index + 1) . "@universal.dev",
            'phone' => "555-010" . ($index + 1),
            'website' => "https://company" . ($index + 1) . ".universal.dev",
            'description' => "A leading company in the Universal industry, focusing on innovation and excellence.",
            'location' => "Universal City, State",
            'user_id' => $user->id,
            'is_active' => true,
        ]);
        $companies[] = $company;
        echo "   • Created: {$company->name} (ID: {$company->id})\n";
    }

    // Create job categories if they don't exist
    echo "\n📂 Creating Job Categories...\n";
    $categories = [];
    $categoryNames = ['Software Development', 'Data Science', 'DevOps', 'Product Management'];
    foreach ($categoryNames as $categoryName) {
        $category = JobCategory::firstOrCreate([
            'name' => $categoryName,
            'description' => "Jobs related to $categoryName",
            'is_default' => false,
        ]);
        $categories[] = $category;
        echo "   • Created/Found: {$category->name} (ID: {$category->id})\n";
    }

    // Create skills
    echo "\n💼 Creating Skills...\n";
    $skills = [];
    $skillNames = ['Laravel', 'Vue.js', 'React', 'PHP', 'JavaScript', 'MySQL', 'Redis', 'Docker'];
    foreach ($skillNames as $skillName) {
        $skill = Skill::firstOrCreate([
            'name' => $skillName,
            'description' => "Proficiency in $skillName technology",
        ]);
        $skills[] = $skill;
        echo "   • Created/Found: {$skill->name} (ID: {$skill->id})\n";
    }

    // Create test jobs
    echo "\n💼 Creating Test Jobs...\n";
    $jobs = [];
    $jobTitles = [
        'Senior Laravel Developer',
        'Full Stack Vue.js Developer',
        'DevOps Engineer',
        'PHP Backend Developer',
        'Frontend React Developer'
    ];

    foreach ($companies as $companyIndex => $company) {
        for ($i = 0; $i < 2; $i++) {
            $jobTitle = $jobTitles[($companyIndex * 2 + $i) % count($jobTitles)];
            $job = Job::create([
                'title' => $jobTitle,
                'description' => "We are looking for an experienced $jobTitle to join our dynamic team at {$company->name}. This is an excellent opportunity to work with cutting-edge technologies and grow your career in a supportive environment.",
                'hide_salary' => false,
                'salary_from' => rand(60000, 80000),
                'salary_to' => rand(90000, 120000),
                'career_level_id' => 1,
                'functional_area_id' => 1,
                'job_type_id' => 1,
                'job_shift_id' => 1,
                'degree_level_id' => 1,
                'experience' => rand(2, 5) . '-' . rand(6, 10) . ' years',
                'position' => rand(1, 3),
                'job_expiry_date' => now()->addDays(30),
                'company_id' => $company->id,
                'job_category_id' => $categories[array_rand($categories)]->id,
                'salary_currency_id' => 1,
                'salary_period_id' => 1,
                'country_id' => 1,
                'state_id' => 1,
                'city_id' => 1,
                'is_freelance' => false,
                'is_suspended' => false,
                'status' => 'open',
                'created_by' => $company->user_id,
            ]);

            // Attach random skills
            $randomSkills = collect($skills)->random(rand(2, 4));
            $job->skills()->attach($randomSkills->pluck('id'));

            $jobs[] = $job;
            echo "   • Created: {$job->title} at {$company->name} (ID: {$job->id})\n";
        }
    }

    echo "\n📊 Test Data Summary:\n";
    echo "   • Users: " . count($users) . "\n";
    echo "   • Companies: " . count($companies) . "\n";
    echo "   • Jobs: " . count($jobs) . "\n";
    echo "   • Skills: " . count($skills) . "\n";
    echo "   • Categories: " . count($categories) . "\n";

    echo "\n🎉 UNIVERSAL TEST DATA CREATED SUCCESSFULLY!\n";
    echo "✅ Ready for Universal API testing\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    if ($e->getPrevious()) {
        echo "   Previous: " . $e->getPrevious()->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n"; 