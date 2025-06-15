<?php

/**
 * Context7 PSR-4 Namespace Fix Script
 * Systematically fixes all PSR-4 autoloading violations.
 */
class Context7PSR4NamespaceFixer
{
    private array $fixedFiles = [];
    private array $errors = [];

    public function __construct()
    {
        echo "🚀 Context7 PSR-4 Namespace Fixer Started\n";
        echo "========================================\n\n";
    }

    /**
     * Main execution method.
     */
    public function fixAllNamespaces(): void
    {
        $this->fixRequestNamespaces();
        $this->fixTestNamespaces();
        $this->generateReport();
    }

    /**
     * Fix app/Http/Requests/* namespace violations.
     */
    private function fixRequestNamespaces(): void
    {
        echo "📁 Fixing Request Class Namespaces...\n";

        $directories = [
            'app/Http/Requests/Job' => 'App\Http\Requests\Job',
            'app/Http/Requests/Admin' => 'App\Http\Requests\Admin',
            'app/Http/Requests/Candidate' => 'App\Http\Requests\Candidate',
            'app/Http/Requests/Location' => 'App\Http\Requests\Location',
            'app/Http/Requests/MasterData' => 'App\Http\Requests\MasterData',
            'app/Http/Requests/Financial' => 'App\Http\Requests\Financial',
        ];

        foreach ($directories as $directory => $correctNamespace) {
            if (is_dir($directory)) {
                $this->fixDirectoryNamespaces($directory, $correctNamespace);
            }
        }
    }

    /**
     * Fix test directory namespace violations.
     */
    private function fixTestNamespaces(): void
    {
        echo "\n🧪 Fixing Test Class Namespaces...\n";

        // Move Universal2 to Universal
        if (is_dir('tests/Feature/Universal2')) {
            $this->moveDirectory('tests/Feature/Universal2', 'tests/Feature/Universal');
        }

        // Fix helpers directory
        if (is_dir('tests/Helpers')) {
            $this->moveDirectory('tests/Helpers', 'tests/Support');
        }
    }

    /**
     * Fix namespaces in a specific directory.
     */
    private function fixDirectoryNamespaces(string $directory, string $correctNamespace): void
    {
        $files = glob($directory.'/*.php');

        foreach ($files as $file) {
            $this->fixFileNamespace($file, $correctNamespace);
        }

        echo "   ✅ Fixed {$directory} (".count($files)." files)\n";
    }

    /**
     * Fix namespace in a specific file.
     */
    private function fixFileNamespace(string $filePath, string $correctNamespace): void
    {
        if (!file_exists($filePath)) {
            $this->errors[] = "File not found: {$filePath}";

            return;
        }

        $content = file_get_contents($filePath);
        $originalContent = $content;

        // Replace incorrect namespace declarations
        $incorrectPatterns = [
            '/^namespace App\\\Http\\\Requests;$/m',
            '/^namespace App\\\Http\\\Requests\\\;$/m',
        ];

        foreach ($incorrectPatterns as $pattern) {
            $content = preg_replace($pattern, "namespace {$correctNamespace};", $content);
        }

        // Only write if content changed
        if ($content !== $originalContent) {
            if (file_put_contents($filePath, $content)) {
                $this->fixedFiles[] = $filePath;
            } else {
                $this->errors[] = "Failed to write: {$filePath}";
            }
        }
    }

    /**
     * Move directory and update namespaces.
     */
    private function moveDirectory(string $source, string $destination): void
    {
        if (!is_dir($source)) {
            return;
        }

        // Create destination directory
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        // Move directory
        if (rename($source, $destination)) {
            echo "   ✅ Moved {$source} → {$destination}\n";

            // Update namespaces in moved files
            $files = glob($destination.'/*.php');
            foreach ($files as $file) {
                $this->updateMovedFileNamespace($file, $destination);
            }
        } else {
            $this->errors[] = "Failed to move directory: {$source} → {$destination}";
        }
    }

    /**
     * Update namespace for moved files.
     */
    private function updateMovedFileNamespace(string $filePath, string $newDirectory): void
    {
        $content = file_get_contents($filePath);

        // Convert directory path to namespace
        $namespace = str_replace(['/', '\\\\'], ['\\', '\\'], $newDirectory);
        $namespace = str_replace('tests', 'Tests', $namespace);
        $namespace = ucfirst($namespace);

        // Update namespace
        $content = preg_replace(
            '/^namespace Tests\\\Feature\\\Universal;$/m',
            "namespace {$namespace};",
            $content
        );

        file_put_contents($filePath, $content);
    }

    /**
     * Generate comprehensive report.
     */
    private function generateReport(): void
    {
        echo "\n📊 Context7 PSR-4 Fix Report\n";
        echo "===============================\n";
        echo '✅ Files Fixed: '.count($this->fixedFiles)."\n";
        echo '❌ Errors: '.count($this->errors)."\n";

        if (!empty($this->errors)) {
            echo "\n🚨 Errors Encountered:\n";
            foreach ($this->errors as $error) {
                echo "   - {$error}\n";
            }
        }

        echo "\n🎯 Next Steps:\n";
        echo "   1. Run: composer dump-autoload --optimize\n";
        echo "   2. Test application functionality\n";
        echo "   3. Verify zero PSR-4 warnings\n";

        echo "\n✅ Context7 PSR-4 Fix Complete!\n";
    }
}

// Execute the fixer
$fixer = new Context7PSR4NamespaceFixer();
$fixer->fixAllNamespaces();
