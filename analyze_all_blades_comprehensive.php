<?php

/**
 * Comprehensive Blade Template Analysis Script
 * Analyzes all blade files, finds routes, checks functionality, and identifies errors
 * 
 * As requested: "analyze all blades. find all routes in blade files and analyze does route works 
 * and what showing in browser and find all errors and fix all errors"
 */

require_once __DIR__ . '/vendor/autoload.php';

class BladeAnalyzer
{
    private $bladeFiles = [];
    private $routes = [];
    private $errors = [];
    private $results = [];
    private $routeCache = [];
    
    public function __construct()
    {
        $this->loadRoutes();
        echo "🚀 Starting Comprehensive Blade Analysis using Universal patterns...\n\n";
    }
    
    /**
     * Load all application routes
     */
    private function loadRoutes()
    {
        try {
            // Get all registered routes using Artisan
            $output = shell_exec('php artisan route:list --json 2>/dev/null');
            if ($output) {
                $routes = json_decode($output, true);
                if (is_array($routes)) {
                    foreach ($routes as $route) {
                        $this->routeCache[$route['name'] ?? 'unnamed'] = $route;
                    }
                }
            }
            echo "✅ Loaded " . count($this->routeCache) . " routes\n";
        } catch (Exception $e) {
            echo "⚠️ Could not load routes: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Find all blade template files
     */
    public function findBladeFiles()
    {
        echo "🔍 Scanning for blade templates...\n";
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('resources/views')
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                if (strpos($filePath, '.blade.php') !== false) {
                    $this->bladeFiles[] = $filePath;
                }
            }
        }
        
        echo "✅ Found " . count($this->bladeFiles) . " blade templates\n\n";
        return $this;
    }
    
    /**
     * Analyze all blade files for routes, errors, and issues
     */
    public function analyzeBlades()
    {
        echo "🔬 Analyzing blade templates for routes and errors...\n\n";
        
        foreach ($this->bladeFiles as $index => $bladeFile) {
            $progress = ($index + 1) . '/' . count($this->bladeFiles);
            echo "📄 [{$progress}] Analyzing: " . basename($bladeFile) . "\n";
            
            $analysis = $this->analyzeSingleBlade($bladeFile);
            $this->results[$bladeFile] = $analysis;
            
            // Show immediate critical issues
            if (!empty($analysis['critical_errors'])) {
                echo "  ❌ CRITICAL: " . implode(', ', $analysis['critical_errors']) . "\n";
            }
            if (!empty($analysis['routes_found'])) {
                echo "  🔗 Routes: " . count($analysis['routes_found']) . " found\n";
            }
        }
        
        return $this;
    }
    
    /**
     * Analyze a single blade file
     */
    private function analyzeSingleBlade($filePath)
    {
        $content = file_get_contents($filePath);
        $analysis = [
            'file' => $filePath,
            'size' => filesize($filePath),
            'routes_found' => [],
            'syntax_errors' => [],
            'bootstrap_usage' => [],
            'cdn_usage' => [],
            'inline_styles_js' => [],
            'missing_components' => [],
            'critical_errors' => [],
            'tailwind_migration_needed' => false,
            'security_issues' => []
        ];
        
        // Check for syntax errors
        $analysis['syntax_errors'] = $this->checkSyntaxErrors($content);
        
        // Find all routes in the blade
        $analysis['routes_found'] = $this->findRoutesInBlade($content);
        
        // Check for Bootstrap usage (should be migrated to TailwindCSS)
        $analysis['bootstrap_usage'] = $this->findBootstrapUsage($content);
        
        // Check for CDN usage (should use local npm packages)
        $analysis['cdn_usage'] = $this->findCdnUsage($content);
        
        // Check for inline CSS/JS (should be in separate files)
        $analysis['inline_styles_js'] = $this->findInlineStylesJs($content);
        
        // Check for missing components
        $analysis['missing_components'] = $this->findMissingComponents($content);
        
        // Security checks
        $analysis['security_issues'] = $this->checkSecurityIssues($content);
        
        // Determine if TailwindCSS migration is needed
        $analysis['tailwind_migration_needed'] = !empty($analysis['bootstrap_usage']) || 
                                                 !empty($analysis['cdn_usage']);
        
        // Mark critical errors
        if (!empty($analysis['syntax_errors'])) {
            $analysis['critical_errors'][] = 'Syntax errors found';
        }
        if (!empty($analysis['security_issues'])) {
            $analysis['critical_errors'][] = 'Security issues found';
        }
        
        return $analysis;
    }
    
    /**
     * Check for blade syntax errors
     */
    private function checkSyntaxErrors($content)
    {
        $errors = [];
        
        // Check for common Blade syntax errors
        $patterns = [
            '/\{\{\s*--.*?--\s*\}\}/' => 'Incorrect comment syntax (use {{-- --}})',
            '/\$\$[a-zA-Z_]/' => 'Double dollar sign variable syntax error',
            '/\{\{[^}]*\}\}[^}]/' => 'Potential malformed Blade echo',
            '/@[a-zA-Z]+\([^)]*\)[^(\s]/' => 'Potential malformed Blade directive',
            '/\{\{[^}]*\$[^}]*\$[^}]*\}\}/' => 'Multiple dollar signs in single echo',
        ];
        
        foreach ($patterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $errors[] = $description;
            }
        }
        
        // Check for unclosed tags
        $openTags = substr_count($content, '{{');
        $closeTags = substr_count($content, '}}');
        if ($openTags !== $closeTags) {
            $errors[] = 'Mismatched Blade echo tags {{ }}';
        }
        
        return $errors;
    }
    
    /**
     * Find all routes referenced in blade file
     */
    private function findRoutesInBlade($content)
    {
        $routes = [];
        
        // Find route() calls
        preg_match_all('/route\s*\(\s*[\'"]([^\'"]*)[\'"]/', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $routeName) {
                $routes[] = [
                    'type' => 'named_route',
                    'name' => $routeName,
                    'exists' => isset($this->routeCache[$routeName])
                ];
            }
        }
        
        // Find URL() calls
        preg_match_all('/url\s*\(\s*[\'"]([^\'"]*)[\'"]/', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $url) {
                $routes[] = [
                    'type' => 'url',
                    'url' => $url,
                    'exists' => true // URLs are harder to validate
                ];
            }
        }
        
        // Find href attributes
        preg_match_all('/href\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $href) {
                if (!in_array($href, ['#', 'javascript:void(0)', 'javascript:;'])) {
                    $routes[] = [
                        'type' => 'href',
                        'url' => $href,
                        'exists' => true
                    ];
                }
            }
        }
        
        // Find form actions
        preg_match_all('/action\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $action) {
                $routes[] = [
                    'type' => 'form_action',
                    'url' => $action,
                    'exists' => true
                ];
            }
        }
        
        return $routes;
    }
    
    /**
     * Find Bootstrap CSS classes (should be migrated to TailwindCSS)
     */
    private function findBootstrapUsage($content)
    {
        $bootstrap = [];
        
        $bootstrapClasses = [
            'btn btn-primary', 'btn btn-secondary', 'btn btn-success', 'btn btn-danger',
            'form-control', 'form-label', 'form-group', 'form-check',
            'container', 'container-fluid', 'row', 'col-', 'col-md-', 'col-lg-',
            'navbar', 'nav-link', 'dropdown', 'modal', 'card', 'alert',
            'badge', 'pagination', 'breadcrumb', 'list-group',
            'fs-', 'ps-', 'pe-', 'ms-', 'me-', 'mt-', 'mb-', 'pt-', 'pb-'
        ];
        
        foreach ($bootstrapClasses as $class) {
            if (strpos($content, $class) !== false) {
                $bootstrap[] = $class;
            }
        }
        
        return array_unique($bootstrap);
    }
    
    /**
     * Find CDN usage (should use local npm packages)
     */
    private function findCdnUsage($content)
    {
        $cdnUsage = [];
        
        $cdnPatterns = [
            'cdn\.jsdelivr\.net' => 'jsDelivr CDN',
            'cdnjs\.cloudflare\.com' => 'Cloudflare CDN',
            'maxcdn\.bootstrapcdn\.com' => 'Bootstrap CDN',
            'code\.jquery\.com' => 'jQuery CDN',
            'fonts\.googleapis\.com' => 'Google Fonts CDN',
            'unpkg\.com' => 'unpkg CDN',
            'stackpath\.bootstrapcdn\.com' => 'StackPath CDN',
        ];
        
        foreach ($cdnPatterns as $pattern => $description) {
            if (preg_match('/' . $pattern . '/', $content)) {
                $cdnUsage[] = $description;
            }
        }
        
        return array_unique($cdnUsage);
    }
    
    /**
     * Find inline CSS and JavaScript (should be in separate files)
     */
    private function findInlineStylesJs($content)
    {
        $inline = [];
        
        // Check for <style> tags
        if (preg_match_all('/<style[^>]*>.*?<\/style>/s', $content, $matches)) {
            $inline['css_blocks'] = count($matches[0]);
        }
        
        // Check for <script> tags with content
        if (preg_match_all('/<script[^>]*>(?!\s*<\/script>).*?<\/script>/s', $content, $matches)) {
            $inline['js_blocks'] = count($matches[0]);
        }
        
        // Check for inline style attributes
        if (preg_match_all('/style\s*=\s*[\'"][^\'"]*[\'"]/', $content, $matches)) {
            $inline['inline_styles'] = count($matches[0]);
        }
        
        // Check for inline event handlers
        $eventHandlers = ['onclick', 'onchange', 'onload', 'onsubmit'];
        foreach ($eventHandlers as $handler) {
            if (preg_match_all('/' . $handler . '\s*=\s*[\'"][^\'"]*[\'"]/', $content, $matches)) {
                $inline['event_handlers'][$handler] = count($matches[0]);
            }
        }
        
        return $inline;
    }
    
    /**
     * Find potentially missing components
     */
    private function findMissingComponents($content)
    {
        $missing = [];
        
        // Find component usage
        preg_match_all('/<x-([a-zA-Z0-9\-\.]+)/', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $component) {
                $componentFile = 'resources/views/components/' . str_replace('.', '/', $component) . '.blade.php';
                if (!file_exists($componentFile)) {
                    $missing[] = $component;
                }
            }
        }
        
        return array_unique($missing);
    }
    
    /**
     * Check for security issues
     */
    private function checkSecurityIssues($content)
    {
        $issues = [];
        
        // Check for unescaped output
        if (preg_match('/\{!![^}]*!!\}/', $content)) {
            $issues[] = 'Unescaped output found {!! !!} - potential XSS risk';
        }
        
        // Check for direct $_GET, $_POST usage
        if (preg_match('/\$_(GET|POST|REQUEST)/', $content)) {
            $issues[] = 'Direct superglobal usage - use Request object instead';
        }
        
        // Check for eval() usage
        if (strpos($content, 'eval(') !== false) {
            $issues[] = 'eval() usage found - security risk';
        }
        
        // Check for missing CSRF in forms
        if (preg_match('/<form[^>]*method\s*=\s*[\'"]post[\'"][^>]*>/i', $content)) {
            if (!preg_match('/@csrf|csrf_token/', $content)) {
                $issues[] = 'POST form without CSRF protection';
            }
        }
        
        return $issues;
    }
    
    /**
     * Generate comprehensive report
     */
    public function generateReport()
    {
        echo "\n\n" . str_repeat("=", 80) . "\n";
        echo "🎯 COMPREHENSIVE BLADE ANALYSIS REPORT (Universal Enhanced)\n";
        echo str_repeat("=", 80) . "\n\n";
        
        $this->generateSummary();
        $this->generateCriticalIssues();
        $this->generateRouteAnalysis();
        $this->generateMigrationNeeds();
        $this->generateRecommendations();
        
        // Save detailed JSON report
        $this->saveJsonReport();
        
        echo "\n✅ Analysis complete! Check 'blade_analysis_comprehensive_report.json' for detailed results.\n";
    }
    
    private function generateSummary()
    {
        echo "📊 SUMMARY\n";
        echo str_repeat("-", 40) . "\n";
        
        $totalFiles = count($this->results);
        $filesWithErrors = 0;
        $totalRoutes = 0;
        $filesNeedingMigration = 0;
        
        foreach ($this->results as $result) {
            if (!empty($result['critical_errors'])) {
                $filesWithErrors++;
            }
            $totalRoutes += count($result['routes_found']);
            if ($result['tailwind_migration_needed']) {
                $filesNeedingMigration++;
            }
        }
        
        echo "Total Blade Files: {$totalFiles}\n";
        echo "Files with Critical Errors: {$filesWithErrors}\n";
        echo "Total Routes Found: {$totalRoutes}\n";
        echo "Files Needing TailwindCSS Migration: {$filesNeedingMigration}\n";
        echo "Files Using CDN (should be local): " . $this->countFilesWithCdn() . "\n";
        echo "\n";
    }
    
    private function generateCriticalIssues()
    {
        echo "🚨 CRITICAL ISSUES REQUIRING IMMEDIATE ATTENTION\n";
        echo str_repeat("-", 50) . "\n";
        
        $count = 0;
        foreach ($this->results as $filePath => $result) {
            if (!empty($result['critical_errors'])) {
                $count++;
                echo "{$count}. " . basename($filePath) . "\n";
                foreach ($result['critical_errors'] as $error) {
                    echo "   ❌ {$error}\n";
                }
                
                // Show specific syntax errors
                if (!empty($result['syntax_errors'])) {
                    foreach ($result['syntax_errors'] as $syntaxError) {
                        echo "      - {$syntaxError}\n";
                    }
                }
                
                // Show security issues
                if (!empty($result['security_issues'])) {
                    foreach ($result['security_issues'] as $securityIssue) {
                        echo "      - {$securityIssue}\n";
                    }
                }
                echo "\n";
            }
        }
        
        if ($count === 0) {
            echo "✅ No critical issues found!\n\n";
        }
    }
    
    private function generateRouteAnalysis()
    {
        echo "🔗 ROUTE ANALYSIS\n";
        echo str_repeat("-", 30) . "\n";
        
        $allRoutes = [];
        $brokenRoutes = [];
        
        foreach ($this->results as $result) {
            foreach ($result['routes_found'] as $route) {
                $allRoutes[] = $route;
                if (isset($route['exists']) && !$route['exists']) {
                    $brokenRoutes[] = $route;
                }
            }
        }
        
        echo "Total Routes Found: " . count($allRoutes) . "\n";
        echo "Potentially Broken Routes: " . count($brokenRoutes) . "\n";
        
        if (!empty($brokenRoutes)) {
            echo "\nBroken Routes:\n";
            foreach (array_slice($brokenRoutes, 0, 10) as $i => $route) {
                echo "  " . ($i + 1) . ". {$route['type']}: {$route['name']}\n";
            }
            if (count($brokenRoutes) > 10) {
                echo "  ... and " . (count($brokenRoutes) - 10) . " more\n";
            }
        }
        echo "\n";
    }
    
    private function generateMigrationNeeds()
    {
        echo "🔄 MIGRATION REQUIREMENTS (as per user guidelines)\n";
        echo str_repeat("-", 50) . "\n";
        
        $bootstrapFiles = 0;
        $cdnFiles = 0;
        $inlineCodeFiles = 0;
        
        foreach ($this->results as $result) {
            if (!empty($result['bootstrap_usage'])) {
                $bootstrapFiles++;
            }
            if (!empty($result['cdn_usage'])) {
                $cdnFiles++;
            }
            if (!empty($result['inline_styles_js'])) {
                $inlineCodeFiles++;
            }
        }
        
        echo "Files using Bootstrap (migrate to TailwindCSS): {$bootstrapFiles}\n";
        echo "Files using CDN (migrate to local npm): {$cdnFiles}\n";
        echo "Files with inline CSS/JS (move to separate files): {$inlineCodeFiles}\n";
        echo "\n";
    }
    
    private function generateRecommendations()
    {
        echo "💡 UNIVERSAL RECOMMENDATIONS\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. PRIORITY FIXES:\n";
        echo "   • Fix all syntax errors immediately\n";
        echo "   • Address security issues (CSRF, XSS prevention)\n";
        echo "   • Validate all routes and fix broken ones\n\n";
        
        echo "2. TAILWINDCSS MIGRATION:\n";
        echo "   • Replace Bootstrap classes with TailwindCSS equivalents\n";
        echo "   • Use modern utility-first approach\n";
        echo "   • Implement responsive design patterns\n\n";
        
        echo "3. ASSET MANAGEMENT:\n";
        echo "   • Move all CDN resources to local npm packages\n";
        echo "   • Extract inline CSS/JS to separate files\n";
        echo "   • Use Vite for asset compilation\n\n";
        
        echo "4. COMPONENT OPTIMIZATION:\n";
        echo "   • Create reusable Blade components\n";
        echo "   • Minimize UI component files count\n";
        echo "   • Follow Laravel component best practices\n\n";
        
        echo "5. BUILD PROCESS:\n";
        echo "   • Run 'npm run build' after CSS/JS changes\n";
        echo "   • Use single layout approach\n";
        echo "   • Implement proper asset versioning\n\n";
    }
    
    private function countFilesWithCdn()
    {
        $count = 0;
        foreach ($this->results as $result) {
            if (!empty($result['cdn_usage'])) {
                $count++;
            }
        }
        return $count;
    }
    
    private function saveJsonReport()
    {
        $report = [
            'analysis_date' => date('Y-m-d H:i:s'),
            'total_files' => count($this->results),
            'summary' => [
                'files_with_errors' => count(array_filter($this->results, fn($r) => !empty($r['critical_errors']))),
                'total_routes' => array_sum(array_map(fn($r) => count($r['routes_found']), $this->results)),
                'files_needing_migration' => count(array_filter($this->results, fn($r) => $r['tailwind_migration_needed'])),
                'files_with_cdn' => $this->countFilesWithCdn(),
            ],
            'detailed_results' => $this->results
        ];
        
        file_put_contents('blade_analysis_comprehensive_report.json', json_encode($report, JSON_PRETTY_PRINT));
    }
}

// Execute the analysis
try {
    $analyzer = new BladeAnalyzer();
    $analyzer->findBladeFiles()
             ->analyzeBlades()
             ->generateReport();
             
} catch (Exception $e) {
    echo "❌ Error during analysis: " . $e->getMessage() . "\n";
    exit(1);
} 