<?php
/**
 * Comprehensive Controller Fixer
 * Fixes all controller syntax issues, duplicate use statements, and malformed code
 */

function fixController($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Remove malformed use statements inside class
    $content = preg_replace('/^[\s]*use App\\\\Http\\\\Requests[^;]*;[\s]*$/m', '', $content);
    
    // Split content into lines for processing
    $lines = explode("\n", $content);
    $cleanedLines = [];
    $useStatements = [];
    $inClass = false;
    
    foreach ($lines as $lineNum => $line) {
        $trimmed = trim($line);
        
        // Track if we're inside a class
        if (preg_match('/^class\s+\w+/', $trimmed)) {
            $inClass = true;
        }
        
        // Handle use statements
        if (str_starts_with($trimmed, 'use ') && str_ends_with($trimmed, ';')) {
            // Skip use statements inside class
            if ($inClass) {
                continue;
            }
            
            // Avoid duplicates
            if (!in_array($trimmed, $useStatements)) {
                $useStatements[] = $trimmed;
                $cleanedLines[] = $line;
            }
        } else {
            $cleanedLines[] = $line;
        }
    }
    
    $content = implode("\n", $cleanedLines);
    
    // Fix specific syntax issues
    $content = preg_replace('/\$request->validated\(\),\s*\]\);/', '$request->validated();', $content);
    $content = preg_replace('/\$request->validated\(\);\s*\$request->validated\(\);/', '$request->validated();', $content);
    
    // Remove empty lines between use statements and class
    $content = preg_replace('/(\nuse [^;]+;)\n+(\nclass|\nnamespace)/', '$1$2', $content);
    
    // Clean up multiple empty lines
    $content = preg_replace('/\n{3,}/', "\n\n", $content);
    
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        return true;
    }
    
    return false;
}

// Get all PHP files in Controllers directory
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('app/Http/Controllers')
);

$fixedCount = 0;
$totalCount = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $totalCount++;
        $filePath = $file->getPathname();
        
        if (fixController($filePath)) {
            $fixedCount++;
            echo "✅ Fixed: " . str_replace('app/Http/Controllers/', '', $filePath) . "\n";
        }
    }
}

echo "\n🎯 CONTROLLER FIX SUMMARY:\n";
echo "  📁 Total Controllers: {$totalCount}\n";
echo "  🔧 Fixed Controllers: {$fixedCount}\n";
echo "  ✅ Clean Controllers: " . ($totalCount - $fixedCount) . "\n\n";

echo "🚀 All controllers have been processed!\n"; 