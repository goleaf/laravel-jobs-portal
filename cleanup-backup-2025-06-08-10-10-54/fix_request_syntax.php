<?php
/**
 * Fix Request Files Syntax
 * Converts JavaScript-style array syntax {} to PHP array syntax []
 */

function fixRequestFile($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Fix JavaScript-style array syntax in return statements
    $content = preg_replace_callback(
        '/return\s*\{\s*(.*?)\s*\};/s',
        function($matches) {
            $arrayContent = $matches[1];
            
            // Convert JavaScript object notation to PHP array notation
            $arrayContent = preg_replace('/"([^"]+)":\s*"([^"]*)"/', '"$1" => "$2"', $arrayContent);
            $arrayContent = preg_replace('/"([^"]+)":\s*([^",\}]+)/', '"$1" => $2', $arrayContent);
            
            return "return [\n            " . trim($arrayContent) . "\n        ];";
        },
        $content
    );
    
    // Clean up formatting
    $content = preg_replace('/,\s*\n\s*\]/', "\n        ]", $content);
    
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        return true;
    }
    
    return false;
}

// Get all PHP files in Requests directory
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('app/Http/Requests')
);

$fixedCount = 0;
$totalCount = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $totalCount++;
        $filePath = $file->getPathname();
        
        if (fixRequestFile($filePath)) {
            $fixedCount++;
            echo "✅ Fixed: " . str_replace('app/Http/Requests/', '', $filePath) . "\n";
        }
    }
}

echo "\n🎯 REQUEST FILES FIX SUMMARY:\n";
echo "  📁 Total Request Files: {$totalCount}\n";
echo "  🔧 Fixed Request Files: {$fixedCount}\n";
echo "  ✅ Clean Request Files: " . ($totalCount - $fixedCount) . "\n\n";

echo "🚀 All request files have been processed!\n"; 