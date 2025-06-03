<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Boot the application
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Company;
use App\Models\Industry;
use App\Models\OwnerShipType;
use App\Models\CompanySize;

try {
    // Check if we have required data
    $industry = Industry::first();
    $ownershipType = OwnerShipType::first();
    $companySize = CompanySize::first();
    
    if (!$industry || !$ownershipType || !$companySize) {
        echo "❌ Missing required reference data (industry, ownership type, or company size)\n";
        echo "Industry: " . ($industry ? "✅" : "❌") . "\n";
        echo "Ownership Type: " . ($ownershipType ? "✅" : "❌") . "\n";
        echo "Company Size: " . ($companySize ? "✅" : "❌") . "\n";
        exit(1);
    }
    
    // Create a test user
    $user = User::create([
        'first_name' => 'Test',
        'last_name' => 'Company',
        'email' => 'test-company@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    
    // Create a test company
    $company = Company::create([
        'user_id' => $user->id,
        'ceo' => 'Test CEO',
        'industry_id' => $industry->id,
        'ownership_type_id' => $ownershipType->id,
        'company_size_id' => $companySize->id,
        'established_in' => 2020,
        'details' => 'This is a test company',
        'website' => 'https://example.com',
        'location' => 'Test City',
        'no_of_offices' => 1,
        'unique_id' => 'test-company-' . time(),
    ]);
    
    echo "✅ Test company created successfully!\n";
    echo "Company ID: {$company->id}\n";
    echo "User ID: {$user->id}\n";
    echo "Company CEO: {$company->ceo}\n";
    echo "Test URL: https://jobportal.prus.dev/company/{$company->id}\n";
    
} catch (Exception $e) {
    echo "❌ Error creating test company: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 