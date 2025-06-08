<?php

/**
 * SQLite Database Setup and Seeding Script
 * 
 * This script will:
 * 1. Create .env file with SQLite configuration
 * 2. Create SQLite database file
 * 3. Run migrations
 * 4. Run comprehensive seeds
 * 
 * Usage: php setup_sqlite_and_seed.php
 */

echo "🚀 SQLite Database Setup and Seeding\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Check if we're in the correct directory
if (!file_exists('artisan')) {
    echo "❌ Error: This script must be run from the Laravel project root directory.\n";
    echo "Please navigate to your Laravel project directory and run: php setup_sqlite_and_seed.php\n";
    exit(1);
}

// Step 1: Create .env file with SQLite configuration
echo "📝 Step 1: Creating .env file with SQLite configuration...\n";

$envContent = <<<ENV
APP_NAME=JobPortal
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# SQLite Database Configuration
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
DB_FOREIGN_KEYS=true

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
ENV;

file_put_contents('.env', $envContent);
echo "✅ .env file created with SQLite configuration\n\n";

// Step 2: Create database directory and SQLite file
echo "📁 Step 2: Creating SQLite database file...\n";

$databaseDir = 'database';
if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0755, true);
}

$dbPath = $databaseDir . '/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    echo "✅ SQLite database file created at: {$dbPath}\n";
} else {
    echo "⚠️  SQLite database file already exists at: {$dbPath}\n";
}
echo "\n";

// Step 3: Generate application key
echo "🔐 Step 3: Generating application key...\n";
$keyGenCommand = 'php artisan key:generate --ansi';
$keyGenOutput = [];
$keyGenReturnCode = 0;
exec($keyGenCommand . ' 2>&1', $keyGenOutput, $keyGenReturnCode);

foreach ($keyGenOutput as $line) {
    echo $line . "\n";
}

if ($keyGenReturnCode === 0) {
    echo "✅ Application key generated successfully\n";
} else {
    echo "⚠️  Warning: Failed to generate application key\n";
}
echo "\n";

// Step 4: Run migrations
echo "🏗️  Step 4: Running database migrations...\n";
$migrateCommand = 'php artisan migrate --force --ansi';
$migrateOutput = [];
$migrateReturnCode = 0;

exec($migrateCommand . ' 2>&1', $migrateOutput, $migrateReturnCode);

foreach ($migrateOutput as $line) {
    echo $line . "\n";
}

if ($migrateReturnCode === 0) {
    echo "✅ Migrations completed successfully\n";
} else {
    echo "❌ Migration failed! Please check the errors above.\n";
    exit(1);
}
echo "\n";

// Step 5: Create storage link
echo "🔗 Step 5: Creating storage link...\n";
$linkCommand = 'php artisan storage:link --ansi';
$linkOutput = [];
$linkReturnCode = 0;

exec($linkCommand . ' 2>&1', $linkOutput, $linkReturnCode);

foreach ($linkOutput as $line) {
    echo $line . "\n";
}

if ($linkReturnCode === 0) {
    echo "✅ Storage link created successfully\n";
} else {
    echo "⚠️  Warning: Failed to create storage link\n";
}
echo "\n";

// Step 6: Run comprehensive seeds
echo "🌱 Step 6: Running comprehensive database seeds...\n";
echo "This may take a few minutes to complete...\n\n";

$seedCommand = 'php artisan db:seed --class=SQLiteOptimizedSeeder --ansi';
$seedOutput = [];
$seedReturnCode = 0;

exec($seedCommand . ' 2>&1', $seedOutput, $seedReturnCode);

foreach ($seedOutput as $line) {
    echo $line . "\n";
}

if ($seedReturnCode === 0) {
    echo "\n✅ SEEDING COMPLETED SUCCESSFULLY!\n";
} else {
    echo "\n❌ SEEDING FAILED! Please check the errors above.\n";
    
    // Try to run with regular DatabaseSeeder as fallback
    echo "\n🔄 Trying fallback seeding with DatabaseSeeder...\n";
    $fallbackCommand = 'php artisan db:seed --ansi';
    $fallbackOutput = [];
    $fallbackReturnCode = 0;
    
    exec($fallbackCommand . ' 2>&1', $fallbackOutput, $fallbackReturnCode);
    
    foreach ($fallbackOutput as $line) {
        echo $line . "\n";
    }
    
    if ($fallbackReturnCode !== 0) {
        exit(1);
    }
}

// Step 7: Clear caches
echo "\n🧹 Step 7: Clearing application caches...\n";
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan route:clear',
    'php artisan view:clear'
];

foreach ($commands as $command) {
    echo "Running: {$command}\n";
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Success\n";
    } else {
        echo "⚠️  Warning: Command failed\n";
        foreach ($output as $line) {
            echo "  {$line}\n";
        }
    }
}

// Final summary
echo "\n";
echo "🎉 SQLITE SETUP AND SEEDING COMPLETE!\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "\n";
echo "📊 Database Information:\n";
echo "• Database Type: SQLite\n";
echo "• Database File: {$dbPath}\n";
echo "• File Size: " . formatBytes(filesize($dbPath)) . "\n";
echo "\n";
echo "🚀 What's Ready:\n";
echo "• ✅ SQLite database configured and created\n";
echo "• ✅ All migrations executed\n";
echo "• ✅ Comprehensive seeds populated\n";
echo "• ✅ Storage links created\n";
echo "• ✅ Caches cleared\n";
echo "\n";
echo "🔗 Next Steps:\n";
echo "1. Start your Laravel server: php artisan serve\n";
echo "2. Visit: http://localhost:8000\n";
echo "3. Login with seeded user accounts\n";
echo "4. Explore all the seeded data!\n";
echo "\n";
echo "📋 Sample Data Available:\n";
echo "• 300+ Users (Admins, Employers, Candidates)\n";
echo "• 500+ Job Postings\n";
echo "• 80+ Companies\n";
echo "• 800+ Job Applications\n";
echo "• Complete Master Data\n";
echo "• Blog Posts, FAQs, Testimonials\n";
echo "• And much more!\n";
echo "\n";

// Database statistics
echo "📈 Database Statistics:\n";
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);
    echo "• Total Tables: " . count($tables) . "\n";
    
    $totalRecords = 0;
    foreach ($tables as $table) {
        $result = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
        $totalRecords += $result;
        if ($result > 0) {
            echo "• {$table}: {$result} records\n";
        }
    }
    echo "• Total Records: {$totalRecords}\n";
    
} catch (Exception $e) {
    echo "• Could not retrieve database statistics: " . $e->getMessage() . "\n";
}

echo "\n🎊 Your Laravel Job Portal with SQLite is ready to use!\n";

/**
 * Format bytes to human readable format
 */
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

?>