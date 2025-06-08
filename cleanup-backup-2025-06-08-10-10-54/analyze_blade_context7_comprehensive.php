<?php

/**
 * Context7 Comprehensive Blade Analysis System
 * Laravel 12 Job Portal - Route & Security Validation
 * 
 * Analyzes all blade templates for:
 * - Route validation and broken links
 * - XSS vulnerabilities (unsafe {!! !!} usage)
 * - Blade syntax errors
 * - TailwindCSS migration status
 * - Context7 best practices compliance
 */

require_once __DIR__ . '/vendor/autoload.php';

class Context7BladeAnalyzer
{
    private $bladeFiles = [];
    private $routes = [];
    private $issues = [];
    private $stats = [
        'total_files' => 0,
        'syntax_errors' => 0,
        'route_issues' => 0,
        'xss_vulnerabilities' => 0,
        'bootstrap_usage' => 0,
        'success_rate' => 0
    ];

    public function __construct()
    {
        echo "🚀 Context7 Comprehensive Blade Analysis Starting...\n";
        echo "📋 Laravel 12 Job Portal - Route & Security Validation\n\n";
    }

    /**
     * Context7 Pattern: Comprehensive Analysis Pipeline
     */
    public function analyzeAll()
    {
        $this->loadRoutes();
        $this->scanBladeFiles();
        $this->analyzeBladeTemplates();
        $this->generateContext7Report();
    }

    /**
     * Load all available routes from Laravel application
     */
    private function loadRoutes()
    {
        echo "📍 Loading Laravel routes...\n";
        
        // Get routes from artisan route:list
        $routeOutput = shell_exec('php artisan route:list --json 2>/dev/null');
        
        if ($routeOutput) {
            $routeData = json_decode($routeOutput, true);
            if ($routeData) {
                foreach ($routeData as $route) {
                    if (isset($route['name'])) {
                        $this->routes[] = $route['name'];
                    }
                }
            }
        }

        // Fallback: Parse routes from web.php and api.php
        $routeFiles = ['routes/web.php', 'routes/api.php'];
        foreach ($routeFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                // Extract route names with regex
                preg_match_all("/->name\(['\"]([^'\"]*)['\"]]/", $content, $matches);
                if (!empty($matches[1])) {
                    $this->routes = array_merge($this->routes, $matches[1]);
                }
            }
        }

        $this->routes = array_unique($this->routes);
        echo "✅ Loaded " . count($this->routes) . " routes\n\n";
    }

    /**
     * Scan all blade template files
     */
    private function scanBladeFiles()
    {
        echo "🔍 Scanning blade template files...\n";
        
        $directories = [
            'resources/views',
            'resources/views/components'
        ];

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir)
                );

                foreach ($iterator as $file) {
                    if ($file->getExtension() === 'php' && 
                        strpos($file->getFilename(), '.blade.') !== false) {
                        $this->bladeFiles[] = $file->getPathname();
                    }
                }
            }
        }

        $this->stats['total_files'] = count($this->bladeFiles);
        echo "✅ Found " . $this->stats['total_files'] . " blade files\n\n";
    }

    /**
     * Context7 Pattern: Comprehensive Blade Template Analysis
     */
    private function analyzeBladeTemplates()
    {
        echo "🔬 Analyzing blade templates with Context7 patterns...\n";
        
        $progressBar = 0;
        $totalFiles = count($this->bladeFiles);

        foreach ($this->bladeFiles as $file) {
            $progressBar++;
            $this->analyzeBladeFile($file);
            
            // Progress indicator
            if ($progressBar % 50 === 0 || $progressBar === $totalFiles) {
                $percentage = round(($progressBar / $totalFiles) * 100, 1);
                echo "📊 Progress: {$progressBar}/{$totalFiles} files ({$percentage}%)\n";
            }
        }

        echo "\n";
    }

    /**
     * Analyze individual blade file for all Context7 criteria
     */
    private function analyzeBladeFile($filePath)
    {
        $content = file_get_contents($filePath);
        $relativePath = str_replace(getcwd() . '/', '', $filePath);
        $fileIssues = [];

        // 1. Context7 Blade Syntax Analysis
        $syntaxIssues = $this->analyzeBladeSyntax($content);
        if (!empty($syntaxIssues)) {
            $fileIssues['syntax'] = $syntaxIssues;
            $this->stats['syntax_errors']++;
        }

        // 2. Route Validation Analysis
        $routeIssues = $this->analyzeRoutes($content);
        if (!empty($routeIssues)) {
            $fileIssues['routes'] = $routeIssues;
            $this->stats['route_issues']++;
        }

        // 3. XSS Security Analysis
        $xssIssues = $this->analyzeXSSSecurity($content);
        if (!empty($xssIssues)) {
            $fileIssues['xss'] = $xssIssues;
            $this->stats['xss_vulnerabilities']++;
        }

        // 4. TailwindCSS Migration Analysis
        $bootstrapUsage = $this->analyzeBootstrapUsage($content);
        if (!empty($bootstrapUsage)) {
            $fileIssues['bootstrap'] = $bootstrapUsage;
            $this->stats['bootstrap_usage']++;
        }

        // Store issues if any found
        if (!empty($fileIssues)) {
            $this->issues[$relativePath] = $fileIssues;
        }
    }

    /**
     * Context7 Pattern: Blade Syntax Validation
     */
    private function analyzeBladeSyntax($content)
    {
        $issues = [];

        // Check for double dollar signs (common error)
        if (preg_match('/\$\$[a-zA-Z_]/', $content)) {
            $issues[] = "Double dollar sign error ($$variable)";
        }

        // Check for malformed Blade comments
        if (preg_match('/\{\{--(?!.*--\}\})/', $content)) {
            $issues[] = "Unclosed Blade comment {{-- --}}";
        }

        // Check for incorrect echo syntax
        if (preg_match('/\{\{[^}]*\{\{/', $content)) {
            $issues[] = "Nested echo syntax {{  {{ }}";
        }

        // Check for malformed directives
        if (preg_match('/@[a-zA-Z]+\([^)]*$/', $content)) {
            $issues[] = "Unclosed Blade directive";
        }

        return $issues;
    }

    /**
     * Context7 Pattern: Route Validation Analysis
     */
    private function analyzeRoutes($content)
    {
        $issues = [];

        // Find all route() calls
        preg_match_all("/route\(['\"]([^'\"]*)['\"](?:,\s*\[([^\]]*)\])?\)/", $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $routeName = $match[1];
            
            // Check for incomplete routes (ending with dots)
            if (preg_match('/\.$/', $routeName)) {
                $issues[] = "Incomplete route: route('{$routeName}')";
                continue;
            }

            // Check if route exists in our loaded routes
            if (!empty($this->routes) && !in_array($routeName, $this->routes)) {
                $issues[] = "Unknown route: route('{$routeName}')";
            }
        }

        // Check for malformed route calls
        if (preg_match('/route\([^)]*[^\'"])$/', $content)) {
            $issues[] = "Malformed route() call - missing closing quote/parenthesis";
        }

        return $issues;
    }

    /**
     * Context7 Pattern: XSS Security Analysis
     */
    private function analyzeXSSSecurity($content)
    {
        $issues = [];

        // Find all unescaped output {!! !!}
        preg_match_all('/\{!!\s*([^}]+)\s*!!\}/', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $variable = trim($match[1]);
            
            // Check if it's potentially unsafe
            if (!$this->isSafeUnescapedOutput($variable)) {
                $issues[] = "Potentially unsafe unescaped output: {!! {$variable} !!}";
            }
        }

        return $issues;
    }

    /**
     * Determine if unescaped output is safe
     */
    private function isSafeUnescapedOutput($variable)
    {
        // Safe patterns for unescaped output
        $safePatterns = [
            '/\$__env->make/',           // View includes
            '/\$slot/',                  // Component slots
            '/Blade::render/',           // Blade rendering
            '/view\(/',                  // View calls
            '/\$errors->/',              // Error bags
            '/csrf_field\(\)/',          // CSRF tokens
            '/method_field\(\)/',        // Method fields
        ];

        foreach ($safePatterns as $pattern) {
            if (preg_match($pattern, $variable)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Context7 Pattern: Bootstrap Usage Analysis
     */
    private function analyzeBootstrapUsage($content)
    {
        $issues = [];

        // Common Bootstrap classes that should be migrated
        $bootstrapClasses = [
            'btn', 'btn-primary', 'btn-secondary', 'btn-success', 'btn-danger',
            'form-control', 'form-group', 'form-label', 'form-check',
            'container', 'container-fluid', 'row', 'col-', 'col-md-',
            'card', 'card-body', 'card-header', 'card-footer',
            'navbar', 'nav-link', 'nav-item', 'dropdown',
            'table', 'table-striped', 'table-bordered',
            'alert', 'alert-success', 'alert-danger', 'alert-warning',
            'modal', 'modal-dialog', 'modal-content', 'modal-header'
        ];

        foreach ($bootstrapClasses as $class) {
            if (preg_match("/class=['\"][^'\"]*\\b{$class}\\b/", $content)) {
                $issues[] = "Bootstrap class found: {$class}";
            }
        }

        return $issues;
    }

    /**
     * Context7 Pattern: Comprehensive Report Generation
     */
    private function generateContext7Report()
    {
        $this->calculateSuccessRate();
        
        echo "📊 CONTEXT7 BLADE ANALYSIS REPORT\n";
        echo "================================\n\n";

        // Overall Statistics
        echo "📈 OVERALL STATISTICS:\n";
        echo "- Total Blade Files: " . $this->stats['total_files'] . "\n";
        echo "- Files with Syntax Errors: " . $this->stats['syntax_errors'] . "\n";
        echo "- Files with Route Issues: " . $this->stats['route_issues'] . "\n";
        echo "- Files with XSS Vulnerabilities: " . $this->stats['xss_vulnerabilities'] . "\n";
        echo "- Files with Bootstrap Usage: " . $this->stats['bootstrap_usage'] . "\n";
        echo "- Success Rate: " . $this->stats['success_rate'] . "%\n\n";

        // Priority Issues
        echo "🚨 PRIORITY ISSUES TO FIX:\n";
        $this->reportPriorityIssues();

        // Detailed Issues by File
        echo "\n📋 DETAILED ISSUES BY FILE:\n";
        $this->reportDetailedIssues();

        // Context7 Recommendations
        echo "\n💡 CONTEXT7 RECOMMENDATIONS:\n";
        $this->generateRecommendations();

        // Save detailed report
        $this->saveDetailedReport();
    }

    /**
     * Calculate overall success rate
     */
    private function calculateSuccessRate()
    {
        $totalFiles = $this->stats['total_files'];
        $totalIssues = $this->stats['syntax_errors'] + $this->stats['route_issues'] + 
                      $this->stats['xss_vulnerabilities'] + $this->stats['bootstrap_usage'];
        
        if ($totalFiles > 0) {
            $successFiles = $totalFiles - count($this->issues);
            $this->stats['success_rate'] = round(($successFiles / $totalFiles) * 100, 1);
        }
    }

    /**
     * Report high-priority issues
     */
    private function reportPriorityIssues()
    {
        $priorities = [
            'syntax' => 'CRITICAL: Blade Syntax Errors',
            'routes' => 'HIGH: Route Issues', 
            'xss' => 'HIGH: XSS Vulnerabilities',
            'bootstrap' => 'MEDIUM: Bootstrap Migration'
        ];

        foreach ($priorities as $type => $label) {
            $count = 0;
            foreach ($this->issues as $file => $issues) {
                if (isset($issues[$type])) {
                    $count++;
                }
            }
            
            if ($count > 0) {
                echo "- {$label}: {$count} files\n";
            }
        }
    }

    /**
     * Report detailed issues
     */
    private function reportDetailedIssues()
    {
        $count = 0;
        foreach ($this->issues as $file => $fileIssues) {
            $count++;
            if ($count > 20) { // Limit output
                echo "... and " . (count($this->issues) - 20) . " more files with issues\n";
                break;
            }

            echo "\n📄 {$file}:\n";
            foreach ($fileIssues as $category => $issues) {
                foreach ($issues as $issue) {
                    echo "  - [{$category}] {$issue}\n";
                }
            }
        }
    }

    /**
     * Generate Context7 recommendations
     */
    private function generateRecommendations()
    {
        echo "1. 🔧 Fix syntax errors first (prevents compilation failures)\n";
        echo "2. 🛡️ Address XSS vulnerabilities immediately (security risk)\n";
        echo "3. 🔗 Fix broken routes (improves user experience)\n";
        echo "4. 🎨 Complete TailwindCSS migration (removes Bootstrap dependency)\n";
        echo "5. 🧪 Run tests after each fix to ensure stability\n";
    }

    /**
     * Save detailed report to file
     */
    private function saveDetailedReport()
    {
        $reportData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'stats' => $this->stats,
            'issues' => $this->issues,
            'routes_found' => count($this->routes)
        ];

        $reportFile = 'context7_blade_analysis_report.json';
        file_put_contents($reportFile, json_encode($reportData, JSON_PRETTY_PRINT));
        
        echo "\n💾 Detailed report saved to: {$reportFile}\n";
    }
}

// Execute Context7 Analysis
$analyzer = new Context7BladeAnalyzer();
$analyzer->analyzeAll();

echo "\n🎯 Context7 Blade Analysis Complete!\n";
echo "📋 Review the issues above and proceed with fixes using Laravel 12 patterns.\n"; 