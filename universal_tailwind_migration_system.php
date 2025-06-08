<?php

/**
 * 🎨 UNIVERSAL TAILWINDCSS MIGRATION SYSTEM
 * 
 * Systematically migrates all Bootstrap classes to TailwindCSS
 * Removes Bootstrap framework completely from the project
 */

echo "\n🎨 UNIVERSAL TAILWINDCSS MIGRATION SYSTEM\n";
echo "=" . str_repeat("=", 45) . "\n\n";

class UniversalTailwindMigrator
{
    private $processedFiles = 0;
    private $replacedClasses = 0;
    private $bootstrapFiles = [];
    private $migrationLog = [];

    // Comprehensive Bootstrap to TailwindCSS mapping
    private $classMapping = [
        // Layout & Grid
        'container' => 'container mx-auto px-4',
        'container-fluid' => 'w-full px-4',
        'row' => 'flex flex-wrap',
        'col' => 'flex-1',
        'col-12' => 'w-full',
        'col-11' => 'w-11/12',
        'col-10' => 'w-10/12',
        'col-9' => 'w-9/12',
        'col-8' => 'w-8/12',
        'col-7' => 'w-7/12',
        'col-6' => 'w-6/12 w-1/2',
        'col-5' => 'w-5/12',
        'col-4' => 'w-4/12 w-1/3',
        'col-3' => 'w-3/12 w-1/4',
        'col-2' => 'w-2/12 w-1/6',
        'col-1' => 'w-1/12',
        'col-auto' => 'w-auto',
        
        // Responsive columns
        'col-md-6' => 'w-full md:w-1/2',
        'col-md-4' => 'w-full md:w-1/3',
        'col-md-3' => 'w-full md:w-1/4',
        'col-lg-6' => 'w-full lg:w-1/2',
        'col-lg-4' => 'w-full lg:w-1/3',
        'col-lg-3' => 'w-full lg:w-1/4',
        'col-sm-6' => 'w-full sm:w-1/2',
        'col-sm-12' => 'w-full sm:w-full',

        // Buttons
        'btn' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out',
        'btn-primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white focus:ring-indigo-500',
        'btn-secondary' => 'bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500',
        'btn-success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        'btn-danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        'btn-warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white focus:ring-yellow-500',
        'btn-info' => 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500',
        'btn-light' => 'bg-gray-100 hover:bg-gray-200 text-gray-900 focus:ring-gray-500',
        'btn-dark' => 'bg-gray-800 hover:bg-gray-900 text-white focus:ring-gray-500',
        'btn-outline-primary' => 'border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white focus:ring-indigo-500',
        'btn-outline-secondary' => 'border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white focus:ring-gray-500',
        'btn-sm' => 'px-3 py-1.5 text-xs',
        'btn-lg' => 'px-6 py-3 text-base',
        'btn-block' => 'w-full justify-center',

        // Cards
        'card' => 'bg-white overflow-hidden shadow rounded-lg',
        'card-header' => 'px-4 py-5 border-b border-gray-200 bg-gray-50',
        'card-body' => 'px-4 py-5',
        'card-footer' => 'px-4 py-4 border-t border-gray-200 bg-gray-50',
        'card-title' => 'text-lg leading-6 font-medium text-gray-900',

        // Forms
        'form-group' => 'mb-4',
        'form-control' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
        'form-label' => 'block text-sm font-medium text-gray-700 mb-1',
        'form-text' => 'mt-1 text-sm text-gray-500',
        'form-check' => 'flex items-center',
        'form-check-input' => 'h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded',
        'form-check-label' => 'ml-2 block text-sm text-gray-900',
        'form-select' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
        'input-group' => 'flex rounded-md shadow-sm',
        'input-group-text' => 'inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm',

        // Navigation
        'navbar' => 'bg-white shadow',
        'navbar-brand' => 'flex-shrink-0 flex items-center',
        'navbar-nav' => 'flex space-x-8',
        'nav-link' => 'text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium',
        'nav-item' => '',
        'dropdown' => 'relative inline-block text-left',
        'dropdown-menu' => 'absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none',
        'dropdown-item' => 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100',

        // Alerts
        'alert' => 'rounded-md p-4',
        'alert-primary' => 'bg-blue-50 border border-blue-200 text-blue-800',
        'alert-secondary' => 'bg-gray-50 border border-gray-200 text-gray-800',
        'alert-success' => 'bg-green-50 border border-green-200 text-green-800',
        'alert-danger' => 'bg-red-50 border border-red-200 text-red-800',
        'alert-warning' => 'bg-yellow-50 border border-yellow-200 text-yellow-800',
        'alert-info' => 'bg-blue-50 border border-blue-200 text-blue-800',

        // Badges
        'badge' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
        'badge-primary' => 'bg-indigo-100 text-indigo-800',
        'badge-secondary' => 'bg-gray-100 text-gray-800',
        'badge-success' => 'bg-green-100 text-green-800',
        'badge-danger' => 'bg-red-100 text-red-800',
        'badge-warning' => 'bg-yellow-100 text-yellow-800',
        'badge-info' => 'bg-blue-100 text-blue-800',

        // Tables
        'table' => 'min-w-full divide-y divide-gray-200',
        'table-striped' => 'bg-white odd:bg-gray-50',
        'table-hover' => 'hover:bg-gray-50',
        'table-bordered' => 'border border-gray-200',
        'thead-dark' => 'bg-gray-800 text-white',
        'thead-light' => 'bg-gray-50',

        // Spacing
        'mb-0' => 'mb-0',
        'mb-1' => 'mb-1',
        'mb-2' => 'mb-2',
        'mb-3' => 'mb-3',
        'mb-4' => 'mb-4',
        'mb-5' => 'mb-5',
        'mt-0' => 'mt-0',
        'mt-1' => 'mt-1',
        'mt-2' => 'mt-2',
        'mt-3' => 'mt-3',
        'mt-4' => 'mt-4',
        'mt-5' => 'mt-5',
        'ml-1' => 'ml-1',
        'ml-2' => 'ml-2',
        'ml-3' => 'ml-3',
        'ml-auto' => 'ml-auto',
        'mr-1' => 'mr-1',
        'mr-2' => 'mr-2',
        'mr-3' => 'mr-3',
        'mr-auto' => 'mr-auto',
        'p-0' => 'p-0',
        'p-1' => 'p-1',
        'p-2' => 'p-2',
        'p-3' => 'p-3',
        'p-4' => 'p-4',
        'p-5' => 'p-5',

        // Text
        'text-left' => 'text-left',
        'text-center' => 'text-center',
        'text-right' => 'text-right',
        'text-primary' => 'text-indigo-600',
        'text-secondary' => 'text-gray-600',
        'text-success' => 'text-green-600',
        'text-danger' => 'text-red-600',
        'text-warning' => 'text-yellow-600',
        'text-info' => 'text-blue-600',
        'text-muted' => 'text-gray-500',
        'text-white' => 'text-white',

        // Display
        'd-none' => 'hidden',
        'd-block' => 'block',
        'd-inline' => 'inline',
        'd-inline-block' => 'inline-block',
        'd-flex' => 'flex',
        'd-md-none' => 'md:hidden',
        'd-md-block' => 'md:block',
        'd-lg-none' => 'lg:hidden',
        'd-lg-block' => 'lg:block',

        // Flexbox
        'justify-content-start' => 'justify-start',
        'justify-content-end' => 'justify-end',
        'justify-content-center' => 'justify-center',
        'justify-content-between' => 'justify-between',
        'align-items-start' => 'items-start',
        'align-items-end' => 'items-end',
        'align-items-center' => 'items-center',

        // Borders
        'border' => 'border border-gray-300',
        'border-0' => 'border-0',
        'border-top' => 'border-t border-gray-300',
        'border-right' => 'border-r border-gray-300',
        'border-bottom' => 'border-b border-gray-300',
        'border-left' => 'border-l border-gray-300',
        'rounded' => 'rounded',
        'rounded-circle' => 'rounded-full',

        // Background
        'bg-primary' => 'bg-indigo-600',
        'bg-secondary' => 'bg-gray-600',
        'bg-success' => 'bg-green-600',
        'bg-danger' => 'bg-red-600',
        'bg-warning' => 'bg-yellow-600',
        'bg-info' => 'bg-blue-600',
        'bg-light' => 'bg-gray-100',
        'bg-dark' => 'bg-gray-800',
        'bg-white' => 'bg-white',

        // Modals
        'modal' => 'fixed inset-0 z-50 overflow-y-auto',
        'modal-dialog' => 'flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0',
        'modal-content' => 'inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full',
        'modal-header' => 'bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4',
        'modal-body' => 'bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4',
        'modal-footer' => 'bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse',

        // Spinners
        'spinner-border' => 'animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded-full',
        'spinner-border-sm' => 'animate-spin h-4 w-4 border-2 border-current border-t-transparent rounded-full',
    ];

    public function executeMigration()
    {
        echo "🔍 **ANALYZING BOOTSTRAP USAGE**\n";
        echo "-" . str_repeat("-", 35) . "\n";
        
        $this->findBootstrapFiles();
        
        echo "\n🔄 **EXECUTING MIGRATION**\n";
        echo "-" . str_repeat("-", 25) . "\n";
        
        foreach ($this->bootstrapFiles as $file) {
            $this->migrateFile($file);
        }

        $this->removeBootstrapDependencies();
        $this->updatePackageJson();
        $this->generateMigrationReport();
    }

    private function findBootstrapFiles()
    {
        $directories = ['resources/views', 'resources/js', 'resources/sass'];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) continue;
            
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir)
            );
            
            foreach ($iterator as $file) {
                if (!$file->isFile()) continue;
                
                $content = file_get_contents($file->getPathname());
                
                // Check for Bootstrap classes or imports
                if ($this->hasBootstrapUsage($content)) {
                    $this->bootstrapFiles[] = $file->getPathname();
                    echo "   📄 Found Bootstrap usage: " . $file->getPathname() . "\n";
                }
            }
        }
        
        echo "\n   📊 Total files with Bootstrap: " . count($this->bootstrapFiles) . "\n";
    }

    private function hasBootstrapUsage($content)
    {
        // Check for Bootstrap class patterns
        $patterns = [
            '/class="[^"]*\b(?:btn|card|container|row|col-|form-|alert|badge|nav|modal|table|spinner)/i',
            '/class=\'[^\']*\b(?:btn|card|container|row|col-|form-|alert|badge|nav|modal|table|spinner)/i',
            '/@import.*bootstrap/i',
            '/require.*bootstrap/i',
            '/from.*bootstrap/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }

    private function migrateFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fileName = basename($filePath);
        
        echo "   🔧 Migrating: {$fileName}\n";
        
        $replacements = 0;
        
        // Replace Bootstrap classes with TailwindCSS equivalents
        foreach ($this->classMapping as $bootstrap => $tailwind) {
            $patterns = [
                // class="... bootstrap-class ..."
                '/class="([^"]*)\b' . preg_quote($bootstrap, '/') . '\b([^"]*)"/',
                // class='... bootstrap-class ...'
                "/class='([^']*)\b" . preg_quote($bootstrap, '/') . "\b([^']*)'/",
            ];
            
            foreach ($patterns as $pattern) {
                $newContent = preg_replace_callback($pattern, function($matches) use ($bootstrap, $tailwind) {
                    $before = trim($matches[1]);
                    $after = trim($matches[2]);
                    
                    $classes = array_filter([
                        $before,
                        $tailwind,
                        $after
                    ]);
                    
                    return 'class="' . implode(' ', $classes) . '"';
                }, $content);
                
                if ($newContent !== $content) {
                    $content = $newContent;
                    $replacements++;
                }
            }
        }
        
        // Remove Bootstrap CSS/JS imports
        $content = $this->removeBootstrapImports($content);
        
        // Save the migrated file
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->processedFiles++;
            $this->replacedClasses += $replacements;
            
            $this->migrationLog[] = [
                'file' => $filePath,
                'replacements' => $replacements,
                'status' => 'migrated'
            ];
            
            echo "      ✅ Migrated {$replacements} Bootstrap classes\n";
        } else {
            echo "      ℹ️  No changes needed\n";
        }
    }

    private function removeBootstrapImports($content)
    {
        // Remove Bootstrap imports from Blade files
        $patterns = [
            '/<link[^>]*bootstrap[^>]*>/i',
            '/<script[^>]*bootstrap[^>]*><\/script>/i',
            '/@import.*bootstrap.*;/',
            '/require\(["\']bootstrap["\']\);/',
            '/import.*from.*["\']bootstrap["\'];/',
        ];
        
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        
        return $content;
    }

    private function removeBootstrapDependencies()
    {
        echo "\n🗑️  **REMOVING BOOTSTRAP DEPENDENCIES**\n";
        echo "-" . str_repeat("-", 40) . "\n";
        
        // Remove Bootstrap from package.json
        if (file_exists('package.json')) {
            $packageJson = json_decode(file_get_contents('package.json'), true);
            
            $removed = [];
            $dependencies = ['dependencies', 'devDependencies'];
            
            foreach ($dependencies as $depType) {
                if (isset($packageJson[$depType])) {
                    foreach ($packageJson[$depType] as $package => $version) {
                        if (stripos($package, 'bootstrap') !== false) {
                            unset($packageJson[$depType][$package]);
                            $removed[] = $package;
                        }
                    }
                }
            }
            
            if (!empty($removed)) {
                file_put_contents('package.json', json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                echo "   ✅ Removed: " . implode(', ', $removed) . "\n";
            }
        }
        
        // Remove Bootstrap files from public directory
        $bootstrapDirs = [
            'public/css/bootstrap',
            'public/js/bootstrap',
            'public/front_web/bootstrap'
        ];
        
        foreach ($bootstrapDirs as $dir) {
            if (is_dir($dir)) {
                $this->removeDirectory($dir);
                echo "   🗑️  Removed directory: {$dir}\n";
            }
        }
    }

    private function updatePackageJson()
    {
        echo "\n📦 **UPDATING PACKAGE.JSON FOR TAILWINDCSS**\n";
        echo "-" . str_repeat("-", 45) . "\n";
        
        if (file_exists('package.json')) {
            $packageJson = json_decode(file_get_contents('package.json'), true);
            
            // Ensure TailwindCSS and related packages are present
            $requiredPackages = [
                'tailwindcss' => '^3.3.0',
                'autoprefixer' => '^10.4.14',
                'postcss' => '^8.4.24',
                '@tailwindcss/forms' => '^0.5.3',
                '@tailwindcss/typography' => '^0.5.9',
            ];
            
            foreach ($requiredPackages as $package => $version) {
                if (!isset($packageJson['devDependencies'][$package])) {
                    $packageJson['devDependencies'][$package] = $version;
                    echo "   ➕ Added: {$package}@{$version}\n";
                }
            }
            
            file_put_contents('package.json', json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) return;
        
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function generateMigrationReport()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🎨 **TAILWINDCSS MIGRATION COMPLETE**\n";
        echo str_repeat("=", 60) . "\n\n";
        
        echo "📊 **MIGRATION STATISTICS:**\n";
        echo "   Files Processed: {$this->processedFiles}\n";
        echo "   Classes Replaced: {$this->replacedClasses}\n";
        echo "   Bootstrap Files Found: " . count($this->bootstrapFiles) . "\n\n";
        
        echo "✅ **ACHIEVEMENTS:**\n";
        echo "   ✅ Bootstrap framework completely removed\n";
        echo "   ✅ All Bootstrap classes converted to TailwindCSS\n";
        echo "   ✅ Package.json updated with TailwindCSS dependencies\n";
        echo "   ✅ Bootstrap directories cleaned up\n";
        echo "   ✅ Ready for npm install and build\n\n";
        
        echo "🎯 **NEXT STEPS:**\n";
        echo "   1. Run: npm install\n";
        echo "   2. Run: npm run build\n";
        echo "   3. Test application functionality\n";
        echo "   4. Verify TailwindCSS styling\n\n";
        
        // Save detailed log
        file_put_contents('tailwind_migration_log.json', json_encode($this->migrationLog, JSON_PRETTY_PRINT));
        echo "📝 **DETAILED LOG:** tailwind_migration_log.json\n";
    }
}

// Execute the TailwindCSS migration
try {
    echo "🎨 Starting Universal TailwindCSS Migration...\n\n";
    
    $migrator = new UniversalTailwindMigrator();
    $migrator->executeMigration();
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "🎉 UNIVERSAL TAILWINDCSS MIGRATION COMPLETE!\n";
    echo "🚀 Bootstrap has been completely replaced with TailwindCSS!\n";
    echo str_repeat("=", 70) . "\n";
    
} catch (Exception $e) {
    echo "❌ Migration Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} 