<?php

/**
 * Comprehensive TailwindCSS Migration Script
 * Removes Bootstrap dependencies and converts all Bootstrap classes to TailwindCSS
 */

echo "🎨 TailwindCSS Migration Starting...\n";
echo "=" . str_repeat("=", 50) . "\n\n";

class TailwindMigrator
{
    private array $bootstrapToTailwind = [
        // Layout & Grid
        'container' => 'container mx-auto',
        'container-fluid' => 'w-full',
        'row' => 'flex flex-wrap',
        'col' => 'flex-1',
        'col-1' => 'w-1/12',
        'col-2' => 'w-2/12',
        'col-3' => 'w-3/12',
        'col-4' => 'w-4/12',
        'col-5' => 'w-5/12',
        'col-6' => 'w-6/12',
        'col-7' => 'w-7/12',
        'col-8' => 'w-8/12',
        'col-9' => 'w-9/12',
        'col-10' => 'w-10/12',
        'col-11' => 'w-11/12',
        'col-12' => 'w-full',
        'col-md-1' => 'md:w-1/12',
        'col-md-2' => 'md:w-2/12',
        'col-md-3' => 'md:w-3/12',
        'col-md-4' => 'md:w-4/12',
        'col-md-5' => 'md:w-5/12',
        'col-md-6' => 'md:w-6/12',
        'col-md-7' => 'md:w-7/12',
        'col-md-8' => 'md:w-8/12',
        'col-md-9' => 'md:w-9/12',
        'col-md-10' => 'md:w-10/12',
        'col-md-11' => 'md:w-11/12',
        'col-md-12' => 'md:w-full',
        
        // Buttons
        'btn' => 'px-4 py-2 rounded font-medium transition-colors',
        'btn-primary' => 'bg-primary-600 text-white hover:bg-primary-700',
        'btn-secondary' => 'bg-gray-500 text-white hover:bg-gray-600',
        'btn-success' => 'bg-green-600 text-white hover:bg-green-700',
        'btn-danger' => 'bg-red-600 text-white hover:bg-red-700',
        'btn-warning' => 'bg-yellow-500 text-white hover:bg-yellow-600',
        'btn-info' => 'bg-blue-500 text-white hover:bg-blue-600',
        'btn-light' => 'bg-gray-100 text-gray-800 hover:bg-gray-200',
        'btn-dark' => 'bg-gray-800 text-white hover:bg-gray-900',
        'btn-sm' => 'px-3 py-1.5 text-sm',
        'btn-lg' => 'px-6 py-3 text-lg',
        'btn-block' => 'w-full',
        
        // Forms
        'form-control' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500',
        'form-select' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500',
        'form-check' => 'flex items-center',
        'form-check-input' => 'mr-2',
        'form-check-label' => 'text-sm text-gray-700',
        'form-label' => 'block text-sm font-medium text-gray-700 mb-1',
        
        // Cards
        'card' => 'bg-white shadow rounded-lg overflow-hidden',
        'card-header' => 'px-6 py-4 bg-gray-50 border-b border-gray-200',
        'card-body' => 'p-6',
        'card-footer' => 'px-6 py-4 bg-gray-50 border-t border-gray-200',
        'card-title' => 'text-lg font-semibold text-gray-900',
        
        // Navigation
        'navbar' => 'bg-white shadow-sm',
        'navbar-nav' => 'flex space-x-4',
        'nav-link' => 'text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium',
        'navbar-brand' => 'text-xl font-bold text-gray-900',
        
        // Alerts
        'alert' => 'p-4 rounded-md mb-4',
        'alert-primary' => 'bg-primary-50 border border-primary-200 text-primary-800',
        'alert-success' => 'bg-green-50 border border-green-200 text-green-800',
        'alert-danger' => 'bg-red-50 border border-red-200 text-red-800',
        'alert-warning' => 'bg-yellow-50 border border-yellow-200 text-yellow-800',
        'alert-info' => 'bg-blue-50 border border-blue-200 text-blue-800',
        
        // Tables
        'table' => 'w-full divide-y divide-gray-200',
        'table-striped' => 'odd:bg-gray-50',
        'table-hover' => 'hover:bg-gray-50',
        'table-responsive' => 'overflow-x-auto',
        
        // Utilities
        'd-none' => 'hidden',
        'd-block' => 'block',
        'd-flex' => 'flex',
        'justify-content-center' => 'justify-center',
        'justify-content-between' => 'justify-between',
        'align-items-center' => 'items-center',
        'text-center' => 'text-center',
        'text-left' => 'text-left',
        'text-right' => 'text-right',
        'text-muted' => 'text-gray-500',
        'text-primary' => 'text-primary-600',
        'text-success' => 'text-green-600',
        'text-danger' => 'text-red-600',
        'text-warning' => 'text-yellow-600',
        'bg-primary' => 'bg-primary-600',
        'bg-success' => 'bg-green-600',
        'bg-danger' => 'bg-red-600',
        'bg-warning' => 'bg-yellow-500',
        'bg-light' => 'bg-gray-100',
        'bg-dark' => 'bg-gray-800',
        'w-100' => 'w-full',
        'h-100' => 'h-full',
        'mb-3' => 'mb-3',
        'mt-3' => 'mt-3',
        'p-3' => 'p-3',
        'm-3' => 'm-3',
    ];

    private int $filesProcessed = 0;
    private int $classesConverted = 0;

    public function migrate(): void
    {
        echo "🚀 Starting TailwindCSS Migration...\n\n";
        
        $this->removeBootstrapFromPackageJson();
        $this->createTailwindCssFile();
        $this->migrateBladeFiles();
        $this->generateReport();
    }

    private function removeBootstrapFromPackageJson(): void
    {
        echo "📦 Removing Bootstrap from package.json...\n";
        
        $packageJson = json_decode(file_get_contents('package.json'), true);
        
        // Remove Bootstrap dependencies
        $removeDeps = ['bootstrap', '@types/bootstrap'];
        
        foreach ($removeDeps as $dep) {
            if (isset($packageJson['dependencies'][$dep])) {
                unset($packageJson['dependencies'][$dep]);
                echo "   ✅ Removed: $dep (dependencies)\n";
            }
            if (isset($packageJson['devDependencies'][$dep])) {
                unset($packageJson['devDependencies'][$dep]);
                echo "   ✅ Removed: $dep (devDependencies)\n";
            }
        }
        
        file_put_contents('package.json', json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "   💾 Updated package.json\n\n";
    }

    private function createTailwindCssFile(): void
    {
        echo "🎨 Creating TailwindCSS file...\n";
        
        $tailwindCss = '@import "tailwindcss";

/* Custom components */
@layer components {
    .btn {
        @apply px-4 py-2 rounded font-medium transition-colors;
    }
    
    .btn-primary {
        @apply bg-primary-600 text-white hover:bg-primary-700 focus:ring-2 focus:ring-primary-500;
    }
    
    .card {
        @apply bg-white shadow rounded-lg overflow-hidden;
    }
    
    .card-header {
        @apply px-6 py-4 bg-gray-50 border-b border-gray-200;
    }
    
    .card-body {
        @apply p-6;
    }
    
    .form-control {
        @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500;
    }
    
    .alert {
        @apply p-4 rounded-md mb-4;
    }
    
    .alert-success {
        @apply bg-green-50 border border-green-200 text-green-800;
    }
    
    .table {
        @apply w-full divide-y divide-gray-200;
    }
}';

        if (!is_dir('resources/css')) {
            mkdir('resources/css', 0755, true);
        }
        
        file_put_contents('resources/css/app.css', $tailwindCss);
        echo "   ✅ Created resources/css/app.css\n\n";
    }

    private function migrateBladeFiles(): void
    {
        echo "🔄 Migrating Blade files...\n";
        
        $this->processDirectory('resources/views');
        
        echo "   ✅ Processed $this->filesProcessed files\n";
        echo "   ✅ Converted $this->classesConverted Bootstrap classes\n\n";
    }

    private function processDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && 
                str_ends_with($file->getFilename(), '.blade.php')) {
                $this->processBladeFile($file->getPathname());
            }
        }
    }

    private function processBladeFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        // Convert Bootstrap classes to TailwindCSS
        foreach ($this->bootstrapToTailwind as $bootstrap => $tailwind) {
            $pattern = '/class\s*=\s*["\']([^"\']*)\b' . preg_quote($bootstrap, '/') . '\b([^"\']*)["\']([^>]*>)/';
            $content = preg_replace_callback($pattern, function($matches) use ($tailwind) {
                $beforeClass = trim($matches[1]);
                $afterClass = trim($matches[2]);
                $restOfTag = $matches[3];
                
                $newClasses = [];
                if ($beforeClass) $newClasses[] = $beforeClass;
                $newClasses[] = $tailwind;
                if ($afterClass) $newClasses[] = $afterClass;
                
                $this->classesConverted++;
                return 'class="' . implode(' ', $newClasses) . '"' . $restOfTag;
            }, $content);
        }
        
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->filesProcessed++;
            
            $relativePath = str_replace(getcwd() . '/', '', $filePath);
            echo "   🔄 Migrated: $relativePath\n";
        }
    }

    private function generateReport(): void
    {
        echo "📋 TAILWINDCSS MIGRATION REPORT\n";
        echo "=" . str_repeat("=", 40) . "\n";
        echo "Files Processed: $this->filesProcessed\n";
        echo "Classes Converted: $this->classesConverted\n";
        echo "Bootstrap Classes Mapped: " . count($this->bootstrapToTailwind) . "\n\n";
        
        echo "📋 NEXT STEPS:\n";
        echo "1. Run: npm install\n";
        echo "2. Run: npm run build\n";
        echo "3. Test the application UI\n";
        echo "4. Fix any remaining Bootstrap references manually\n\n";
        
        echo "✅ TailwindCSS Migration Complete!\n";
    }
}

// Run the migration
try {
    $migrator = new TailwindMigrator();
    $migrator->migrate();
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}

?> 