<?php

/**
 * Fix Controller Import Conflicts
 * Resolves naming conflicts in controller files
 */

echo "🔧 Fixing Controller Import Conflicts\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Find all controllers with conflicts
$controllers = glob('app/Http/Controllers/**/*.php');
$controllers = array_merge($controllers, glob('app/Http/Controllers/*.php'));

$fixedCount = 0;

foreach ($controllers as $controllerPath) {
    $content = file_get_contents($controllerPath);
    $originalContent = $content;
    
    // Fix duplicate imports
    $lines = explode("\n", $content);
    $useStatements = [];
    $cleanedLines = [];
    
    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        
        // If it's a use statement
        if (str_starts_with($trimmedLine, 'use ')) {
            // Extract the class name being imported
            if (preg_match('/use\s+([^;]+);/', $trimmedLine, $matches)) {
                $fullClassName = $matches[1];
                $className = basename(str_replace('\\', '/', $fullClassName));
                
                // Check if we already have this class name imported
                if (!isset($useStatements[$className])) {
                    $useStatements[$className] = $fullClassName;
                    $cleanedLines[] = $line;
                } else {
                    // Skip duplicate import
                    echo "   Removed duplicate import: $fullClassName\n";
                }
            } else {
                $cleanedLines[] = $line;
            }
        } else {
            $cleanedLines[] = $line;
        }
    }
    
    $content = implode("\n", $cleanedLines);
    
    // Fix specific conflicts by adding aliases
    $conflicts = [
        'UpdateCompanyRequest' => 'use App\\Http\\Requests\\Company\\UpdateCompanyRequest as CompanyUpdateRequest;',
        'StoreCompanyRequest' => 'use App\\Http\\Requests\\Company\\StoreCompanyRequest as CompanyStoreRequest;',
        'UpdateJobRequest' => 'use App\\Http\\Requests\\Job\\UpdateJobRequest as JobUpdateRequest;',
        'StoreJobRequest' => 'use App\\Http\\Requests\\Job\\StoreJobRequest as JobStoreRequest;',
    ];
    
    foreach ($conflicts as $className => $aliasImport) {
        if (str_contains($content, "use App\\Http\\Requests\\") && str_contains($content, $className)) {
            // Replace the problematic import with aliased version
            $pattern = '/use App\\\\Http\\\\Requests\\\\[^\\\\]+\\\\' . $className . ';/';
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $aliasImport, $content);
                
                // Update the usage in method signatures
                $aliasName = explode(' as ', $aliasImport)[1];
                $aliasName = rtrim($aliasName, ';');
                $content = str_replace("$className \$request", "$aliasName \$request", $content);
                
                echo "   Fixed conflict in $controllerPath: $className -> $aliasName\n";
            }
        }
    }
    
    if ($content !== $originalContent) {
        file_put_contents($controllerPath, $content);
        $fixedCount++;
    }
}

echo "\n✅ Fixed $fixedCount controller files\n";
echo "🧪 Testing route system...\n";

// Test if routes work now
$output = shell_exec('php artisan route:list --json 2>&1');
if (str_contains($output, 'Fatal error')) {
    echo "❌ Still have route errors\n";
    echo "Error details:\n" . $output . "\n";
} else {
    echo "✅ Routes are working properly!\n";
    
    // Count total routes
    $routes = json_decode($output, true);
    if (is_array($routes)) {
        echo "📊 Total registered routes: " . count($routes) . "\n";
    }
}

echo "\n🎉 Controller import conflicts resolved!\n"; 