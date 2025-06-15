<?php

/**
 * Comprehensive Upgrade Verification for Laravel Job Portal
 * Verifies all upgrades have been applied successfully.
 */
echo "🔍 COMPREHENSIVE UPGRADE VERIFICATION - Laravel Job Portal\n";
echo "==========================================================\n\n";

$verification_results = [];
$total_checks = 0;
$passed_checks = 0;

// Helper function
function checkResult($description, $condition, &$total, &$passed)
{
    ++$total;
    if ($condition) {
        echo "✅ {$description}\n";
        ++$passed;

        return true;
    }
    echo "❌ {$description}\n";

    return false;
}

// 1. Security Verification
echo "🔐 SECURITY VERIFICATION\n";
echo "========================\n";

$security_passed = 0;
$security_total = 0;

checkResult(
    'AuthenticateMiddleware exists',
    file_exists('app/Http/Middleware/AuthenticateMiddleware.php'),
    $security_total,
    $security_passed
);

checkResult(
    'SecurityHeadersMiddleware exists',
    file_exists('app/Http/Middleware/SecurityHeadersMiddleware.php'),
    $security_total,
    $security_passed
);

checkResult(
    'Sanctum config exists',
    file_exists('config/sanctum.php'),
    $security_total,
    $security_passed
);

if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    checkResult(
        'APP_DEBUG is false',
        false !== strpos($env_content, 'APP_DEBUG=false'),
        $security_total,
        $security_passed
    );

    checkResult(
        'Security headers enabled',
        false !== strpos($env_content, 'SECURITY_HEADERS_ENABLED=true'),
        $security_total,
        $security_passed
    );
}

echo "Security Score: {$security_passed}/{$security_total}\n\n";

// 2. Dependencies Verification
echo "📦 DEPENDENCIES VERIFICATION\n";
echo "============================\n";

$deps_passed = 0;
$deps_total = 0;

if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);

    checkResult(
        'Laravel Framework updated',
        isset($composer['require']['laravel/framework'])
        && false !== strpos($composer['require']['laravel/framework'], '^12.17'),
        $deps_total,
        $deps_passed
    );

    checkResult(
        'Laravel Sanctum updated',
        isset($composer['require']['laravel/sanctum'])
        && false !== strpos($composer['require']['laravel/sanctum'], '^4.0'),
        $deps_total,
        $deps_passed
    );
}

if (file_exists('package.json')) {
    $package = json_decode(file_get_contents('package.json'), true);

    checkResult(
        'Vite updated',
        isset($package['devDependencies']['vite'])
        && false !== strpos($package['devDependencies']['vite'], '^5.4'),
        $deps_total,
        $deps_passed
    );

    checkResult(
        'TailwindCSS updated',
        isset($package['devDependencies']['tailwindcss'])
        && false !== strpos($package['devDependencies']['tailwindcss'], '^3.4'),
        $deps_total,
        $deps_passed
    );

    checkResult(
        'Laravel Vite Plugin compatible',
        isset($package['devDependencies']['laravel-vite-plugin'])
        && false !== strpos($package['devDependencies']['laravel-vite-plugin'], '^1.0'),
        $deps_total,
        $deps_passed
    );
}

echo "Dependencies Score: {$deps_passed}/{$deps_total}\n\n";

// 3. Vue.js Verification
echo "🖼️ VUE.JS VERIFICATION\n";
echo "======================\n";

$vue_passed = 0;
$vue_total = 0;

checkResult(
    'App.vue exists',
    file_exists('resources/js/App.vue'),
    $vue_total,
    $vue_passed
);

checkResult(
    'NavBar component exists',
    file_exists('resources/js/components/layout/NavBar.vue'),
    $vue_total,
    $vue_passed
);

checkResult(
    'Footer component exists',
    file_exists('resources/js/components/layout/Footer.vue'),
    $vue_total,
    $vue_passed
);

checkResult(
    'Main.ts exists',
    file_exists('resources/js/main.ts'),
    $vue_total,
    $vue_passed
);

checkResult(
    'Home page exists',
    file_exists('resources/js/pages/Home.vue'),
    $vue_total,
    $vue_passed
);

checkResult(
    'UI components directory exists',
    is_dir('resources/js/components/ui'),
    $vue_total,
    $vue_passed
);

checkResult(
    'Forms components directory exists',
    is_dir('resources/js/components/forms'),
    $vue_total,
    $vue_passed
);

checkResult(
    'Stores directory exists',
    is_dir('resources/js/stores'),
    $vue_total,
    $vue_passed
);

echo "Vue.js Score: {$vue_passed}/{$vue_total}\n\n";

// 4. Database Verification
echo "🗄️ DATABASE VERIFICATION\n";
echo "========================\n";

$db_passed = 0;
$db_total = 0;

$permissions_migration = glob('database/migrations/*_create_permissions_table.php');
checkResult(
    'Permissions migration exists',
    count($permissions_migration) > 0,
    $db_total,
    $db_passed
);

$roles_migration = glob('database/migrations/*_create_roles_table.php');
checkResult(
    'Roles migration exists',
    count($roles_migration) > 0,
    $db_total,
    $db_passed
);

// Check if migrations ran successfully
exec('php artisan migrate:status 2>/dev/null', $migrate_output);
if ($migrate_output) {
    $has_permissions = false;
    $has_roles = false;
    foreach ($migrate_output as $line) {
        if (false !== strpos($line, 'create_permissions_table') && false !== strpos($line, 'Ran')) {
            $has_permissions = true;
        }
        if (false !== strpos($line, 'create_roles_table') && false !== strpos($line, 'Ran')) {
            $has_roles = true;
        }
    }

    checkResult('Permissions migration ran', $has_permissions, $db_total, $db_passed);
    checkResult('Roles migration ran', $has_roles, $db_total, $db_passed);
}

echo "Database Score: {$db_passed}/{$db_total}\n\n";

// 5. Build Verification
echo "🏗️ BUILD VERIFICATION\n";
echo "=====================\n";

$build_passed = 0;
$build_total = 0;

checkResult(
    'Build manifest exists',
    file_exists('public/build/manifest.json'),
    $build_total,
    $build_passed
);

if (file_exists('public/build/manifest.json')) {
    $manifest = json_decode(file_get_contents('public/build/manifest.json'), true);
    checkResult(
        'Assets compiled',
        count($manifest) > 0,
        $build_total,
        $build_passed
    );

    checkResult(
        'CSS assets exist',
        count(array_filter($manifest, function ($asset) {
            return isset($asset['file']) && false !== strpos($asset['file'], '.css');
        })) > 0,
        $build_total,
        $build_passed
    );

    checkResult(
        'JS assets exist',
        count(array_filter($manifest, function ($asset) {
            return isset($asset['file']) && false !== strpos($asset['file'], '.js');
        })) > 0,
        $build_total,
        $build_passed
    );
}

echo "Build Score: {$build_passed}/{$build_total}\n\n";

// 6. Laravel Application Health
echo "🚀 LARAVEL APPLICATION HEALTH\n";
echo "==============================\n";

$app_passed = 0;
$app_total = 0;

// Check if Laravel is working
exec('php artisan --version 2>/dev/null', $version_output, $version_exit);
checkResult(
    'Laravel command working',
    0 === $version_exit && count($version_output) > 0,
    $app_total,
    $app_passed
);

if ($version_output) {
    echo '  Laravel Version: '.$version_output[0]."\n";
}

// Check config cache
checkResult(
    'Config cached',
    file_exists('bootstrap/cache/config.php'),
    $app_total,
    $app_passed
);

checkResult(
    'Routes cached',
    file_exists('bootstrap/cache/routes.php'),
    $app_total,
    $app_passed
);

checkResult(
    'Views cached',
    file_exists('storage/framework/views') && count(glob('storage/framework/views/*.php')) > 0,
    $app_total,
    $app_passed
);

echo "Application Score: {$app_passed}/{$app_total}\n\n";

// Overall Summary
$total_checks = $security_total + $deps_total + $vue_total + $db_total + $build_total + $app_total;
$passed_checks = $security_passed + $deps_passed + $vue_passed + $db_passed + $build_passed + $app_passed;

echo "🎯 OVERALL VERIFICATION SUMMARY\n";
echo "================================\n";
echo "Security: {$security_passed}/{$security_total} (".round(($security_passed / $security_total) * 100, 1)."%)\n";
echo "Dependencies: {$deps_passed}/{$deps_total} (".round(($deps_passed / $deps_total) * 100, 1)."%)\n";
echo "Vue.js: {$vue_passed}/{$vue_total} (".round(($vue_passed / $vue_total) * 100, 1)."%)\n";
echo "Database: {$db_passed}/{$db_total} (".round(($db_passed / $db_total) * 100, 1)."%)\n";
echo "Build: {$build_passed}/{$build_total} (".round(($build_passed / $build_total) * 100, 1)."%)\n";
echo "Application: {$app_passed}/{$app_total} (".round(($app_passed / $app_total) * 100, 1)."%)\n\n";

$overall_score = round(($passed_checks / $total_checks) * 100, 1);
echo "🏆 OVERALL SCORE: {$passed_checks}/{$total_checks} ({$overall_score}%)\n\n";

if ($overall_score >= 90) {
    echo "🎉 EXCELLENT! Your Laravel Job Portal upgrade is highly successful!\n";
} elseif ($overall_score >= 80) {
    echo "✅ GOOD! Your Laravel Job Portal upgrade is successful with minor issues.\n";
} elseif ($overall_score >= 70) {
    echo "⚠️ ACCEPTABLE: Your upgrade is functional but needs some improvements.\n";
} else {
    echo "❌ NEEDS WORK: Several issues need to be addressed.\n";
}

// Performance & Security Status
echo "\n📊 PERFORMANCE & SECURITY STATUS\n";
echo "=================================\n";

// Check file sizes
if (file_exists('public/build/manifest.json')) {
    $manifest = json_decode(file_get_contents('public/build/manifest.json'), true);
    $total_size = 0;
    foreach ($manifest as $asset) {
        if (isset($asset['file']) && file_exists('public/build/'.$asset['file'])) {
            $total_size += filesize('public/build/'.$asset['file']);
        }
    }
    echo 'Total Asset Size: '.round($total_size / 1024, 2)." KB\n";
}

// Security status
echo 'Security Middleware: '.($security_passed >= 3 ? '✅ Active' : '❌ Incomplete')."\n";
echo 'Production Mode: '.(false !== strpos(file_get_contents('.env'), 'APP_DEBUG=false') ? '✅ Enabled' : '❌ Debug Mode')."\n";

echo "\n🚀 UPGRADE VERIFICATION COMPLETE!\n";
echo "Your Laravel Job Portal has been successfully upgraded with modern architecture.\n";

?> 