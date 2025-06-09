<?php

/**
 * Script to standardize all models with casts, scopes, and traits following Laravel best practices and Context7 patterns.
 * This script will only update models if necessary, preserving existing functionality.
 */

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

// Define the base path for models
$modelPath = app_path('Models');

// Get all PHP files in the Models directory
$modelFiles = File::allFiles($modelPath);

// Define standard traits and scopes to check/add
define('STANDARD_TRAITS', [
    'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
]);

define('STANDARD_SCOPES', [
    'active' => 'where("is_active", true)',
    'inactive' => 'where("is_active", false)',
    'recent' => 'orderBy("created_at", "desc")',
    'old' => 'orderBy("created_at", "asc")',
    'popular' => 'orderBy("views_count", "desc")',
    'featured' => 'where("is_featured", true)',
]);

define('STANDARD_CASTS', [
    'is_active' => 'boolean',
    'is_featured' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
]);

echo "Starting model standardization process...\n";

foreach ($modelFiles as $file) {
    if ($file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        $content = file_get_contents($filePath);
        $modelName = $file->getFilenameWithoutExtension();
        echo "Processing: $modelName\n";

        // Check for namespace and class definition
        if (!preg_match('/namespace App\\\Models;/', $content)) {
            echo " - Skipping: Incorrect namespace or not a model file.\n";
            continue;
        }

        // Check and add traits if missing
        foreach (STANDARD_TRAITS as $trait) {
            $traitName = basename(str_replace('\\', '/', $trait));
            if (strpos($content, "use $trait;") === false && strpos($content, "use $traitName;") === false) {
                echo " - Adding trait: $traitName\n";
                $content = preg_replace(
                    '/(class \w+ extends \w+\s*{)/',
                    "\1\n    use $trait;\n",
                    $content
                );
            }
        }

        // Check and add casts if missing or incomplete
        if (strpos($content, 'protected function casts(): array') === false && strpos($content, 'protected $casts = [') === false) {
            echo " - Adding casts method.\n";
            $castsCode = "    protected function casts(): array\n    {\n        return [\n";
            foreach (STANDARD_CASTS as $attribute => $type) {
                $castsCode .= "            '$attribute' => '$type',\n";
            }
            $castsCode .= "        ];\n    }\n";
            $content = preg_replace(
                '/(class \w+ extends \w+\s*{[\s\S]*?)(})/',
                "\1\n$castsCode\n\2",
                $content
            );
        } elseif (strpos($content, 'protected $casts = [') !== false) {
            echo " - Updating old-style casts to new casts() method.\n";
            preg_match('/protected \$casts = \[(.*?)\];/s', $content, $matches);
            $existingCasts = isset($matches[1]) ? $matches[1] : '';
            $castsCode = "    protected function casts(): array\n    {\n        return [\n";
            foreach (STANDARD_CASTS as $attribute => $type) {
                if (strpos($existingCasts, "'$attribute' =>") === false) {
                    $castsCode .= "            '$attribute' => '$type',\n";
                }
            }
            $castsCode .= "$existingCasts\n        ];\n    }\n";
            $content = preg_replace(
                '/protected \$casts = \[(.*?)\];/s',
                $castsCode,
                $content
            );
        }

        // Check and add scopes if missing
        foreach (STANDARD_SCOPES as $scopeName => $scopeLogic) {
            $scopeMethod = 'scope' . Str::studly($scopeName);
            if (strpos($content, "public function $scopeMethod(") === false) {
                echo " - Adding scope: $scopeName\n";
                $scopeCode = "    /**\n     * Scope a query to only include $scopeName records.\n     *\n     * @param  \Illuminate\Database\Eloquent\Builder  \$query\n     * @return \Illuminate\Database\Eloquent\Builder\n     */\n    public function $scopeMethod(\Illuminate\Database\Eloquent\Builder \$query): \Illuminate\Database\Eloquent\Builder\n    {\n        return \$query->$scopeLogic;\n    }\n";
                $content = preg_replace(
                    '/(class \w+ extends \w+\s*{[\s\S]*?)(})/',
                    "\1\n$scopeCode\n\2",
                    $content
                );
            }
        }

        // Write the updated content back to the file
        file_put_contents($filePath, $content);
        echo " - Updated: $modelName\n";
    }
}

echo "Model standardization process completed.\n"; 