<?php

/**
 * Comprehensive Database Seeder Runner
 * 
 * This script runs the comprehensive seeder for all tables in the Laravel job portal application.
 * 
 * Usage:
 * php seed_all_tables.php
 * 
 * Or if you prefer to use artisan directly:
 * php artisan db:seed --class=ComprehensiveAllTablesSeeder
 */

echo "🚀 Starting Comprehensive Database Seeding...\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "\n";

// Check if we're in the correct directory
if (!file_exists('artisan')) {
    echo "❌ Error: This script must be run from the Laravel project root directory.\n";
    echo "Please navigate to your Laravel project directory and run: php seed_all_tables.php\n";
    exit(1);
}

// Run the comprehensive seeder
echo "📊 Running ComprehensiveAllTablesSeeder...\n\n";

$command = 'php artisan db:seed --class=ComprehensiveAllTablesSeeder';
$output = [];
$returnCode = 0;

exec($command . ' 2>&1', $output, $returnCode);

// Display the output
foreach ($output as $line) {
    echo $line . "\n";
}

if ($returnCode === 0) {
    echo "\n";
    echo "✅ SEEDING COMPLETED SUCCESSFULLY!\n";
    echo "=" . str_repeat("=", 50) . "\n";
    echo "\n";
    echo "🎉 All tables have been seeded with comprehensive data!\n";
    echo "\n";
    echo "📋 What was seeded:\n";
    echo "• Core System: Countries, States, Cities, Users, Settings\n";
    echo "• Master Data: Industries, Job Types, Skills, etc.\n";
    echo "• Job Portal: Companies, Jobs, Candidates, Applications\n";
    echo "• Content: Posts, Comments, FAQs, Testimonials\n";
    echo "• Communication: Notifications, Email Templates\n";
    echo "• Media: Sliders, Images, Files\n";
    echo "• Social: Favorites, Reports, Social Accounts\n";
    echo "• Financial: Plans, Subscriptions, Transactions\n";
    echo "• Utilities: Noticeboards, Todos\n";
    echo "\n";
    echo "🔗 Next Steps:\n";
    echo "1. Run: php artisan storage:link\n";
    echo "2. Clear caches: php artisan cache:clear && php artisan config:clear\n";
    echo "3. Access your application and explore the seeded data\n";
    echo "\n";
} else {
    echo "\n";
    echo "❌ SEEDING FAILED!\n";
    echo "=" . str_repeat("=", 50) . "\n";
    echo "\n";
    echo "Please check the error messages above and try again.\n";
    echo "You may need to:\n";
    echo "• Check your database connection\n";
    echo "• Run migrations first: php artisan migrate\n";
    echo "• Check for missing dependencies\n";
    echo "\n";
    exit(1);
}

?>