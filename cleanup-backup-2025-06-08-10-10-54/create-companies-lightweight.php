<?php

// Lightweight company creation using Laravel's database connection
// This avoids the heavy Laravel bootstrap that causes memory issues

ini_set('memory_limit', '256M');

// Minimal Laravel bootstrap - just what we need for database
require_once __DIR__.'/vendor/autoload.php';

// Create application with minimal services
$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// Register essential service providers
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// Bootstrap just the configuration and database
$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
]);

// Setup database manually
$config = $app['config'];
$dbConfig = $config['database.connections.mysql'];

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};port={$dbConfig['port']}",
        $dbConfig['username'],
        $dbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ Connected to database: {$dbConfig['database']}\n";
    
    // Check current data
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM companies');
    $companyCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM industries');
    $industryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "Current companies: $companyCount, Industries: $industryCount\n";
    
    if ($companyCount > 0) {
        echo "✅ Companies already exist!\n";
        
        // Show existing companies
        $stmt = $pdo->query('SELECT c.id, c.ceo, u.first_name, u.last_name FROM companies c LEFT JOIN users u ON c.user_id = u.id LIMIT 3');
        $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($companies as $company) {
            echo "- Company {$company['id']}: CEO {$company['ceo']}, User: {$company['first_name']} {$company['last_name']}\n";
        }
        
        // Test the first company
        $testUrl = "https://jobportal.prus.dev/company/{$companies[0]['id']}";
        echo "\n🔗 Test URL: $testUrl\n";
        
        exit(0);
    }
    
    // Create minimal reference data if needed
    if ($industryCount == 0) {
        echo "Creating reference data...\n";
        
        $pdo->exec("INSERT INTO industries (name, is_default, created_at, updated_at) VALUES 
            ('Technology', 1, NOW(), NOW()),
            ('Healthcare', 1, NOW(), NOW()),
            ('Finance', 1, NOW(), NOW())");
        
        $pdo->exec("INSERT INTO ownership_types (name, created_at, updated_at) VALUES 
            ('Private', NOW(), NOW()),
            ('Public', NOW(), NOW())");
        
        $pdo->exec("INSERT INTO company_sizes (size, created_at, updated_at) VALUES 
            ('1-10', NOW(), NOW()),
            ('11-50', NOW(), NOW()),
            ('51-200', NOW(), NOW())");
        
        echo "✅ Reference data created\n";
    }
    
    // Create test user and company
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, email_verified_at, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), ?, NOW(), NOW())');
    $stmt->execute(['Test', 'Company Owner', 'test-company-' . time() . '@example.com', password_hash('password', PASSWORD_DEFAULT)]);
    $userId = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare('INSERT INTO companies (user_id, ceo, industry_id, ownership_type_id, company_size_id, established_in, details, website, location, no_of_offices, unique_id, created_at, updated_at) VALUES (?, ?, 1, 1, 1, 2020, ?, ?, ?, 1, ?, NOW(), NOW())');
    $stmt->execute([
        $userId,
        'John Test CEO',
        'This is a test company created for testing the company routes functionality.',
        'https://test-company.example.com',
        'Test City, Test State',
        'test-company-' . time()
    ]);
    $companyId = $pdo->lastInsertId();
    
    $pdo->commit();
    
    echo "✅ Test company created successfully!\n";
    echo "Company ID: $companyId\n";
    echo "User ID: $userId\n";
    echo "🔗 Test URL: https://jobportal.prus.dev/company/$companyId\n";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 