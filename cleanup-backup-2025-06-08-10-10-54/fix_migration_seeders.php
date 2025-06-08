<?php

/**
 * Fix Migration Seeders Script
 * Removes problematic Artisan::call statements from migrations
 */

$migrationFiles = [
    'database/migrations/2024_02_17_041054_run_reset_password_email_template.php',
    'database/migrations/2024_03_22_050110_add_default_language_seeder.php',
    'database/migrations/2022_10_07_050613_add_plan_currency_id_to_transactions_table.php',
    'database/migrations/2023_09_02_073953_run_default_env_setting_seeder_table.php',
    'database/migrations/2022_08_29_090208_add_is_approved_to_transactions_table.php',
    'database/migrations/2023_12_11_104535_add_paystack_key_to_env_settings_table.php',
];

$fixed = 0;
$errors = 0;

foreach ($migrationFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    try {
        $content = file_get_contents($file);
        
        // Pattern to match Artisan::call statements in migrations
        $pattern = '/\s*Artisan::call\(\s*[\'"]db:seed[\'"],\s*\[[^\]]*\]\s*\);\s*/';
        
        $newContent = preg_replace($pattern, "\n        // Seeding removed - handled by separate seeders\n", $content);
        
        if ($content !== $newContent) {
            file_put_contents($file, $newContent);
            echo "✅ Fixed: $file\n";
            $fixed++;
        } else {
            echo "ℹ️  No changes needed: $file\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error processing $file: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n📊 Summary:\n";
echo "✅ Fixed: $fixed files\n";
echo "❌ Errors: $errors files\n";
echo "\n🚀 Ready to run migrations!\n"; 