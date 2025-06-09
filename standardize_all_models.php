<?php

// Script to standardize Laravel models in app/Models directory

// Ensure we're in the correct directory
chdir(__DIR__);

// Directory containing the models
$modelDir = 'app/Models';

// Backup directory
$backupDir = 'app/Models/backup_' . date('Ymd_His');
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Function to check if a file contains a casts method and convert it to property
$standardizeFile = function($filePath, $backupPath) {
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "Failed to read $filePath\n";
        return false;
    }
    // Backup the original file
    copy($filePath, $backupPath);
    // Check if there's a casts() method
    if (preg_match('/protected function casts\(\): array\s*{[^}]*}/s', $content, $matches)) {
        $castContent = $matches[0];
        // Extract the array content
        if (preg_match('/return \[([^\]]*)];/s', $castContent, $arrayMatch)) {
            $castArray = $arrayMatch[1];
            // Replace the method with a property
            $newCastProperty = "protected \$casts = [$castArray];";
            // Replace the old method with the new property
            $newContent = str_replace($castContent, $newCastProperty, $content);
            // Write the updated content back to the file
            if (file_put_contents($filePath, $newContent) !== false) {
                echo "Updated casts in $filePath\n";
                return true;
            } else {
                echo "Failed to write to $filePath\n";
                return false;
            }
        }
    }
    return false;
};

// Process each PHP file in the models directory
$files = glob("$modelDir/*.php");
foreach ($files as $file) {
    $filename = basename($file);
    if (strpos($filename, '.backup') === false) { // Skip backup files
        $backupPath = "$backupDir/$filename";
        $standardizeFile($file, $backupPath);
    }
}

echo "Standardization complete. Backups created in $backupDir\n"; 