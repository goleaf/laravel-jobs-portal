<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

$modelsWithSoftDeletes = [
    'Country' => 'countries',
    'Setting' => 'settings', 
    'State' => 'states',
    'City' => 'cities',
    'MaritalStatus' => 'marital_status',
    'SalaryCurrency' => 'salary_currencies',
    'SalaryPeriod' => 'salary_periods',
    'Skill' => 'skills',
    'User' => 'users',
    'BrandingSliders' => 'branding_sliders',
    'Testimonial' => 'testimonials'
];

echo "Checking models with SoftDeletes trait...\n\n";

$modelsToFix = [];

foreach ($modelsWithSoftDeletes as $model => $table) {
    try {
        $hasDeletedAt = Schema::hasColumn($table, 'deleted_at');
        
        if (!$hasDeletedAt) {
            $modelsToFix[] = $model;
            echo "❌ {$model} (table: {$table}) - NO deleted_at column\n";
        } else {
            echo "✅ {$model} (table: {$table}) - HAS deleted_at column\n";
        }
    } catch (Exception $e) {
        echo "⚠️  {$model} (table: {$table}) - Error: {$e->getMessage()}\n";
    }
}

echo "\n\nModels that need SoftDeletes removed:\n";
foreach ($modelsToFix as $model) {
    echo "- {$model}\n";
} 