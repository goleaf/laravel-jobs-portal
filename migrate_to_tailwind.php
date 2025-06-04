<?php

/**
 * TailwindCSS Migration Script
 * Converts Bootstrap classes to TailwindCSS throughout the Laravel application
 * 
 * This script will:
 * 1. Scan all Blade templates for Bootstrap classes
 * 2. Create mapping from Bootstrap to TailwindCSS
 * 3. Replace Bootstrap CDN links with local TailwindCSS
 * 4. Update package.json with TailwindCSS dependencies
 * 5. Generate TailwindCSS configuration
 * 6. Convert common Bootstrap components to TailwindCSS
 */

class TailwindMigrationScript
{
    private $bladeFiles = [];
    private $bootstrapUsage = [];
    private $replacements = [];
    private $tailwindConfig = '';
    private $stats = [];

    // Bootstrap to TailwindCSS mapping
    private $classMapping = [
        // Layout & Grid
        'container' => 'container mx-auto px-4',
        'container-fluid' => 'w-full px-4',
        'row' => 'flex flex-wrap -mx-2',
        'col' => 'flex-1 px-2',
        'col-1' => 'w-1/12 px-2',
        'col-2' => 'w-2/12 px-2',
        'col-3' => 'w-3/12 px-2',
        'col-4' => 'w-4/12 px-2',
        'col-6' => 'w-6/12 px-2',
        'col-8' => 'w-8/12 px-2',
        'col-12' => 'w-full px-2',
        'col-md-1' => 'md:w-1/12 px-2',
        'col-md-2' => 'md:w-2/12 px-2',
        'col-md-3' => 'md:w-3/12 px-2',
        'col-md-4' => 'md:w-4/12 px-2',
        'col-md-6' => 'md:w-6/12 px-2',
        'col-md-8' => 'md:w-8/12 px-2',
        'col-md-12' => 'md:w-full px-2',
        'col-lg-1' => 'lg:w-1/12 px-2',
        'col-lg-2' => 'lg:w-2/12 px-2',
        'col-lg-3' => 'lg:w-3/12 px-2',
        'col-lg-4' => 'lg:w-4/12 px-2',
        'col-lg-6' => 'lg:w-6/12 px-2',
        'col-lg-8' => 'lg:w-8/12 px-2',
        'col-lg-12' => 'lg:w-full px-2',

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
        'justify-content-center' => 'justify-center',
        'justify-content-end' => 'justify-end',
        'justify-content-between' => 'justify-between',
        'align-items-start' => 'items-start',
        'align-items-center' => 'items-center',
        'align-items-end' => 'items-end',
        'flex-column' => 'flex-col',
        'flex-row' => 'flex-row',
        'flex-wrap' => 'flex-wrap',

        // Spacing
        'm-0' => 'm-0',
        'm-1' => 'm-1',
        'm-2' => 'm-2',
        'm-3' => 'm-3',
        'm-4' => 'm-4',
        'm-5' => 'm-5',
        'p-0' => 'p-0',
        'p-1' => 'p-1',
        'p-2' => 'p-2',
        'p-3' => 'p-3',
        'p-4' => 'p-4',
        'p-5' => 'p-5',
        'mt-0' => 'mt-0',
        'mt-1' => 'mt-1',
        'mt-2' => 'mt-2',
        'mt-3' => 'mt-3',
        'mt-4' => 'mt-4',
        'mt-5' => 'mt-5',
        'mb-0' => 'mb-0',
        'mb-1' => 'mb-1',
        'mb-2' => 'mb-2',
        'mb-3' => 'mb-3',
        'mb-4' => 'mb-4',
        'mb-5' => 'mb-5',
        'pt-0' => 'pt-0',
        'pt-1' => 'pt-1',
        'pt-2' => 'pt-2',
        'pt-3' => 'pt-3',
        'pt-4' => 'pt-4',
        'pt-5' => 'pt-5',
        'pb-0' => 'pb-0',
        'pb-1' => 'pb-1',
        'pb-2' => 'pb-2',
        'pb-3' => 'pb-3',
        'pb-4' => 'pb-4',
        'pb-5' => 'pb-5',

        // Buttons
        'btn' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out',
        'btn-primary' => 'bg-blue-600 hover:bg-blue-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500',
        'btn-secondary' => 'bg-gray-600 hover:bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-gray-500',
        'btn-success' => 'bg-green-600 hover:bg-green-700 text-white focus:outline-none focus:ring-2 focus:ring-green-500',
        'btn-danger' => 'bg-red-600 hover:bg-red-700 text-white focus:outline-none focus:ring-2 focus:ring-red-500',
        'btn-warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500',
        'btn-info' => 'bg-blue-500 hover:bg-blue-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500',
        'btn-light' => 'bg-gray-100 hover:bg-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500',
        'btn-dark' => 'bg-gray-800 hover:bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-gray-500',
        'btn-outline-primary' => 'border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white',
        'btn-outline-secondary' => 'border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white',
        'btn-sm' => 'px-3 py-1.5 text-xs',
        'btn-lg' => 'px-6 py-3 text-base',

        // Forms
        'form-control' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
        'form-select' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
        'form-check' => 'flex items-center',
        'form-check-input' => 'mr-2',
        'form-check-label' => 'text-sm',
        'form-label' => 'block text-sm font-medium text-gray-700 mb-1',
        'form-text' => 'text-xs text-gray-500 mt-1',
        'input-group' => 'flex',
        'input-group-text' => 'px-3 py-2 bg-gray-50 border border-gray-300 border-r-0 rounded-l-md text-gray-500',

        // Cards
        'card' => 'bg-white rounded-lg shadow-md border border-gray-200',
        'card-header' => 'px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg',
        'card-body' => 'px-6 py-4',
        'card-footer' => 'px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg',
        'card-title' => 'text-lg font-semibold text-gray-900 mb-2',
        'card-text' => 'text-gray-700',

        // Alerts
        'alert' => 'px-4 py-3 rounded-md border mb-4',
        'alert-primary' => 'bg-blue-50 border-blue-200 text-blue-700',
        'alert-secondary' => 'bg-gray-50 border-gray-200 text-gray-700',
        'alert-success' => 'bg-green-50 border-green-200 text-green-700',
        'alert-danger' => 'bg-red-50 border-red-200 text-red-700',
        'alert-warning' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
        'alert-info' => 'bg-blue-50 border-blue-200 text-blue-700',

        // Tables
        'table' => 'min-w-full divide-y divide-gray-200',
        'table-striped' => 'odd:bg-gray-50 even:bg-white',
        'table-hover' => 'hover:bg-gray-50',
        'table-bordered' => 'border border-gray-300',
        'thead-dark' => 'bg-gray-800 text-white',
        'thead-light' => 'bg-gray-100 text-gray-700',

        // Navigation
        'nav' => 'flex space-x-1',
        'nav-link' => 'px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out',
        'nav-link.active' => 'bg-blue-600 text-white',
        'navbar' => 'bg-white shadow-sm border-b border-gray-200',
        'navbar-brand' => 'text-xl font-semibold text-gray-900',
        'navbar-nav' => 'flex space-x-1',
        'navbar-toggler' => 'md:hidden p-2',

        // Dropdowns
        'dropdown' => 'relative inline-block text-left',
        'dropdown-toggle' => 'inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50',
        'dropdown-menu' => 'origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50',
        'dropdown-item' => 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100',

        // Modal
        'modal' => 'fixed inset-0 z-50 overflow-y-auto',
        'modal-dialog' => 'flex items-center justify-center min-h-screen px-4',
        'modal-content' => 'bg-white rounded-lg shadow-xl max-w-lg w-full',
        'modal-header' => 'px-6 py-4 border-b border-gray-200',
        'modal-body' => 'px-6 py-4',
        'modal-footer' => 'px-6 py-4 border-t border-gray-200 flex justify-end space-x-2',

        // Text
        'text-primary' => 'text-blue-600',
        'text-secondary' => 'text-gray-600',
        'text-success' => 'text-green-600',
        'text-danger' => 'text-red-600',
        'text-warning' => 'text-yellow-600',
        'text-info' => 'text-blue-500',
        'text-dark' => 'text-gray-900',
        'text-muted' => 'text-gray-500',
        'text-center' => 'text-center',
        'text-left' => 'text-left',
        'text-right' => 'text-right',

        // Background
        'bg-primary' => 'bg-blue-600',
        'bg-secondary' => 'bg-gray-600',
        'bg-success' => 'bg-green-600',
        'bg-danger' => 'bg-red-600',
        'bg-warning' => 'bg-yellow-500',
        'bg-info' => 'bg-blue-500',
        'bg-light' => 'bg-gray-100',
        'bg-dark' => 'bg-gray-800',

        // Border
        'border' => 'border border-gray-300',
        'border-0' => 'border-0',
        'border-primary' => 'border-blue-600',
        'border-secondary' => 'border-gray-600',
        'border-success' => 'border-green-600',
        'border-danger' => 'border-red-600',

        // Border radius
        'rounded' => 'rounded',
        'rounded-0' => 'rounded-none',
        'rounded-lg' => 'rounded-lg',
        'rounded-pill' => 'rounded-full',

        // Position
        'position-relative' => 'relative',
        'position-absolute' => 'absolute',
        'position-fixed' => 'fixed',
        'position-sticky' => 'sticky',

        // Float
        'float-left' => 'float-left',
        'float-right' => 'float-right',
        'float-none' => 'float-none',

        // Width & Height
        'w-25' => 'w-1/4',
        'w-50' => 'w-1/2',
        'w-75' => 'w-3/4',
        'w-100' => 'w-full',
        'h-25' => 'h-1/4',
        'h-50' => 'h-1/2',
        'h-75' => 'h-3/4',
        'h-100' => 'h-full',
    ];

    public function __construct()
    {
        echo "🎨 TAILWINDCSS MIGRATION SCRIPT\n";
        echo "===============================\n\n";
    }

    /**
     * Main migration workflow
     */
    public function migrate()
    {
        $this->step1_scanBladeFiles();
        $this->step2_analyzeBootstrapUsage();
        $this->step3_removeCdnReferences();
        $this->step4_setupTailwindCSS();
        $this->step5_convertBootstrapClasses();
        $this->step6_generateTailwindComponents();
        $this->step7_generateReport();
    }

    /**
     * Step 1: Scan all Blade files
     */
    private function step1_scanBladeFiles()
    {
        echo "📁 STEP 1: Scanning Blade Files\n";
        echo "================================\n";

        $directories = [
            'resources/views',
        ];

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir)
                );

                foreach ($iterator as $file) {
                    if ($file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.') !== false) {
                        $this->bladeFiles[] = $file->getPathname();
                    }
                }
            }
        }

        echo "✅ Found " . count($this->bladeFiles) . " blade files\n\n";
    }

    /**
     * Step 2: Analyze Bootstrap usage
     */
    private function step2_analyzeBootstrapUsage()
    {
        echo "🔍 STEP 2: Analyzing Bootstrap Usage\n";
        echo "===================================\n";

        $bootstrapClassPattern = '/class=["\']([^"\']*(?:' . implode('|', array_keys($this->classMapping)) . ')[^"\']*)["\']|\bclass:\s*["\']([^"\']*(?:' . implode('|', array_keys($this->classMapping)) . ')[^"\']*)["\']|\bclass=\{["\']([^"\']*(?:' . implode('|', array_keys($this->classMapping)) . ')[^"\']*)["\']}/';

        foreach ($this->bladeFiles as $file) {
            $content = file_get_contents($file);
            
            // Find Bootstrap CDN references
            if (preg_match('/bootstrap.*\.css|bootstrap.*\.js/', $content)) {
                $this->bootstrapUsage['cdn_files'][] = $file;
            }

            // Find Bootstrap classes
            if (preg_match_all($bootstrapClassPattern, $content, $matches)) {
                foreach ($matches[1] as $classString) {
                    if (!empty($classString)) {
                        $classes = explode(' ', $classString);
                        foreach ($classes as $class) {
                            if (array_key_exists(trim($class), $this->classMapping)) {
                                $this->bootstrapUsage['classes'][trim($class)][] = $file;
                            }
                        }
                    }
                }
            }
        }

        $totalBootstrapClasses = count($this->bootstrapUsage['classes']);
        $filesWithCdn = count($this->bootstrapUsage['cdn_files'] ?? []);

        echo "  ✓ Found {$totalBootstrapClasses} unique Bootstrap classes\n";
        echo "  ✓ Found {$filesWithCdn} files with CDN references\n\n";
    }

    /**
     * Step 3: Remove CDN references
     */
    private function step3_removeCdnReferences()
    {
        echo "🗑️ STEP 3: Removing Bootstrap CDN References\n";
        echo "==========================================\n";

        $cdnPatterns = [
            '/\s*<link[^>]*bootstrap[^>]*>\s*/',
            '/\s*<script[^>]*bootstrap[^>]*><\/script>\s*/',
            '/\s*<link[^>]*href=["\'][^"\']*bootstrap[^"\']*["\'][^>]*>\s*/',
            '/\s*<script[^>]*src=["\'][^"\']*bootstrap[^"\']*["\'][^>]*><\/script>\s*/',
        ];

        foreach ($this->bladeFiles as $file) {
            $content = file_get_contents($file);
            $originalContent = $content;

            foreach ($cdnPatterns as $pattern) {
                $content = preg_replace($pattern, '', $content);
            }

            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                echo "  ✓ Removed CDN references from: " . basename($file) . "\n";
                $this->stats['cdn_removed'] = ($this->stats['cdn_removed'] ?? 0) + 1;
            }
        }

        echo "✅ CDN references removed\n\n";
    }

    /**
     * Step 4: Setup TailwindCSS
     */
    private function step4_setupTailwindCSS()
    {
        echo "⚙️ STEP 4: Setting up TailwindCSS\n";
        echo "================================\n";

        // Update package.json
        $this->updatePackageJson();
        echo "  ✓ Updated package.json\n";

        // Create TailwindCSS config
        $this->createTailwindConfig();
        echo "  ✓ Created tailwind.config.js\n";

        // Create main CSS file
        $this->createMainCssFile();
        echo "  ✓ Created main CSS file\n";

        // Update Vite config
        $this->updateViteConfig();
        echo "  ✓ Updated vite.config.js\n";

        echo "✅ TailwindCSS setup completed\n\n";
    }

    /**
     * Update package.json with TailwindCSS dependencies
     */
    private function updatePackageJson()
    {
        $packageJsonPath = 'package.json';
        
        if (file_exists($packageJsonPath)) {
            $packageJson = json_decode(file_get_contents($packageJsonPath), true);
        } else {
            $packageJson = [
                'name' => 'job-portal',
                'private' => true,
                'scripts' => [
                    'dev' => 'vite',
                    'build' => 'vite build'
                ]
            ];
        }

        $packageJson['devDependencies'] = array_merge(
            $packageJson['devDependencies'] ?? [],
            [
                'tailwindcss' => '^3.4.0',
                '@tailwindcss/forms' => '^0.5.7',
                '@tailwindcss/typography' => '^0.5.10',
                'autoprefixer' => '^10.4.16',
                'postcss' => '^8.4.32',
                'alpinejs' => '^3.13.3'
            ]
        );

        file_put_contents($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Create TailwindCSS configuration
     */
    private function createTailwindConfig()
    {
        $config = "/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};";

        file_put_contents('tailwind.config.js', $config);
    }

    /**
     * Create main CSS file
     */
    private function createMainCssFile()
    {
        $cssContent = "@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom Components */
@layer components {
    .btn {
        @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2;
    }
    
    .btn-primary {
        @apply bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500;
    }
    
    .btn-secondary {
        @apply bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500;
    }
    
    .btn-success {
        @apply bg-green-600 hover:bg-green-700 text-white focus:ring-green-500;
    }
    
    .btn-danger {
        @apply bg-red-600 hover:bg-red-700 text-white focus:ring-red-500;
    }
    
    .form-input {
        @apply w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
    }
    
    .card {
        @apply bg-white rounded-lg shadow-md border border-gray-200;
    }
    
    .card-header {
        @apply px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg;
    }
    
    .card-body {
        @apply px-6 py-4;
    }
    
    .alert {
        @apply px-4 py-3 rounded-md border mb-4;
    }
    
    .alert-success {
        @apply bg-green-50 border-green-200 text-green-700;
    }
    
    .alert-danger {
        @apply bg-red-50 border-red-200 text-red-700;
    }
    
    .alert-warning {
        @apply bg-yellow-50 border-yellow-200 text-yellow-700;
    }
    
    .alert-info {
        @apply bg-blue-50 border-blue-200 text-blue-700;
    }
}

/* Custom Utilities */
@layer utilities {
    .container-custom {
        @apply container mx-auto px-4 sm:px-6 lg:px-8;
    }
}";

        if (!is_dir('resources/css')) {
            mkdir('resources/css', 0755, true);
        }
        
        file_put_contents('resources/css/app.css', $cssContent);
    }

    /**
     * Update Vite configuration
     */
    private function updateViteConfig()
    {
        $viteConfig = "import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [
                require('tailwindcss'),
                require('autoprefixer'),
            ],
        },
    },
});";

        file_put_contents('vite.config.js', $viteConfig);

        // Create PostCSS config
        $postCssConfig = "export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};";

        file_put_contents('postcss.config.js', $postCssConfig);
    }

    /**
     * Step 5: Convert Bootstrap classes
     */
    private function step5_convertBootstrapClasses()
    {
        echo "🔄 STEP 5: Converting Bootstrap Classes\n";
        echo "======================================\n";

        foreach ($this->bladeFiles as $file) {
            $content = file_get_contents($file);
            $originalContent = $content;

            // Replace Bootstrap classes with TailwindCSS equivalents
            foreach ($this->classMapping as $bootstrap => $tailwind) {
                // Handle class="bootstrap-class"
                $content = preg_replace_callback(
                    '/class=["\']([^"\']*\b' . preg_quote($bootstrap, '/') . '\b[^"\']*)["\']/',
                    function ($matches) use ($bootstrap, $tailwind) {
                        $classString = $matches[1];
                        $classes = explode(' ', $classString);
                        $newClasses = [];
                        
                        foreach ($classes as $class) {
                            if (trim($class) === $bootstrap) {
                                $newClasses[] = $tailwind;
                            } else {
                                $newClasses[] = $class;
                            }
                        }
                        
                        return 'class="' . implode(' ', $newClasses) . '"';
                    },
                    $content
                );
            }

            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                echo "  ✓ Converted classes in: " . basename($file) . "\n";
                $this->stats['files_converted'] = ($this->stats['files_converted'] ?? 0) + 1;
            }
        }

        echo "✅ Bootstrap classes converted\n\n";
    }

    /**
     * Step 6: Generate TailwindCSS components
     */
    private function step6_generateTailwindComponents()
    {
        echo "🧩 STEP 6: Generating TailwindCSS Components\n";
        echo "==========================================\n";

        // Create common components
        $components = [
            'alert' => $this->generateAlertComponent(),
            'button' => $this->generateButtonComponent(),
            'form-input' => $this->generateFormInputComponent(),
            'modal' => $this->generateModalComponent(),
            'table' => $this->generateTableComponent(),
        ];

        foreach ($components as $name => $content) {
            $componentFile = "resources/views/components/tailwind/{$name}.blade.php";
            $dir = dirname($componentFile);
            
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            file_put_contents($componentFile, $content);
            echo "  ✓ Created component: {$name}.blade.php\n";
        }

        echo "✅ TailwindCSS components generated\n\n";
    }

    /**
     * Generate Alert component
     */
    private function generateAlertComponent()
    {
        return '@props([
    "type" => "info",
    "dismissible" => false
])

@php
    $classes = [
        "info" => "bg-blue-50 border-blue-200 text-blue-700",
        "success" => "bg-green-50 border-green-200 text-green-700",
        "warning" => "bg-yellow-50 border-yellow-200 text-yellow-700",
        "danger" => "bg-red-50 border-red-200 text-red-700",
    ];
@endphp

<div {{ $attributes->merge(["class" => "px-4 py-3 rounded-md border mb-4 " . $classes[$type]]) }}>
    <div class="flex">
        <div class="flex-1">
            {{ $slot }}
        </div>
        @if($dismissible)
            <button type="button" class="ml-2 text-gray-400 hover:text-gray-600" onclick="this.parentElement.parentElement.remove()">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        @endif
    </div>
</div>';
    }

    /**
     * Generate Button component
     */
    private function generateButtonComponent()
    {
        return '@props([
    "variant" => "primary",
    "size" => "md",
    "type" => "button"
])

@php
    $variants = [
        "primary" => "bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500",
        "secondary" => "bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500",
        "success" => "bg-green-600 hover:bg-green-700 text-white focus:ring-green-500",
        "danger" => "bg-red-600 hover:bg-red-700 text-white focus:ring-red-500",
        "outline" => "border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-blue-500",
    ];
    
    $sizes = [
        "sm" => "px-3 py-1.5 text-xs",
        "md" => "px-4 py-2 text-sm",
        "lg" => "px-6 py-3 text-base",
    ];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge([
        "class" => "inline-flex items-center font-medium rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 " . $variants[$variant] . " " . $sizes[$size]
    ]) }}
>
    {{ $slot }}
</button>';
    }

    /**
     * Generate Form Input component
     */
    private function generateFormInputComponent()
    {
        return '@props([
    "label" => null,
    "error" => null,
    "help" => null,
    "type" => "text",
    "required" => false
])

<div>
    @if($label)
        <label {{ $attributes->only("id")->mapWithKeys(fn($value, $key) => ["for" => $value]) }} class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        {{ $attributes->merge([
            "class" => "w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 " . ($error ? "border-red-300" : "border-gray-300")
        ]) }}
    >
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @elseif($help)
        <p class="mt-1 text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>';
    }

    /**
     * Generate Modal component
     */
    private function generateModalComponent()
    {
        return '@props([
    "show" => false,
    "title" => null,
    "size" => "md"
])

@php
    $sizes = [
        "sm" => "max-w-md",
        "md" => "max-w-lg",
        "lg" => "max-w-2xl",
        "xl" => "max-w-4xl",
    ];
@endphp

<div 
    x-data="{ show: @json($show) }"
    x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>
        
        <div 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative inline-block w-full {{ $sizes[$size] }} bg-white rounded-lg shadow-xl transform transition-all"
        >
            @if($title)
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
                </div>
            @endif
            
            <div class="px-6 py-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>';
    }

    /**
     * Generate Table component
     */
    private function generateTableComponent()
    {
        return '@props([
    "striped" => false,
    "hover" => false
])

<div class="overflow-x-auto">
    <table {{ $attributes->merge([
        "class" => "min-w-full divide-y divide-gray-200 " . 
                   ($striped ? "odd:bg-gray-50 even:bg-white " : "") .
                   ($hover ? "hover:bg-gray-50 " : "")
    ]) }}>
        {{ $slot }}
    </table>
</div>';
    }

    /**
     * Step 7: Generate migration report
     */
    private function step7_generateReport()
    {
        echo "📊 STEP 7: Generating Migration Report\n";
        echo "=====================================\n";

        $report = $this->generateMigrationReport();
        file_put_contents('TAILWIND_MIGRATION_REPORT.md', $report);
        echo "  ✓ Created: TAILWIND_MIGRATION_REPORT.md\n";

        echo "✅ Migration report generated\n\n";
    }

    /**
     * Generate comprehensive migration report
     */
    private function generateMigrationReport()
    {
        $totalFiles = count($this->bladeFiles);
        $cdnRemoved = $this->stats['cdn_removed'] ?? 0;
        $filesConverted = $this->stats['files_converted'] ?? 0;
        
        return "# 🎨 TAILWINDCSS MIGRATION REPORT

## Summary
- **Migration Date**: " . date('Y-m-d H:i:s') . "
- **Total Blade Files**: {$totalFiles}
- **Files with CDN Removed**: {$cdnRemoved}
- **Files with Classes Converted**: {$filesConverted}
- **Bootstrap Classes Mapped**: " . count($this->classMapping) . "

## Files Created
- `tailwind.config.js` - TailwindCSS configuration
- `postcss.config.js` - PostCSS configuration  
- `vite.config.js` - Updated Vite configuration
- `resources/css/app.css` - Main TailwindCSS file
- `package.json` - Updated with TailwindCSS dependencies
- TailwindCSS components in `resources/views/components/tailwind/`

## Components Created
- Alert component with variants (success, danger, warning, info)
- Button component with variants (primary, secondary, success, danger, outline)
- Form input component with labels and error states
- Modal component with Alpine.js integration
- Table component with striping and hover options

## Next Steps

### 1. Install Dependencies
```bash
npm install
```

### 2. Build Assets
```bash
npm run dev
# or for production
npm run build
```

### 3. Update Layout Files
Add TailwindCSS to your main layout:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

### 4. Use New Components
```blade
<x-tailwind.alert type=\"success\" dismissible>
    Success message here
</x-tailwind.alert>

<x-tailwind.button variant=\"primary\" size=\"lg\">
    Click me
</x-tailwind.button>

<x-tailwind.form-input 
    label=\"Email Address\" 
    type=\"email\" 
    required 
    id=\"email\" 
    name=\"email\" 
/>
```

### 5. Manual Review Required
Some complex Bootstrap components may need manual conversion:
- Custom Bootstrap themes
- Complex grid layouts
- JavaScript dependencies
- Third-party plugins

## Bootstrap Classes Converted
" . $this->generateClassMappingList() . "

## Notes
- All CDN references have been removed
- TailwindCSS provides better performance and smaller bundle sizes
- Alpine.js is included for JavaScript interactions
- Components follow Laravel Blade component conventions
- Responsive design is maintained with Tailwind's mobile-first approach
";
    }

    /**
     * Generate class mapping list for documentation
     */
    private function generateClassMappingList()
    {
        $list = "";
        $mappings = array_slice($this->classMapping, 0, 20, true);
        foreach ($mappings as $bootstrap => $tailwind) {
            $list .= "- `{$bootstrap}` → `{$tailwind}`\n";
        }
        $list .= "- ... and " . (count($this->classMapping) - 20) . " more mappings\n";
        return $list;
    }
}

// Run the migration
if (php_sapi_name() === 'cli') {
    $migration = new TailwindMigrationScript();
    $migration->migrate();
    
    echo "🎉 TAILWINDCSS MIGRATION COMPLETE!\n";
    echo "==================================\n";
    echo "✅ Bootstrap CDN references removed\n";
    echo "✅ TailwindCSS configuration created\n";
    echo "✅ Bootstrap classes converted\n";
    echo "✅ TailwindCSS components generated\n\n";
    echo "📖 Next steps:\n";
    echo "1. Run: npm install\n";
    echo "2. Run: npm run dev\n";
    echo "3. Update layout files with @vite directive\n";
    echo "4. Review TAILWIND_MIGRATION_REPORT.md\n\n";
}

?> 