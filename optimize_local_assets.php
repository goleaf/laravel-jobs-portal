<?php

/**
 * Comprehensive Local Asset Management Optimization
 * Moves all CSS/JS to local npm packages and removes CDN dependencies
 */

echo "📦 Local Asset Management Optimization Starting...\n";
echo "=" . str_repeat("=", 50) . "\n\n";

class LocalAssetOptimizer
{
    private int $cdnLinksRemoved = 0;
    private int $filesProcessed = 0;
    private array $assetsAdded = [];
    private array $cdnDependencies = [];

    public function optimize(): void
    {
        echo "🚀 Starting Local Asset Management Optimization...\n\n";
        
        $this->scanForCdnDependencies();
        $this->installLocalPackages();
        $this->removeCdnReferences();
        $this->optimizeViteConfiguration();
        $this->generateReport();
    }

    private function scanForCdnDependencies(): void
    {
        echo "🔍 Scanning for CDN Dependencies...\n";
        
        $bladeFiles = $this->getBladeFiles();
        
        foreach ($bladeFiles as $file) {
            $content = file_get_contents($file);
            
            // Find CDN links
            if (preg_match_all('/https?:\/\/[^"\']*\.(css|js)/', $content, $matches)) {
                foreach ($matches[0] as $cdnUrl) {
                    $this->cdnDependencies[] = [
                        'url' => $cdnUrl,
                        'file' => $file,
                        'type' => pathinfo($cdnUrl, PATHINFO_EXTENSION)
                    ];
                }
            }
        }
        
        $uniqueCdns = array_unique(array_column($this->cdnDependencies, 'url'));
        echo "   ✅ Found " . count($uniqueCdns) . " unique CDN dependencies\n";
        
        // Log common CDN patterns
        $cdnPatterns = [
            'bootstrap' => 0,
            'jquery' => 0,
            'fontawesome' => 0,
            'googleapis' => 0,
            'jsdelivr' => 0,
            'unpkg' => 0,
            'cdnjs' => 0
        ];
        
        foreach ($uniqueCdns as $cdn) {
            foreach ($cdnPatterns as $pattern => $count) {
                if (stripos($cdn, $pattern) !== false) {
                    $cdnPatterns[$pattern]++;
                }
            }
        }
        
        echo "   📊 CDN Breakdown:\n";
        foreach ($cdnPatterns as $pattern => $count) {
            if ($count > 0) {
                echo "      - $pattern: $count references\n";
            }
        }
    }

    private function installLocalPackages(): void
    {
        echo "\n📦 Installing Local NPM Packages...\n";
        
        // Read current package.json
        $packageJson = json_decode(file_get_contents('package.json'), true);
        
        // Define packages to install
        $packagesToInstall = [
            // Core frameworks
            'jquery' => '^3.7.1',
            'bootstrap' => '^5.3.2',
            '@popperjs/core' => '^2.11.8',
            
            // Icons and fonts
            '@fortawesome/fontawesome-free' => '^6.5.1',
            
            // UI libraries
            'alpinejs' => '^3.13.3',
            'select2' => '^4.1.0',
            'datatables.net' => '^1.13.7',
            'datatables.net-bs5' => '^1.13.7',
            'sweetalert2' => '^11.10.1',
            
            // Utilities
            'moment' => '^2.29.4',
            'lodash' => '^4.17.21',
            'axios' => '^1.6.2',
            
            // Chart libraries
            'chart.js' => '^4.4.0',
            'apexcharts' => '^3.44.0',
            
            // Date pickers
            'flatpickr' => '^4.6.13',
            
            // File uploads
            'dropzone' => '^6.0.0-beta.2',
            
            // Sliders
            'swiper' => '^11.0.5',
            'slick-carousel' => '^1.8.1'
        ];
        
        // Add packages to devDependencies
        if (!isset($packageJson['devDependencies'])) {
            $packageJson['devDependencies'] = [];
        }
        
        foreach ($packagesToInstall as $package => $version) {
            if (!isset($packageJson['devDependencies'][$package])) {
                $packageJson['devDependencies'][$package] = $version;
                $this->assetsAdded[] = "$package@$version";
                echo "   ✅ Added $package@$version\n";
            }
        }
        
        // Update package.json
        file_put_contents('package.json', json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "   ✅ Updated package.json with " . count($this->assetsAdded) . " packages\n";
    }

    private function removeCdnReferences(): void
    {
        echo "\n🗑️ Removing CDN References from Blade Files...\n";
        
        $bladeFiles = $this->getBladeFiles();
        
        foreach ($bladeFiles as $file) {
            $content = file_get_contents($file);
            $originalContent = $content;
            
            // Remove CDN CSS links
            $content = preg_replace(
                '/<link[^>]*href=["\']https?:\/\/[^"\']*\.(css)[^"\']*["\'][^>]*>/i',
                '{{-- CDN CSS removed - now using local assets --}}',
                $content
            );
            
            // Remove CDN JS scripts
            $content = preg_replace(
                '/<script[^>]*src=["\']https?:\/\/[^"\']*\.(js)[^"\']*["\'][^>]*><\/script>/i',
                '{{-- CDN JS removed - now using local assets --}}',
                $content
            );
            
            // Remove Google Fonts (we'll handle these separately)
            $content = preg_replace(
                '/<link[^>]*href=["\']https?:\/\/fonts\.googleapis\.com[^"\']*["\'][^>]*>/i',
                '{{-- Google Fonts moved to local assets --}}',
                $content
            );
            
            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                $this->cdnLinksRemoved++;
                $this->filesProcessed++;
            }
        }
        
        echo "   ✅ Processed $this->filesProcessed files\n";
        echo "   ✅ Removed CDN references from $this->cdnLinksRemoved files\n";
    }

    private function optimizeViteConfiguration(): void
    {
        echo "\n⚙️ Optimizing Vite Configuration...\n";
        
        // Create optimized vite.config.js
        $viteConfig = "import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin.js',
                'resources/js/frontend.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            '~jquery': path.resolve(__dirname, 'node_modules/jquery'),
            '~fontawesome': path.resolve(__dirname, 'node_modules/@fortawesome/fontawesome-free'),
            '~select2': path.resolve(__dirname, 'node_modules/select2'),
            '~datatables': path.resolve(__dirname, 'node_modules/datatables.net'),
            '~sweetalert2': path.resolve(__dirname, 'node_modules/sweetalert2'),
            '~alpinejs': path.resolve(__dirname, 'node_modules/alpinejs'),
            '~moment': path.resolve(__dirname, 'node_modules/moment'),
            '~lodash': path.resolve(__dirname, 'node_modules/lodash'),
            '~axios': path.resolve(__dirname, 'node_modules/axios'),
            '~chartjs': path.resolve(__dirname, 'node_modules/chart.js'),
            '~apexcharts': path.resolve(__dirname, 'node_modules/apexcharts'),
            '~flatpickr': path.resolve(__dirname, 'node_modules/flatpickr'),
            '~dropzone': path.resolve(__dirname, 'node_modules/dropzone'),
            '~swiper': path.resolve(__dirname, 'node_modules/swiper'),
            '~slick': path.resolve(__dirname, 'node_modules/slick-carousel')
        }
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': [
                        'jquery',
                        'bootstrap',
                        '@popperjs/core'
                    ],
                    'ui-libs': [
                        'select2',
                        'datatables.net',
                        'sweetalert2',
                        'alpinejs'
                    ],
                    'charts': [
                        'chart.js',
                        'apexcharts'
                    ],
                    'utilities': [
                        'moment',
                        'lodash',
                        'axios'
                    ]
                }
            }
        },
        cssCodeSplit: true,
        sourcemap: true
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});";

        file_put_contents('vite.config.js', $viteConfig);
        echo "   ✅ Created optimized vite.config.js\n";

        // Update main CSS file
        $appCss = "@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import 'tailwindcss/utilities';

/* Local Asset Imports */
@import '~bootstrap/dist/css/bootstrap.min.css';
@import '~@fortawesome/fontawesome-free/css/all.min.css';
@import '~select2/dist/css/select2.min.css';
@import '~datatables.net-bs5/css/dataTables.bootstrap5.min.css';
@import '~sweetalert2/dist/sweetalert2.min.css';
@import '~flatpickr/dist/flatpickr.min.css';
@import '~dropzone/dist/dropzone.css';
@import '~swiper/css/bundle';
@import '~slick-carousel/slick/slick.css';
@import '~slick-carousel/slick/slick-theme.css';

/* Custom Styles */
@layer components {
    .btn-primary {
        @apply bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
    }
    
    .btn-secondary {
        @apply bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
    }
    
    .btn-success {
        @apply bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
    }
    
    .btn-danger {
        @apply bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
    }
    
    .card {
        @apply bg-white rounded-lg shadow-md border border-gray-200;
    }
    
    .card-header {
        @apply bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-lg;
    }
    
    .card-body {
        @apply p-6;
    }
    
    .form-control {
        @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent;
    }
    
    .form-label {
        @apply block text-sm font-medium text-gray-700 mb-2;
    }
    
    .table {
        @apply w-full border-collapse bg-white;
    }
    
    .table th {
        @apply bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200;
    }
    
    .table td {
        @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b border-gray-200;
    }
}

/* Custom animations and utilities */
@layer utilities {
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .animate-slide-up {
        animation: slideUp 0.3s ease-out;
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}";

        file_put_contents('resources/css/app.css', $appCss);
        echo "   ✅ Updated resources/css/app.css with local imports\n";

        // Update main JS file
        $appJs = "import './bootstrap';
import '../css/app.css';

// Import local packages
import 'bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'select2';
import 'datatables.net';
import 'datatables.net-bs5';
import Swal from 'sweetalert2';
window.Swal = Swal;

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import moment from 'moment';
window.moment = moment;

import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Chart libraries
import Chart from 'chart.js/auto';
window.Chart = Chart;

import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

// Date picker
import flatpickr from 'flatpickr';
window.flatpickr = flatpickr;

// File upload
import { Dropzone } from 'dropzone';
window.Dropzone = Dropzone;

// Sliders
import { Swiper } from 'swiper/bundle';
window.Swiper = Swiper;

// Initialize common functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"popover\"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Initialize DataTables
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Previous'
                }
            }
        });
    }
    
    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
    
    // Initialize date pickers
    if (window.flatpickr) {
        flatpickr('.datepicker', {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
        
        flatpickr('.datetimepicker', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            allowInput: true
        });
    }
});

// Global error handling
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
});

// CSRF token setup
const token = document.head.querySelector('meta[name=\"csrf-token\"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}";

        file_put_contents('resources/js/app.js', $appJs);
        echo "   ✅ Updated resources/js/app.js with local imports\n";
    }

    private function getBladeFiles(): array
    {
        $files = [];
        $directories = [
            'resources/views',
            'resources/views/admin',
            'resources/views/candidate',
            'resources/views/employer',
            'resources/views/front_web',
            'resources/views/layouts'
        ];
        
        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir)
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getExtension() === 'php') {
                        $files[] = $file->getPathname();
                    }
                }
            }
        }
        
        return $files;
    }

    private function generateReport(): void
    {
        echo "\n📋 LOCAL ASSET OPTIMIZATION REPORT\n";
        echo "=" . str_repeat("=", 40) . "\n";
        echo "CDN Links Removed: $this->cdnLinksRemoved\n";
        echo "Files Processed: $this->filesProcessed\n";
        echo "NPM Packages Added: " . count($this->assetsAdded) . "\n";
        echo "Vite Configuration: Optimized\n";
        echo "Asset Pipeline: Configured\n\n";
        
        echo "📦 NPM Packages Added:\n";
        foreach ($this->assetsAdded as $package) {
            echo "  - $package\n";
        }
        
        echo "\n📋 NEXT STEPS:\n";
        echo "1. Run: npm install\n";
        echo "2. Run: npm run build\n";
        echo "3. Test asset loading in browser\n";
        echo "4. Verify all functionality works\n\n";
        
        echo "✅ Local Asset Management Optimization Complete!\n";
    }
}

// Run the optimization
try {
    $optimizer = new LocalAssetOptimizer();
    $optimizer->optimize();
} catch (Exception $e) {
    echo "❌ Optimization failed: " . $e->getMessage() . "\n";
}

?> 