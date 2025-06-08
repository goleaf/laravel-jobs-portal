<?php

// Minimal script to create test data
try {
    // Get database connection info from config
    $configContent = file_get_contents('config/database.php');
    
    // Try different database connection approaches
    $databases = [
        ['host' => 'localhost', 'db' => 'jobportal_prus_dev', 'user' => 'jobportal_prus_dev', 'pass' => 'jobportal_prus_dev'],
        ['host' => 'localhost', 'db' => 'jobportal_prus_dev', 'user' => 'root', 'pass' => ''],
        ['host' => '127.0.0.1', 'db' => 'jobportal_prus_dev', 'user' => 'root', 'pass' => ''],
    ];
    
    $pdo = null;
    foreach ($databases as $config) {
        try {
            $pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['db']}", 
                $config['user'], 
                $config['pass']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "✅ Connected to database with user: {$config['user']}\n";
            break;
        } catch (PDOException $e) {
            echo "❌ Failed to connect with user {$config['user']}: " . $e->getMessage() . "\n";
        }
    }
    
    if (!$pdo) {
        throw new Exception("Could not connect to database with any configuration");
    }
    
    // Check existing data
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM companies');
    $companyCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Current companies in database: $companyCount\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Current users in database: $userCount\n";
    
    // Check if we have required reference data
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM industries');
    $industryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM ownership_types');
    $ownershipCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM company_sizes');
    $sizeCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "Reference data - Industries: $industryCount, Ownership Types: $ownershipCount, Company Sizes: $sizeCount\n";
    
    if ($industryCount == 0 || $ownershipCount == 0 || $sizeCount == 0) {
        echo "❌ Missing required reference data. Need to run seeders first.\n";
        exit(1);
    }
    
    if ($companyCount > 0) {
        echo "✅ Companies already exist. No need to create test data.\n";
        
        // Show first few companies
        $stmt = $pdo->query('SELECT c.id, c.ceo, u.first_name, u.last_name, u.email FROM companies c LEFT JOIN users u ON c.user_id = u.id LIMIT 3');
        $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nExisting companies:\n";
        foreach ($companies as $company) {
            echo "- ID: {$company['id']}, CEO: {$company['ceo']}, User: {$company['first_name']} {$company['last_name']} ({$company['email']})\n";
        }
        exit(0);
    }
    
    // Create test user and company
    echo "\n🚀 Creating test company...\n";
    
    // Get reference data IDs
    $stmt = $pdo->query('SELECT id FROM industries LIMIT 1');
    $industryId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    
    $stmt = $pdo->query('SELECT id FROM ownership_types LIMIT 1');
    $ownershipId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    
    $stmt = $pdo->query('SELECT id FROM company_sizes LIMIT 1');
    $sizeId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    
    $pdo->beginTransaction();
    
    // Create user
    $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, email_verified_at, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([
        'Test',
        'Company Owner',
        'test-company-' . time() . '@example.com',
        date('Y-m-d H:i:s'),
        password_hash('password', PASSWORD_DEFAULT)
    ]);
    $userId = $pdo->lastInsertId();
    
    // Create company
    $stmt = $pdo->prepare('INSERT INTO companies (user_id, ceo, industry_id, ownership_type_id, company_size_id, established_in, details, website, location, no_of_offices, unique_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([
        $userId,
        'John Test CEO',
        $industryId,
        $ownershipId,
        $sizeId,
        2020,
        'This is a test company created for testing purposes.',
        'https://test-company.example.com',
        'Test City, Test State',
        1,
        'test-company-' . time()
    ]);
    $companyId = $pdo->lastInsertId();
    
    $pdo->commit();
    
    echo "✅ Test company created successfully!\n";
    echo "User ID: $userId\n";
    echo "Company ID: $companyId\n";
    echo "Test URL: https://jobportal.prus.dev/company/$companyId\n";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "❌ Error: " . $e->getMessage() . "\n";
} 