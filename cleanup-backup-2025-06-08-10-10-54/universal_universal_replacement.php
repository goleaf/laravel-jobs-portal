<?php
/**
 * Universal Universal Replacement System
 * Replaces all Universal references with Universal naming
 */

class UniversalReplacementSystem
{
    private array $replacements = [
        // Class names
        'Universal' => 'Universal',
        'universal' => 'universal',
        'UNIVERSAL' => 'UNIVERSAL',
        
        // File/directory patterns
        'Universal/' => 'Universal/',
        'universal/' => 'universal/',
        '/universal/' => '/universal/',
        
        // API patterns
        'universalApi' => 'universalApi',
        'universal_token' => 'universal_token',
        'universalUI' => 'universalUI',
        
        // CSS class patterns
        'universal-' => 'universal-',
        '.universal-' => '.universal-',
        
        // File name patterns
        'universal_' => 'universal_',
        'Universal_' => 'Universal_',
        
        // Comments and documentation
        'Universal ' => 'Universal ',
        'universal ' => 'universal ',
        'UNIVERSAL ' => 'UNIVERSAL ',
    ];

    private array $processedFiles = [];
    private array $renamedFiles = [];
    private array $renamedDirectories = [];

    public function execute(): void
    {
        echo "🔄 Starting Universal Universal Replacement System\n";
        echo "==============================================\n\n";

        // Step 1: Rename directories
        $this->renameDirectories();

        // Step 2: Rename files
        $this->renameFiles();

        // Step 3: Replace content in files
        $this->replaceFileContents();

        // Step 4: Update configuration files
        $this->updateConfigurationFiles();

        // Step 5: Generate report
        $this->generateReport();

        echo "\n🎉 Universal Universal Replacement Complete!\n";
    }

    private function renameDirectories(): void
    {
        echo "📁 Renaming directories...\n";

        $directoriesToRename = [
            'app/Http/Resources/Universal' => 'app/Http/Resources/Universal',
            'tests/Feature/Api/Universal' => 'tests/Feature/Api/Universal',
            'resources/css/universal' => 'resources/css/universal',
            'resources/js/universal' => 'resources/js/universal',
        ];

        foreach ($directoriesToRename as $oldDir => $newDir) {
            if (is_dir($oldDir)) {
                if (rename($oldDir, $newDir)) {
                    $this->renamedDirectories[] = "$oldDir → $newDir";
                    echo "   ✅ Renamed: $oldDir → $newDir\n";
                } else {
                    echo "   ❌ Failed to rename: $oldDir\n";
                }
            } else {
                echo "   ⚠️  Directory not found: $oldDir\n";
            }
        }
    }

    private function renameFiles(): void
    {
        echo "\n📄 Renaming files...\n";

        $filesToRename = $this->findFilesToRename();

        foreach ($filesToRename as $oldFile => $newFile) {
            if (file_exists($oldFile)) {
                if (rename($oldFile, $newFile)) {
                    $this->renamedFiles[] = "$oldFile → $newFile";
                    echo "   ✅ Renamed: $oldFile → $newFile\n";
                } else {
                    echo "   ❌ Failed to rename: $oldFile\n";
                }
            }
        }
    }

    private function findFilesToRename(): array
    {
        $files = [];
        
        // Find all files with universal in their names
        $patterns = [
            'universal_*.php',
            'Universal_*.php',
            'UNIVERSAL_*.md',
            '*universal*.php',
            '*Universal*.md',
        ];

        foreach ($patterns as $pattern) {
            $matches = glob($pattern);
            foreach ($matches as $file) {
                $newName = $this->generateNewFileName($file);
                if ($newName !== $file) {
                    $files[$file] = $newName;
                }
            }
        }

        return $files;
    }

    private function generateNewFileName(string $file): string
    {
        $newName = $file;
        foreach ($this->replacements as $old => $new) {
            $newName = str_replace($old, $new, $newName);
        }
        return $newName;
    }

    private function replaceFileContents(): void
    {
        echo "\n📝 Replacing file contents...\n";

        $extensions = ['php', 'js', 'css', 'scss', 'vue', 'md', 'json'];
        $files = $this->findAllFiles($extensions);

        foreach ($files as $file) {
            $this->processFile($file);
        }

        echo "   ✅ Processed " . count($this->processedFiles) . " files\n";
    }

    private function findAllFiles(array $extensions): array
    {
        $files = [];
        $directories = [
            'app/',
            'resources/',
            'tests/',
            'routes/',
            'config/',
            '*.php',
            '*.md',
            '*.js',
            'vite.config.js',
        ];

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir)
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
                        if (in_array($ext, $extensions)) {
                            $files[] = $file->getPathname();
                        }
                    }
                }
            } else {
                // Handle glob patterns
                $matches = glob($dir);
                foreach ($matches as $file) {
                    if (is_file($file)) {
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        if (in_array($ext, $extensions)) {
                            $files[] = $file;
                        }
                    }
                }
            }
        }

        return array_unique($files);
    }

    private function processFile(string $filePath): void
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return;
        }

        $originalContent = file_get_contents($filePath);
        $newContent = $originalContent;
        $hasChanges = false;

        foreach ($this->replacements as $old => $new) {
            $updatedContent = str_replace($old, $new, $newContent);
            if ($updatedContent !== $newContent) {
                $hasChanges = true;
                $newContent = $updatedContent;
            }
        }

        if ($hasChanges) {
            if (file_put_contents($filePath, $newContent) !== false) {
                $this->processedFiles[] = $filePath;
                echo "   ✅ Updated: $filePath\n";
            } else {
                echo "   ❌ Failed to update: $filePath\n";
            }
        }
    }

    private function updateConfigurationFiles(): void
    {
        echo "\n⚙️  Updating configuration files...\n";

        // Update vite.config.js specifically
        $viteConfig = 'vite.config.js';
        if (file_exists($viteConfig)) {
            $content = file_get_contents($viteConfig);
            $newContent = str_replace(
                ['universal/', 'universal:'],
                ['universal/', 'universal:'],
                $content
            );
            
            if ($content !== $newContent) {
                file_put_contents($viteConfig, $newContent);
                echo "   ✅ Updated: $viteConfig\n";
            }
        }

        // Update package.json if it exists
        $packageJson = 'package.json';
        if (file_exists($packageJson)) {
            $content = file_get_contents($packageJson);
            $newContent = str_replace(
                ['universal'],
                ['universal'],
                $content
            );
            
            if ($content !== $newContent) {
                file_put_contents($packageJson, $newContent);
                echo "   ✅ Updated: $packageJson\n";
            }
        }
    }

    private function generateReport(): void
    {
        $report = "# Universal Universal Replacement Report\n\n";
        $report .= "**Date:** " . date('Y-m-d H:i:s') . "\n\n";

        $report .= "## Summary\n\n";
        $report .= "- **Directories Renamed:** " . count($this->renamedDirectories) . "\n";
        $report .= "- **Files Renamed:** " . count($this->renamedFiles) . "\n";
        $report .= "- **Files Content Updated:** " . count($this->processedFiles) . "\n\n";

        if (!empty($this->renamedDirectories)) {
            $report .= "## Renamed Directories\n\n";
            foreach ($this->renamedDirectories as $rename) {
                $report .= "- $rename\n";
            }
            $report .= "\n";
        }

        if (!empty($this->renamedFiles)) {
            $report .= "## Renamed Files\n\n";
            foreach ($this->renamedFiles as $rename) {
                $report .= "- $rename\n";
            }
            $report .= "\n";
        }

        $report .= "## Key Replacements Made\n\n";
        foreach ($this->replacements as $old => $new) {
            $report .= "- `$old` → `$new`\n";
        }

        $report .= "\n## Next Steps\n\n";
        $report .= "1. Run `npm run build` to rebuild assets with new Universal naming\n";
        $report .= "2. Clear Laravel caches: `php artisan cache:clear`\n";
        $report .= "3. Clear view cache: `php artisan view:clear`\n";
        $report .= "4. Run tests to ensure everything works: `php artisan test`\n";
        $report .= "5. Update any remaining references manually if needed\n";

        file_put_contents('UNIVERSAL_REPLACEMENT_REPORT.md', $report);
        echo "\n📄 Report saved: UNIVERSAL_REPLACEMENT_REPORT.md\n";
    }
}

// Execute the replacement
echo "🚀 Starting Universal Universal Replacement...\n\n";

$replacer = new UniversalReplacementSystem();
$replacer->execute();

echo "\n✨ All Universal references have been replaced with Universal!\n";
echo "📋 Check UNIVERSAL_REPLACEMENT_REPORT.md for detailed changes.\n"; 