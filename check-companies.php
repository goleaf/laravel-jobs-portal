<?php

// Simple script to check companies without heavy Laravel bootstrap
try {
    $pdo = new PDO('mysql:host=localhost;dbname=jobportal_prus_dev', 'jobportal_prus_dev', 'jobportal_prus_dev');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM companies');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total companies in database: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        $stmt = $pdo->query('SELECT id, ceo, user_id FROM companies LIMIT 5');
        $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nFirst 5 companies:\n";
        foreach ($companies as $company) {
            echo "- ID: {$company['id']}, CEO: {$company['ceo']}, User ID: {$company['user_id']}\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 