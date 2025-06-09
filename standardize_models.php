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

// Standard elements to check or add
$standardCasts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
];

$standardScopes = [
    'scopeActive' => [
        'signature' => 'public function scopeActive($query)',
        'body' => "return \$query->where('is_active', true);"
    ],
    'scopeInactive' => [
        'signature' => 'public function scopeInactive($query)',
        'body' => "return \$query->where('is_active', false);"
    ],
    'scopeRecent' => [
        'signature' => 'public function scopeRecent($query, int $days = 30)',
        'body' => "return \$query->where('created_at', '>=', \Carbon\Carbon::now()->subDays(\$days));"
    ],
    'scopeSearch' => [
        'signature' => 'public function scopeSearch($query, string $search)',
        'body' => "return \$query->where('name', 'like', '%' . \$search . '%')
                     ->orWhere('title', 'like', '%' . \$search . '%');"
    ],
];

// Function to check if a string exists in file content
function hasString($content, $string) {
    return strpos($content, $string) !== false;
}

// Function to add casting if not exists
function addCasting($content, $castArray) {
    $castStart = strpos($content, 'protected $casts = [');
    if ($castStart === false) {
        // If no casts array, add it after fillable
        $fillableEnd = strpos($content, '];', strpos($content, 'protected $fillable = ['));
        if ($fillableEnd !== false) {
            $newCasts = "\n    /**\n     * The attributes that should be cast.\n     *\n     * @var array<string, string>\n     */\n    protected \$casts = [\n";
            foreach ($castArray as $attr => $type) {
                $newCasts .= "        '$attr' => '$type',\n";
            }
            $newCasts .= "    ];\n";
            $content = substr_replace($content, $newCasts, $fillableEnd + 2, 0);
        }
    } else {
        // Add missing casts to existing array
        $castEnd = strpos($content, '];', $castStart);
        $existingCasts = substr($content, $castStart, $castEnd - $castStart + 2);
        $newCasts = $existingCasts;
        foreach ($castArray as $attr => $type) {
            if (strpos($existingCasts, "'$attr' =>") === false) {
                $newCasts = str_replace('];', "        '$attr' => '$type',\n    ];", $newCasts);
            }
        }
        $content = substr_replace($content, $newCasts, $castStart, $castEnd - $castStart + 2);
    }
    return $content;
}

// Function to add scopes if not exists
function addScopes($content, $scopes) {
    $classEnd = strrpos($content, '}');
    if ($classEnd !== false) {
        $newScopes = "\n    // Scopes\n";
        foreach ($scopes as $scope) {
            if (!hasString($content, $scope['signature'])) {
                $newScopes .= "\n    /**\n     * Scope a query to only include " . str_replace('scope', '', strtolower($scope['signature'])) . ".\n     *\n     * @param  \Illuminate\Database\Eloquent\Builder  \$query\n     * @return \Illuminate\Database\Eloquent\Builder\n     */\n    " . $scope['signature'] . "\n    {\n        " . $scope['body'] . "\n    }\n";
            }
        }
        if ($newScopes !== "\n    // Scopes\n") {
            $content = substr_replace($content, $newScopes, $classEnd, 0);
        }
    }
    return $content;
}

// Get all PHP files in the models directory
$files = glob("$modelDir/*.php");

echo "Found " . count($files) . " model files to process.\n";

foreach ($files as $file) {
    $filename = basename($file);
    echo "Processing: $filename\n";
    
    // Backup the original file
    copy($file, "$backupDir/$filename");
    
    // Read file content
    $content = file_get_contents($file);
    
    // Check and add necessary use statements
    if (!hasString($content, 'use Illuminate\Support\Carbon;')) {
        $namespaceEnd = strpos($content, 'namespace App\Models;') + strlen('namespace App\Models;');
        $content = substr_replace($content, "\nuse Illuminate\Support\Carbon;", $namespaceEnd, 0);
    }
    
    // Add standard casting if missing
    $content = addCasting($content, $standardCasts);
    
    // Add standard scopes if missing
    $content = addScopes($content, $standardScopes);
    
    // Write updated content back to file
    file_put_contents($file, $content);
    echo "Updated: $filename\n";
}

echo "Standardization complete. Original files are backed up in $backupDir.\n";
?> 