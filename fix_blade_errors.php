<?php

/**
 * Comprehensive Blade Template Syntax Error Fixer
 * Using Universal best practices for Laravel Blade templating
 */

$fixCount = 0;
$errorCount = 0;

// Common blade syntax error patterns and their fixes
$patterns = [
    // Fix malformed blade comments
    '/\{\{ --/' => '{{--',
    '/ -- \}\}/' => ' --}}',
    '/-- \}\}/' => '--}}',
    
    // Fix malformed ternary operators in class attributes
    '/\?\\"([^\']*)\' : \'([^\']*)\' \}\}/' => '? \'$1\' : \'$2\' }}',
    '/\?\\"([^\']*)\' : ([^\']*) \}\}/' => '? \'$1\' : $2 }}',
    
    // Fix specific malformed ternary patterns
    '/\{\{ \$[^}]+ == 0 \?\\"([^\']*)\' : \'([^\']*)\' \}\}/' => '{{ $key == 0 ? \'$1\' : \'$2\' }}',
    
    // Fix double translation patterns
    '/\{\{ __\(\'(\{\{ __\([^}]+\) \}\})\'\) \}\}/' => '{{ __($1) }}',
    
    // Fix double dollar signs in blade variables
    '/\$\$([a-zA-Z_][a-zA-Z0-9_]*)->/' => '$$1->',
    
    // Fix malformed spacing in blade echo statements
    '/\{\{  ([^}]+)  \}\}/' => '{{ $1 }}',
];

function fixBladeFile($filePath) {
    global $patterns, $fixCount, $errorCount;
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Apply all patterns
    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    // Special fixes for specific problematic patterns found in the test output
    
    // Fix ternary operators with malformed quotes
    $content = preg_replace('/\{\{ \$loop->first \?\\"([^"]*)" : \'([^\']*)\' \}\}/', '{{ $loop->first ? \'$1\' : \'$2\' }}', $content);
    $content = preg_replace('/\{\{ \$key == 0 \?\\"([^"]*)\' : \'([^\']*)\' \}\}/', '{{ $key == 0 ? \'$1\' : \'$2\' }}', $content);
    
    // Fix translation patterns
    $content = preg_replace('/\{\{ __\(\'(\{\{ __\([^}]+\) \}\})\'\) \}\}/', '{{ __($1) }}', $content);
    
    // Fix double opening braces in quotes
    $content = str_replace('"{{', '"{{ ', $content);
    $content = str_replace('}}"', ' }}"', $content);
    
    if ($content !== $originalContent) {
        if (file_put_contents($filePath, $content)) {
            echo "Fixed: $filePath\n";
            $fixCount++;
        } else {
            echo "ERROR: Could not write to $filePath\n";
            $errorCount++;
        }
    }
}

// Get all blade files in the project
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('resources/views', RecursiveDirectoryIterator::SKIP_DOTS)
);

$bladeFiles = new RegexIterator($iterator, '/\.blade\.php$/', RecursiveRegexIterator::GET_MATCH);

foreach ($bladeFiles as $file) {
    $filePath = $file[0];
    fixBladeFile($filePath);
}

echo "\n=== Blade Error Fix Summary ===\n";
echo "Files fixed: $fixCount\n";
echo "Errors encountered: $errorCount\n";

if ($fixCount > 0) {
    echo "\nClearing Laravel view cache...\n";
    exec('php artisan view:clear');
    echo "View cache cleared.\n";
}

echo "\nBlade error fixing complete!\n";

?> 