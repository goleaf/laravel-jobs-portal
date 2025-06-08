<?php

// Simple database check script
try {
    $pdo = new PDO('mysql:host=localhost;dbname=job_portal_dev', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "📊 DATABASE STATUS REPORT\n";
    echo "========================\n";
    
    $tables = [
        'countries' => 'Countries',
        'states' => 'States', 
        'cities' => 'Cities',
        'users' => 'Users',
        'companies' => 'Companies',
        'jobs' => 'Jobs',
        'candidates' => 'Candidates',
        'job_applications' => 'Job Applications',
        'skills' => 'Skills',
        'job_categories' => 'Job Categories',
        'industries' => 'Industries',
        'plans' => 'Plans',
        'company_sizes' => 'Company Sizes',
        'functional_areas' => 'Functional Areas',
        'career_levels' => 'Career Levels',
        'salary_currencies' => 'Salary Currencies',
        'salary_periods' => 'Salary Periods',
        'job_types' => 'Job Types',
        'job_shifts' => 'Job Shifts',
        'required_degree_levels' => 'Degree Levels',
        'marital_status' => 'Marital Statuses',
        'languages' => 'Languages',
        'ownership_types' => 'Ownership Types'
    ];
    
    $totalRecords = 0;
    
    foreach ($tables as $table => $label) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
            $count = $stmt->fetchColumn();
            echo "✅ {$label}: {$count}\n";
            $totalRecords += $count;
        } catch (Exception $e) {
            echo "❌ {$label}: Table not found or error\n";
        }
    }
    
    echo "========================\n";
    echo "🎉 Total Records: {$totalRecords}\n";
    echo "🚀 Database seeding assessment complete!\n";
    
    // Check if we have a working job portal setup
    if ($totalRecords > 100) {
        echo "\n✅ SUCCESS: Database has sufficient data for job portal operation!\n";
        echo "📋 NEXT STEPS:\n";
        echo "1. Run: php artisan storage:link\n";
        echo "2. Visit your application to test functionality\n";
        echo "3. Login with seeded user accounts\n";
    } else {
        echo "\n⚠️  WARNING: Database may need more data for full functionality\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
?> 