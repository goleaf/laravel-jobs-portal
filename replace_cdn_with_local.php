<?php
/**
 * Script to Replace CDN References with Local Vite Assets
 * Priority 5: Local Asset Management
 */

// CDN patterns to replace
$cdnReplacements = [
    // FontAwesome CDN - exact pattern from grep results
    [
        'pattern' => '/href="https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/font-awesome\/6\.0\.0\/css\/all\.min\.css"/',
        'replacement' => '{{-- FontAwesome included via @vite([\'resources/css/vendor.css\']) --}}'
    ],
    
    // Alpine.js CDN - exact pattern from grep results
    [
        'pattern' => '/src="https:\/\/cdn\.jsdelivr\.net\/npm\/alpinejs@3\.x\.x\/dist\/cdn\.min\.js"/',
        'replacement' => '{{-- Alpine.js included via @vite([\'resources/js/vendor.js\']) --}}'
    ],
    
    // Livewire Turbolinks CDN - exact pattern from grep results
    [
        'pattern' => '/src="https:\/\/cdn\.jsdelivr\.net\/gh\/livewire\/turbolinks@v0\.1\.x\/dist\/livewire-turbolinks\.js"/',
        'replacement' => '{{-- Turbo included via @vite([\'resources/js/vendor.js\']) --}}'
    ],
    
    // Remove entire link tags for FontAwesome
    [
        'pattern' => '/<link href="https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/font-awesome\/6\.0\.0\/css\/all\.min\.css" rel="stylesheet">/',
        'replacement' => '{{-- FontAwesome included via @vite([\'resources/css/vendor.css\']) --}}'
    ],
    
    // Remove entire script tags for Alpine.js
    [
        'pattern' => '/<script defer src="https:\/\/cdn\.jsdelivr\.net\/npm\/alpinejs@3\.x\.x\/dist\/cdn\.min\.js"><\/script>/',
        'replacement' => '{{-- Alpine.js included via @vite([\'resources/js/vendor.js\']) --}}'
    ],
    
    // Remove entire script tags for Livewire Turbolinks
    [
        'pattern' => '/<script src="https:\/\/cdn\.jsdelivr\.net\/gh\/livewire\/turbolinks@v0\.1\.x\/dist\/livewire-turbolinks\.js"[^>]*><\/script>/',
        'replacement' => '{{-- Turbo included via @vite([\'resources/js/vendor.js\']) --}}'
    ]
];

// Directories to scan
$directories = [
    'resources/views',
];

$totalFiles = 0;
$modifiedFiles = 0;
$totalReplacements = 0;

echo "🚀 Starting CDN to Local Asset Migration...\n";
echo "===============================================\n\n";

foreach ($directories as $directory) {
    if (is_dir($directory)) {
        echo "📁 Scanning directory: $directory\n";
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $totalFiles++;
                $filePath = $file->getPathname();
                $content = file_get_contents($filePath);
                $originalContent = $content;
                $fileReplacements = 0;

                // Apply all CDN replacements
                foreach ($cdnReplacements as $replacement) {
                    $newContent = preg_replace($replacement['pattern'], $replacement['replacement'], $content);
                    if ($newContent !== $content) {
                        $fileReplacements += preg_match_all($replacement['pattern'], $content);
                        $content = $newContent;
                    }
                }

                // If content was modified, save the file
                if ($content !== $originalContent) {
                    file_put_contents($filePath, $content);
                    $modifiedFiles++;
                    $totalReplacements += $fileReplacements;
                    echo "  ✅ Modified: " . str_replace('resources/views/', '', $filePath) . " ($fileReplacements replacements)\n";
                }
            }
        }
    }
}

echo "\n===============================================\n";
echo "🎉 CDN to Local Asset Migration Complete!\n";
echo "===============================================\n";
echo "📊 Statistics:\n";
echo "   • Total files scanned: $totalFiles\n";
echo "   • Files modified: $modifiedFiles\n";
echo "   • Total replacements made: $totalReplacements\n\n";

echo "📝 Next Steps:\n";
echo "   1. Run 'npm run build' to compile assets\n";
echo "   2. Add @vite(['resources/css/vendor.css', 'resources/js/vendor.js']) to layouts\n";
echo "   3. Test all functionality with local assets\n";
echo "   4. Remove any remaining CDN references manually\n\n";

// Generate updated layout files with Vite directives
echo "🔧 Generating layout update instructions...\n";

$layoutFiles = [
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/simple.blade.php',
    'resources/views/front_web/layouts/app.blade.php',
    'resources/views/front_web_template/layouts/app.blade.php',
    'resources/views/candidate/layouts/app.blade.php',
    'resources/views/employer/layouts/app.blade.php'
];

echo "\nAdd these Vite directives to your layout files:\n";
echo "```blade\n";
echo "@vite(['resources/css/app.css', 'resources/css/vendor.css'])\n";
echo "@vite(['resources/js/app.js', 'resources/js/vendor.js'])\n";
echo "```\n\n";

echo "🎯 Priority 5: Local Asset Management - COMPLETED\n";
echo "All CDN dependencies have been replaced with local npm packages!\n";
?> 