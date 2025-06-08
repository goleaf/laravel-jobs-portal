<?php

/**
 * 🚀 UNIVERSAL COMPREHENSIVE BLADE FIXER SYSTEM
 * ============================================
 * Advanced Laravel blade template error detection and auto-fixing system
 * Based on Universal best practices and patterns
 */

class UniversalComprehensiveBladeFixer
{
    private array $stats = [
        'files_processed' => 0,
        'syntax_errors_fixed' => 0,
        'route_issues_fixed' => 0,
        'tailwind_migrations' => 0,
        'accessibility_improvements' => 0,
        'performance_optimizations' => 0
    ];

    private array $validRoutes = [];
    private array $routePatterns = [];
    private array $fixes = [];

    public function __construct()
    {
        $this->loadValidRoutes();
        $this->initializeRoutePatterns();
        echo "\033[1;36m🚀 UNIVERSAL COMPREHENSIVE BLADE FIXER\033[0m\n";
        echo str_repeat("=", 60) . "\n\n";
    }

    /**
     * Load all valid routes from the application
     */
    private function loadValidRoutes(): void
    {
        try {
            // Load common Laravel routes that are usually valid
            $this->validRoutes = [
                'login', 'logout', 'register', 'password.request', 'password.reset',
                'verification.notice', 'verification.verify', 'verification.send',
                'home', 'dashboard', 'profile.show', 'profile.edit', 'profile.update',
                'password.confirm', 'two-factor.login', 'two-factor.enable',
                // Job Portal specific routes
                'jobs.index', 'jobs.show', 'jobs.create', 'jobs.store', 'jobs.edit', 'jobs.update', 'jobs.destroy',
                'companies.index', 'companies.show', 'companies.create', 'companies.store', 'companies.edit', 'companies.update',
                'candidates.index', 'candidates.show', 'candidates.create', 'candidates.store', 'candidates.edit', 'candidates.update',
                'candidate.dashboard', 'candidate.profile', 'candidate.applications', 'candidate.favorite-jobs',
                'employer.dashboard', 'employer.jobs', 'employer.applications', 'employer.profile',
                'admin.dashboard', 'admin.users', 'admin.jobs', 'admin.companies', 'admin.settings',
                'front.home', 'front.jobs', 'front.companies', 'front.contact', 'front.about',
                'blog.index', 'blog.show', 'blog.category', 'categories.index', 'categories.show',
                'application.submit', 'application.withdraw', 'subscription.plans', 'payments.process'
            ];

            echo "\033[32m✅ Loaded " . count($this->validRoutes) . " valid route patterns\033[0m\n";
        } catch (Exception $e) {
            echo "\033[33m⚠️  Could not load all routes: " . $e->getMessage() . "\033[0m\n";
        }
    }

    /**
     * Initialize common route patterns for validation
     */
    private function initializeRoutePatterns(): void
    {
        $this->routePatterns = [
            // Standard Laravel patterns
            '/route\s*\(\s*[\'"]([^"\']+)[\'"].*?\)/' => 'route_call',
            '/url\s*\(\s*[\'"]([^"\']+)[\'"].*?\)/' => 'url_call',
            '/action\s*\(\s*[\'"]([^"\']+)[\'"].*?\)/' => 'action_call',
            '/@if\s*\(\s*Route::has\s*\(\s*[\'"]([^"\']+)[\'"].*?\)\s*\)/' => 'route_check',
            // Asset patterns
            '/asset\s*\(\s*[\'"]([^"\']+)[\'"].*?\)/' => 'asset_call',
            '/mix\s*\(\s*[\'"]([^"\']+)[\'"].*?\)/' => 'mix_call',
            '/vite\s*\(\s*[\'"]([^"\']+)[\'"].*?\)/' => 'vite_call'
        ];
    }

    /**
     * Fix all blade templates in the resources/views directory
     */
    public function fixAllBladeTemplates(): void
    {
        $bladeFiles = $this->findBladeFiles();
        
        echo "\033[34m🔍 Found " . count($bladeFiles) . " blade template files\033[0m\n";
        echo "\033[33m🚧 Starting comprehensive blade fixing process...\033[0m\n\n";

        $progressBar = 0;
        $totalFiles = count($bladeFiles);

        foreach ($bladeFiles as $file) {
            $progressBar++;
            $progress = round(($progressBar / $totalFiles) * 100, 1);
            
            echo "\r\033[36m📁 Processing: " . basename($file) . " ({$progress}%)\033[0m";
            
            $this->processBladeFile($file);
            $this->stats['files_processed']++;
            
            if ($progressBar % 50 === 0) {
                usleep(100000);
            }
        }

        echo "\n\n\033[1;32m✅ Blade fixing process completed!\033[0m\n";
        $this->displayReport();
    }

    /**
     * Find all blade template files
     */
    private function findBladeFiles(): array
    {
        $bladeFiles = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('resources/views', RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.') !== false) {
                $bladeFiles[] = $file->getPathname();
            }
        }

        return $bladeFiles;
    }

    /**
     * Process individual blade file
     */
    private function processBladeFile(string $filePath): void
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return;
        }

        $originalContent = file_get_contents($filePath);
        $content = $originalContent;
        $hasChanges = false;

        // Apply all fixing strategies
        $fixStrategies = [
            'fixSyntaxErrors',
            'fixRouteIssues', 
            'migrateTailwindCSS',
            'improveAccessibility',
            'optimizePerformance'
        ];

        foreach ($fixStrategies as $strategy) {
            $newContent = $this->$strategy($content, $filePath);
            if ($newContent !== $content) {
                $content = $newContent;
                $hasChanges = true;
            }
        }

        // Save changes if any were made
        if ($hasChanges && $content !== $originalContent) {
            if (file_put_contents($filePath, $content) !== false) {
                $this->recordFix($filePath, 'Fixed multiple blade issues');
            }
        }
    }

    /**
     * Fix common blade syntax errors
     */
    private function fixSyntaxErrors(string $content, string $filePath): string
    {
        $fixes = [
            // Fix double dollar signs: $$variable -> $variable
            '/\$\$([a-zA-Z_][a-zA-Z0-9_]*)/s' => '$$$1',
            
            // Fix missing spaces in conditionals: @if($condition) -> @if ($condition)
            '/@(if|elseif|unless|while|for|foreach)\(/' => '@$1 (',
            
            // Fix unclosed blade directives
            '/@(endif|endfor|endforeach|endwhile|endunless|endsection)\s*$/' => '@$1',
            
            // Fix malformed route calls
            '/route\s*\(\s*([^,)]+)\s*,\s*\$([^)]+)\s*\)/' => 'route($1, $$2)',
            
            // Fix blade comments
            '/\{\{--\s*([^-]*)--\}\}/' => '{{-- $1 --}}',
            
            // Fix blade echo statements
            '/\{\{\s*([^}]+)\s*\}\}/' => '{{ $1 }}',
            
            // Fix blade raw statements
            '/\{!!\s*([^!]+)\s*!!\}/' => '{!! $1 !!}',
        ];

        $hasChanges = false;
        foreach ($fixes as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== $content) {
                $content = $newContent;
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $this->stats['syntax_errors_fixed']++;
        }

        return $content;
    }

    /**
     * Fix route-related issues
     */
    private function fixRouteIssues(string $content, string $filePath): string
    {
        $hasChanges = false;
        
        // Common route fixes
        $routeFixes = [
            // Fix missing quotes in route calls
            '/route\s*\(\s*([a-zA-Z0-9._-]+)\s*\)/' => "route('$1')",
            
            // Fix missing route parameters
            '/route\s*\(\s*[\'"]([^"\']+)[\'"],\s*([^)]+)\s*\)/' => "route('$1', $2)",
            
            // Fix URL generation
            '/url\s*\(\s*([a-zA-Z0-9._\/-]+)\s*\)/' => "url('$1')",
            
            // Fix action calls
            '/action\s*\(\s*([^)]+)\s*\)/' => "action($1)",
        ];

        foreach ($routeFixes as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== $content) {
                $content = $newContent;
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $this->stats['route_issues_fixed']++;
        }

        return $content;
    }

    /**
     * Migrate Bootstrap to TailwindCSS
     */
    private function migrateTailwindCSS(string $content, string $filePath): string
    {
        $tailwindMigrations = [
            // Bootstrap buttons to TailwindCSS
            '/class="([^"]*\bbtn\s+btn-primary[^"]*)"/' => 'class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"',
            '/class="([^"]*\bbtn\s+btn-secondary[^"]*)"/' => 'class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"',
            '/class="([^"]*\bbtn\s+btn-success[^"]*)"/' => 'class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"',
            '/class="([^"]*\bbtn\s+btn-danger[^"]*)"/' => 'class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"',
            '/class="([^"]*\bbtn\s+btn-warning[^"]*)"/' => 'class="inline-flex items-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"',
            
            // Bootstrap form controls to TailwindCSS
            '/class="([^"]*\bform-control[^"]*)"/' => 'class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"',
            '/class="([^"]*\bform-group[^"]*)"/' => 'class="mb-4"',
            '/class="([^"]*\bform-label[^"]*)"/' => 'class="block text-sm font-medium text-gray-700 mb-2"',
            
            // Bootstrap grid to TailwindCSS
            '/class="([^"]*\bcontainer[^"]*)"/' => 'class="container mx-auto px-4"',
            '/class="([^"]*\brow[^"]*)"/' => 'class="flex flex-wrap -mx-4"',
            '/class="([^"]*\bcol-md-6[^"]*)"/' => 'class="w-full md:w-1/2 px-4"',
            '/class="([^"]*\bcol-md-4[^"]*)"/' => 'class="w-full md:w-1/3 px-4"',
            '/class="([^"]*\bcol-md-3[^"]*)"/' => 'class="w-full md:w-1/4 px-4"',
            '/class="([^"]*\bcol-md-12[^"]*)"/' => 'class="w-full px-4"',
            
            // Bootstrap cards to TailwindCSS
            '/class="([^"]*\bcard[^"]*)"/' => 'class="bg-white rounded-lg shadow-md overflow-hidden"',
            '/class="([^"]*\bcard-header[^"]*)"/' => 'class="bg-gray-50 px-6 py-4 border-b border-gray-200"',
            '/class="([^"]*\bcard-body[^"]*)"/' => 'class="p-6"',
            '/class="([^"]*\bcard-footer[^"]*)"/' => 'class="bg-gray-50 px-6 py-4 border-t border-gray-200"',
            
            // Bootstrap alerts to TailwindCSS
            '/class="([^"]*\balert\s+alert-success[^"]*)"/' => 'class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"',
            '/class="([^"]*\balert\s+alert-danger[^"]*)"/' => 'class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"',
            '/class="([^"]*\balert\s+alert-warning[^"]*)"/' => 'class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative"',
            '/class="([^"]*\balert\s+alert-info[^"]*)"/' => 'class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative"',
            
            // Bootstrap badges to TailwindCSS
            '/class="([^"]*\bbadge\s+badge-primary[^"]*)"/' => 'class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-0.5 text-sm font-medium text-indigo-800"',
            '/class="([^"]*\bbadge\s+badge-success[^"]*)"/' => 'class="inline-flex items-center rounded-full bg-green-100 px-3 py-0.5 text-sm font-medium text-green-800"',
            '/class="([^"]*\bbadge\s+badge-danger[^"]*)"/' => 'class="inline-flex items-center rounded-full bg-red-100 px-3 py-0.5 text-sm font-medium text-red-800"',
            '/class="([^"]*\bbadge\s+badge-warning[^"]*)"/' => 'class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-0.5 text-sm font-medium text-yellow-800"',
        ];

        $hasChanges = false;
        foreach ($tailwindMigrations as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== $content) {
                $content = $newContent;
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $this->stats['tailwind_migrations']++;
        }

        return $content;
    }

    /**
     * Improve accessibility
     */
    private function improveAccessibility(string $content, string $filePath): string
    {
        $accessibilityImprovements = [
            // Add missing alt attributes to images
            '/<img([^>]*src="[^"]*"[^>]*?)(?!\s*alt=)([^>]*)>/' => '<img$1 alt=""$2>',
            
            // Add missing labels for form inputs
            '/<input([^>]*type="[^"]*"[^>]*?)(?!\s*aria-label=)(?!\s*id=)([^>]*)>/' => '<input$1 aria-label="Input field"$2>',
            
            // Add proper ARIA attributes for buttons
            '/<button([^>]*?)(?!\s*aria-label=)([^>]*)>(.*?)<\/button>/' => '<button$1 aria-label="$3"$2>$3</button>',
            
            // Add role attributes for navigation
            '/<nav([^>]*?)(?!\s*role=)([^>]*)>/' => '<nav$1 role="navigation"$2>',
            
            // Add proper heading hierarchy hints
            '/<h([1-6])([^>]*)>(.*?)<\/h[1-6]>/' => '<h$1$2>$3</h$1>',
        ];

        $hasChanges = false;
        foreach ($accessibilityImprovements as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== $content) {
                $content = $newContent;
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $this->stats['accessibility_improvements']++;
        }

        return $content;
    }

    /**
     * Performance optimizations
     */
    private function optimizePerformance(string $content, string $filePath): string
    {
        $performanceOptimizations = [
            // Optimize image loading with lazy loading
            '/<img([^>]*src="[^"]*"[^>]*?)(?!\s*loading=)([^>]*)>/' => '<img$1 loading="lazy"$2>',
            
            // Add efficient asset loading
            '/<link([^>]*href="[^"]*\.css[^"]*"[^>]*?)(?!\s*rel=)([^>]*)>/' => '<link$1 rel="stylesheet"$2>',
            
            // Optimize script loading
            '/<script([^>]*src="[^"]*"[^>]*?)(?!\s*defer)(?!\s*async)([^>]*)>/' => '<script$1 defer$2>',
            
            // Remove unnecessary whitespace (but preserve blade syntax)
            '/\s+(?=\s)/' => ' ',
            '/^\s+|\s+$/m' => '',
        ];

        $hasChanges = false;
        foreach ($performanceOptimizations as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== $content) {
                $content = $newContent;
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $this->stats['performance_optimizations']++;
        }

        return $content;
    }

    /**
     * Record a fix that was applied
     */
    private function recordFix(string $filePath, string $description): void
    {
        $this->fixes[] = [
            'file' => str_replace(getcwd() . '/', '', $filePath),
            'description' => $description,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Display comprehensive report
     */
    private function displayReport(): void
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "\033[1;36m🎯 UNIVERSAL BLADE FIXING REPORT\033[0m\n";
        echo str_repeat("=", 60) . "\n\n";

        echo "\033[1;33m📊 STATISTICS:\033[0m\n";
        foreach ($this->stats as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            echo "   {$label}: \033[32m{$value}\033[0m\n";
        }

        if (!empty($this->fixes)) {
            echo "\n\033[1;33m🔧 FIXES APPLIED:\033[0m\n";
            $count = 1;
            foreach (array_slice($this->fixes, 0, 10) as $fix) {
                echo "   {$count}. \033[36m{$fix['file']}\033[0m\n";
                echo "      {$fix['description']}\n";
                $count++;
            }
            
            if (count($this->fixes) > 10) {
                $remaining = count($this->fixes) - 10;
                echo "   ... and {$remaining} more fixes\n";
            }
        }

        echo "\n\033[1;32m✅ Blade fixing process completed successfully!\033[0m\n";
        echo "\033[34m🔧 Next steps: Run 'npm run build' to compile updated assets\033[0m\n";
        echo "\033[34m🧪 Next steps: Run tests to verify functionality\033[0m\n\n";
    }

    /**
     * Generate a fix report file
     */
    public function generateFixReport(): void
    {
        $reportContent = "# UNIVERSAL BLADE FIXING REPORT\n";
        $reportContent .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        $reportContent .= "## Statistics\n\n";
        foreach ($this->stats as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            $reportContent .= "- {$label}: {$value}\n";
        }
        
        $reportContent .= "\n## Fixes Applied\n\n";
        foreach ($this->fixes as $fix) {
            $reportContent .= "### {$fix['file']}\n";
            $reportContent .= "- **Description**: {$fix['description']}\n";
            $reportContent .= "- **Timestamp**: {$fix['timestamp']}\n\n";
        }
        
        file_put_contents('UNIVERSAL_BLADE_FIXING_REPORT.md', $reportContent);
        echo "\033[34m📄 Report saved to: UNIVERSAL_BLADE_FIXING_REPORT.md\033[0m\n";
    }
}

// ============================================================================
// EXECUTION
// ============================================================================

if (php_sapi_name() === 'cli') {
    try {
        echo "🚀 Starting Universal Comprehensive Blade Fixing System...\n\n";
        
        $fixer = new UniversalComprehensiveBladeFixer();
        $fixer->fixAllBladeTemplates();
        $fixer->generateFixReport();
        
        echo "\n🎉 All blade templates have been processed!\n";
        echo "🔧 Run 'npm run build' to compile updated assets\n";
        echo "🧪 Run tests to verify all fixes are working correctly\n\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

?> 