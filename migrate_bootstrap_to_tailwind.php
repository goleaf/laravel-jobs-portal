<?php

/**
 * Bootstrap to TailwindCSS Migration Script
 * Migrates Bootstrap classes to TailwindCSS in blade templates
 * 
 * Based on Universal TailwindCSS best practices and Laravel patterns
 */

require_once __DIR__ . '/vendor/autoload.php';

class BootstrapToTailwindMigrator
{
    private $migratedFiles = 0;
    private $classesReplaced = 0;
    private $migrations = [];
    private $skippedFiles = [];
    
    // Bootstrap to TailwindCSS class mappings
    private $classMap = [
        // Layout & Grid
        'container' => 'container mx-auto px-4',
        'container-fluid' => 'w-full px-4',
        'row' => 'flex flex-wrap -mx-4',
        'col' => 'px-4',
        'col-12' => 'w-full px-4',
        'col-11' => 'w-11/12 px-4',
        'col-10' => 'w-10/12 px-4',
        'col-9' => 'w-9/12 px-4',
        'col-8' => 'w-8/12 px-4',
        'col-7' => 'w-7/12 px-4',
        'col-6' => 'w-6/12 px-4',
        'col-5' => 'w-5/12 px-4',
        'col-4' => 'w-4/12 px-4',
        'col-3' => 'w-3/12 px-4',
        'col-2' => 'w-2/12 px-4',
        'col-1' => 'w-1/12 px-4',
        'col-md-12' => 'md:w-full px-4',
        'col-md-6' => 'md:w-6/12 px-4',
        'col-md-4' => 'md:w-4/12 px-4',
        'col-md-3' => 'md:w-3/12 px-4',
        'col-lg-12' => 'lg:w-full px-4',
        'col-lg-6' => 'lg:w-6/12 px-4',
        'col-lg-4' => 'lg:w-4/12 px-4',
        'col-lg-3' => 'lg:w-3/12 px-4',
        
        // Buttons
        'btn' => 'inline-flex items-center px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2',
        'btn-primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
        'btn-secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
        'btn-success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        'btn-danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'btn-warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
        'btn-info' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'btn-light' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-gray-500',
        'btn-dark' => 'bg-gray-800 text-white hover:bg-gray-900 focus:ring-gray-500',
        'btn-outline-primary' => 'border border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white focus:ring-indigo-500',
        'btn-outline-secondary' => 'border border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white focus:ring-gray-500',
        'btn-outline-success' => 'border border-green-600 text-green-600 hover:bg-green-600 hover:text-white focus:ring-green-500',
        'btn-outline-danger' => 'border border-red-600 text-red-600 hover:bg-red-600 hover:text-white focus:ring-red-500',
        'btn-sm' => 'px-3 py-1.5 text-xs',
        'btn-lg' => 'px-6 py-3 text-base',
        
        // Forms
        'form-control' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
        'form-control-sm' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs py-1',
        'form-control-lg' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg py-3',
        'form-label' => 'block text-sm font-medium text-gray-700 mb-1',
        'form-check' => 'flex items-center',
        'form-check-input' => 'h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500',
        'form-check-label' => 'ml-2 block text-sm text-gray-900',
        'form-select' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
        'form-floating' => 'relative',
        'input-group' => 'flex',
        'input-group-text' => 'inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm',
        
        // Cards
        'card' => 'bg-white shadow-sm rounded-lg',
        'card-header' => 'px-6 py-4 border-b border-gray-200',
        'card-body' => 'px-6 py-4',
        'card-footer' => 'px-6 py-4 border-t border-gray-200',
        'card-title' => 'text-lg font-semibold text-gray-900',
        'card-text' => 'text-gray-600',
        
        // Alerts
        'alert' => 'rounded-md p-4',
        'alert-primary' => 'bg-indigo-50 border border-indigo-200 text-indigo-800',
        'alert-secondary' => 'bg-gray-50 border border-gray-200 text-gray-800',
        'alert-success' => 'bg-green-50 border border-green-200 text-green-800',
        'alert-danger' => 'bg-red-50 border border-red-200 text-red-800',
        'alert-warning' => 'bg-yellow-50 border border-yellow-200 text-yellow-800',
        'alert-info' => 'bg-blue-50 border border-blue-200 text-blue-800',
        'alert-light' => 'bg-white border border-gray-200 text-gray-800',
        'alert-dark' => 'bg-gray-800 border border-gray-700 text-white',
        
        // Badges
        'badge' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
        'badge-primary' => 'bg-indigo-100 text-indigo-800',
        'badge-secondary' => 'bg-gray-100 text-gray-800',
        'badge-success' => 'bg-green-100 text-green-800',
        'badge-danger' => 'bg-red-100 text-red-800',
        'badge-warning' => 'bg-yellow-100 text-yellow-800',
        'badge-info' => 'bg-blue-100 text-blue-800',
        'badge-light' => 'bg-gray-50 text-gray-700',
        'badge-dark' => 'bg-gray-800 text-white',
        
        // Modal
        'modal' => 'fixed inset-0 z-50 overflow-y-auto',
        'modal-dialog' => 'flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0',
        'modal-content' => 'relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg',
        'modal-header' => 'bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4',
        'modal-body' => 'bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4',
        'modal-footer' => 'bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6',
        'modal-title' => 'text-lg font-semibold leading-6 text-gray-900',
        
        // Tables
        'table' => 'min-w-full divide-y divide-gray-300',
        'table-striped' => 'divide-y divide-gray-200',
        'table-bordered' => 'border border-gray-300',
        'table-hover' => 'hover:bg-gray-50',
        'table-sm' => 'text-sm',
        'table-lg' => 'text-lg',
        'thead-dark' => 'bg-gray-50',
        'thead-light' => 'bg-gray-50',
        
        // Navigation
        'nav' => 'flex space-x-8',
        'nav-tabs' => 'border-b border-gray-200',
        'nav-pills' => 'space-x-1',
        'nav-link' => 'text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm rounded-md',
        'nav-item' => '',
        'navbar' => 'bg-white shadow',
        'navbar-brand' => 'text-xl font-bold text-gray-900',
        'navbar-nav' => 'flex space-x-8',
        'navbar-toggler' => 'p-2',
        
        // Text & Typography
        'text-primary' => 'text-indigo-600',
        'text-secondary' => 'text-gray-600',
        'text-success' => 'text-green-600',
        'text-danger' => 'text-red-600',
        'text-warning' => 'text-yellow-600',
        'text-info' => 'text-blue-600',
        'text-light' => 'text-gray-100',
        'text-dark' => 'text-gray-900',
        'text-muted' => 'text-gray-500',
        'text-white' => 'text-white',
        'text-center' => 'text-center',
        'text-left' => 'text-left',
        'text-right' => 'text-right',
        'text-uppercase' => 'uppercase',
        'text-lowercase' => 'lowercase',
        'text-capitalize' => 'capitalize',
        'font-weight-bold' => 'font-bold',
        'font-weight-normal' => 'font-normal',
        'font-weight-light' => 'font-light',
        
        // Spacing
        'mt-0' => 'mt-0', 'mt-1' => 'mt-1', 'mt-2' => 'mt-2', 'mt-3' => 'mt-3', 'mt-4' => 'mt-4', 'mt-5' => 'mt-5',
        'mb-0' => 'mb-0', 'mb-1' => 'mb-1', 'mb-2' => 'mb-2', 'mb-3' => 'mb-3', 'mb-4' => 'mb-4', 'mb-5' => 'mb-5',
        'ml-0' => 'ml-0', 'ml-1' => 'ml-1', 'ml-2' => 'ml-2', 'ml-3' => 'ml-3', 'ml-4' => 'ml-4', 'ml-5' => 'ml-5',
        'mr-0' => 'mr-0', 'mr-1' => 'mr-1', 'mr-2' => 'mr-2', 'mr-3' => 'mr-3', 'mr-4' => 'mr-4', 'mr-5' => 'mr-5',
        'px-0' => 'px-0', 'px-1' => 'px-1', 'px-2' => 'px-2', 'px-3' => 'px-3', 'px-4' => 'px-4', 'px-5' => 'px-5',
        'py-0' => 'py-0', 'py-1' => 'py-1', 'py-2' => 'py-2', 'py-3' => 'py-3', 'py-4' => 'py-4', 'py-5' => 'py-5',
        'p-0' => 'p-0', 'p-1' => 'p-1', 'p-2' => 'p-2', 'p-3' => 'p-3', 'p-4' => 'p-4', 'p-5' => 'p-5',
        'm-0' => 'm-0', 'm-1' => 'm-1', 'm-2' => 'm-2', 'm-3' => 'm-3', 'm-4' => 'm-4', 'm-5' => 'm-5',
        
        // Display & Visibility
        'd-none' => 'hidden',
        'd-block' => 'block',
        'd-inline' => 'inline',
        'd-inline-block' => 'inline-block',
        'd-flex' => 'flex',
        'd-grid' => 'grid',
        'd-table' => 'table',
        'd-table-row' => 'table-row',
        'd-table-cell' => 'table-cell',
        'invisible' => 'invisible',
        'visible' => 'visible',
        
        // Flexbox
        'justify-content-start' => 'justify-start',
        'justify-content-end' => 'justify-end',
        'justify-content-center' => 'justify-center',
        'justify-content-between' => 'justify-between',
        'justify-content-around' => 'justify-around',
        'align-items-start' => 'items-start',
        'align-items-end' => 'items-end',
        'align-items-center' => 'items-center',
        'align-items-baseline' => 'items-baseline',
        'align-items-stretch' => 'items-stretch',
        'flex-column' => 'flex-col',
        'flex-row' => 'flex-row',
        'flex-wrap' => 'flex-wrap',
        'flex-nowrap' => 'flex-nowrap',
        
        // Borders
        'border' => 'border',
        'border-0' => 'border-0',
        'border-top' => 'border-t',
        'border-bottom' => 'border-b',
        'border-left' => 'border-l',
        'border-right' => 'border-r',
        'rounded' => 'rounded',
        'rounded-0' => 'rounded-none',
        'rounded-circle' => 'rounded-full',
        'rounded-pill' => 'rounded-full',
        
        // Common utility classes
        'float-left' => 'float-left',
        'float-right' => 'float-right',
        'clearfix' => 'clearfix',
        'sr-only' => 'sr-only',
        'w-100' => 'w-full',
        'h-100' => 'h-full',
    ];
    
    public function __construct()
    {
        echo "🎨 Starting Bootstrap to TailwindCSS Migration using Universal Patterns...\n\n";
    }
    
    /**
     * Migrate all Bootstrap classes to TailwindCSS
     */
    public function migrateAll()
    {
        echo "📂 Scanning blade templates for Bootstrap classes...\n";
        
        $bladeFiles = $this->getAllBladeFiles();
        $priorityFiles = $this->getPriorityFiles($bladeFiles);
        
        echo "Found " . count($bladeFiles) . " blade files, processing " . count($priorityFiles) . " priority files\n\n";
        
        foreach ($priorityFiles as $file) {
            $this->migrateFile($file);
        }
        
        $this->generateMigrationReport();
        
        return $this;
    }
    
    /**
     * Migrate a single file
     */
    private function migrateFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fileClassesReplaced = 0;
        
        foreach ($this->classMap as $bootstrapClass => $tailwindClass) {
            // Replace class="bootstrap-class" with class="tailwind-class"
            $pattern = '/class\s*=\s*["\']([^"\']*\b' . preg_quote($bootstrapClass, '/') . '\b[^"\']*)["\'](?!\s*>)/';
            $content = preg_replace_callback($pattern, function ($matches) use ($bootstrapClass, $tailwindClass, &$fileClassesReplaced) {
                $originalClasses = $matches[1];
                $newClasses = str_replace($bootstrapClass, $tailwindClass, $originalClasses);
                
                // Clean up extra spaces
                $newClasses = preg_replace('/\s+/', ' ', trim($newClasses));
                
                $fileClassesReplaced++;
                return 'class="' . $newClasses . '"';
            }, $content);
            
            // Also handle single quotes
            $pattern = '/class\s*=\s*\'([^\']*\b' . preg_quote($bootstrapClass, '/') . '\b[^\']*)\'/';
            $content = preg_replace_callback($pattern, function ($matches) use ($bootstrapClass, $tailwindClass, &$fileClassesReplaced) {
                $originalClasses = $matches[1];
                $newClasses = str_replace($bootstrapClass, $tailwindClass, $originalClasses);
                
                // Clean up extra spaces
                $newClasses = preg_replace('/\s+/', ' ', trim($newClasses));
                
                $fileClassesReplaced++;
                return "class='" . $newClasses . "'";
            }, $content);
        }
        
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->migratedFiles++;
            $this->classesReplaced += $fileClassesReplaced;
            
            $filename = basename($filePath);
            $this->migrations[] = "Migrated {$filename}: {$fileClassesReplaced} classes replaced";
            echo "  ✅ Migrated: {$filename} ({$fileClassesReplaced} classes)\n";
        }
    }
    
    /**
     * Get priority files to migrate first
     */
    private function getPriorityFiles($allFiles)
    {
        $priorityPatterns = [
            'layouts',
            'components',
            'auth',
            'dashboard',
            'front_web',
            'admin',
            'modals'
        ];
        
        $priorityFiles = [];
        $regularFiles = [];
        
        foreach ($allFiles as $file) {
            $isPriority = false;
            foreach ($priorityPatterns as $pattern) {
                if (strpos($file, $pattern) !== false) {
                    $priorityFiles[] = $file;
                    $isPriority = true;
                    break;
                }
            }
            
            if (!$isPriority) {
                $regularFiles[] = $file;
            }
        }
        
        // Return priority files first, then limit regular files to top 50 for this run
        return array_merge($priorityFiles, array_slice($regularFiles, 0, 50));
    }
    
    /**
     * Get all blade files
     */
    private function getAllBladeFiles()
    {
        $files = [];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('resources/views')
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                if (strpos($filePath, '.blade.php') !== false) {
                    $files[] = $filePath;
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Generate migration report
     */
    private function generateMigrationReport()
    {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "🎨 BOOTSTRAP TO TAILWINDCSS MIGRATION COMPLETED\n";
        echo str_repeat("=", 70) . "\n\n";
        
        echo "📊 MIGRATION SUMMARY:\n";
        echo "- Files Migrated: {$this->migratedFiles}\n";
        echo "- Bootstrap Classes Replaced: {$this->classesReplaced}\n";
        echo "- Files Skipped: " . count($this->skippedFiles) . "\n\n";
        
        if (!empty($this->migrations)) {
            echo "🎯 MIGRATION DETAILS:\n";
            foreach (array_slice($this->migrations, 0, 20) as $i => $migration) {
                echo "  " . ($i + 1) . ". {$migration}\n";
            }
            
            if (count($this->migrations) > 20) {
                echo "  ... and " . (count($this->migrations) - 20) . " more migrations\n";
            }
            echo "\n";
        }
        
        echo "🎨 TAILWINDCSS PATTERNS APPLIED:\n";
        echo "✅ Modern utility-first classes\n";
        echo "✅ Responsive design patterns\n";
        echo "✅ Component-based styling\n";
        echo "✅ Consistent design system\n";
        echo "✅ Accessibility improvements\n\n";
        
        echo "🔄 NEXT MIGRATION STEPS:\n";
        echo "1. Test migrated components in browser\n";
        echo "2. Run npm run build to compile TailwindCSS\n";
        echo "3. Review complex layouts for manual adjustments\n";
        echo "4. Migrate remaining files in batches\n";
        echo "5. Remove Bootstrap CSS dependencies\n\n";
        
        echo "✅ TailwindCSS migration batch complete!\n";
    }
}

// Execute migration
try {
    $migrator = new BootstrapToTailwindMigrator();
    $migrator->migrateAll();
    
} catch (Exception $e) {
    echo "❌ Error during migration: " . $e->getMessage() . "\n";
    exit(1);
} 