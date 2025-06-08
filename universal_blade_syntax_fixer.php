<?php

/**
 * Universal Blade Syntax Fixer
 * Fixes critical syntax errors in blade templates that prevent tests from running
 */

$criticalFixes = [
    // Fix malformed array_column function
    'array_flex-1 px-4umn' => 'array_column',
    
    // Fix malformed include statements
    'job_bg-white overflow-hidden shadow rounded-lg' => 'job_card',
    'company_bg-white overflow-hidden shadow rounded-lg' => 'company_card',
    
    // Fix malformed CSS classes in divs
    'flex-1 md-12' => 'col-md-12',
    'flex-1 -12' => 'col-md-12',
    'flex-1 -6' => 'col-md-6',
    'flex-1 -8' => 'col-md-8',
    'flex-1 -11' => 'col-md-11',
    
    // Fix malformed translation keys
    'bflex flex-wrap -mx-4se_all' => 'browse_all',
    
    // Fix malformed routes
    "route('front.')" => "route('front.search-jobs')",
    "route('front.', \$post->id)" => "route('posts.details', \$post->id)",
    
    // Fix malformed CSS class combinations
    'bg-flex-1 px-4or-light' => 'bg-color-light',
    'overflow-hidden shadow rounded bg-white job- -lg' => 'card job-card',
    'overflow-hidden shadow rounded bg-white blog- -lg' => 'card blog-card',
    'overflow-hidden shadow rounded bg-white -lg' => 'card',
    'job- bg-white shadow rounded -lg' => 'card job-card',
    'blog- bg-white shadow rounded -lg' => 'card blog-card',
    'testimonial- bg-white shadow rounded -lg' => 'card testimonial-card',
    
    // Fix broken class attributes
    'px-4-sm-10' => 'px-sm-4',
    'mb-4 px-4-sm-10 flex-1 -11' => 'mb-4 px-sm-4 col-md-11',
];

$bladeFiles = [
    'resources/views/front_web/home/home.blade.php',
    'resources/views/front_web_template/home/home.blade.php',
];

echo "🔧 Starting Universal Blade Syntax Fixer...\n";

foreach ($bladeFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    echo "🛠️  Fixing $file...\n";
    
    $content = file_get_contents($file);
    $originalContent = $content;
    $fixesApplied = 0;
    
    foreach ($criticalFixes as $search => $replace) {
        $newContent = str_replace($search, $replace, $content);
        if ($newContent !== $content) {
            $content = $newContent;
            $fixesApplied++;
            echo "   ✅ Fixed: $search → $replace\n";
        }
    }
    
    if ($fixesApplied > 0) {
        file_put_contents($file, $content);
        echo "   📝 Applied $fixesApplied fixes to $file\n";
    } else {
        echo "   ✨ No fixes needed for $file\n";
    }
}

echo "\n🧹 Clearing Laravel caches...\n";
system('php artisan view:clear');
system('php artisan config:clear');
system('php artisan cache:clear');

echo "\n✅ Universal Blade Syntax Fixer completed!\n";
echo "📋 Summary: Fixed critical syntax errors in blade templates\n";
echo "🎯 Tests should now be able to run without compilation errors\n"; 