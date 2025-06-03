<?php

// Run essential seeders with minimal memory footprint
echo "🚀 Running essential seeders...\n";

$seeders = [
    'CreateDefaultIndustriesSeeder',
    'CreateDefaultOwnerShipTypeSeeder', 
    'DefaultCompanySizeSeeder',
    'MakeCountriesSeeder',
    'CreateCompaniesSeeder'
];

foreach ($seeders as $seeder) {
    echo "Running $seeder...\n";
    
    $command = "php -d memory_limit=1G artisan db:seed --class=$seeder --force";
    $output = [];
    $returnVar = 0;
    
    exec($command . ' 2>&1', $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "✅ $seeder completed successfully\n";
    } else {
        echo "❌ $seeder failed with return code $returnVar\n";
        echo "Output: " . implode("\n", $output) . "\n";
        
        // If this seeder fails, try to continue with others
        if (strpos(implode("\n", $output), 'already exists') !== false || 
            strpos(implode("\n", $output), 'Duplicate entry') !== false) {
            echo "ℹ️  Seeder data already exists, continuing...\n";
        }
    }
    
    echo "---\n";
}

echo "✅ Essential seeders completed!\n";
echo "Testing company creation...\n";

// Test if companies were created
$testUrl = 'https://jobportal.prus.dev/company/1';
$response = file_get_contents($testUrl, false, stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'User-Agent: Mozilla/5.0',
        'timeout' => 10
    ]
]));

if ($response && strpos($response, 'Undefined variable') === false && strpos($response, '404') === false) {
    echo "✅ Company route is working!\n";
} else {
    echo "❌ Company route still has issues\n";
    if (strpos($response, '404') !== false) {
        echo "ℹ️  Still getting 404 - company may not exist with ID 1\n";
    }
} 