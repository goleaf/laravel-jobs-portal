<?php

/**
 * Comprehensive Route Analysis Script
 * 
 * This script analyzes all blade files for route references and tests their functionality
 * according to the project update requirements.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RouteAnalyzer
{
    private $bladeFiles = [];
    private $routeReferences = [];
    private $urlReferences = [];
    private $directUrlReferences = [];
    private $errors = [];
    private $warnings = [];
    private $results = [];

    public function __construct()
    {
        $this->scanBladeFiles();
    }

    /**
     * Scan all blade files in the project
     */
    private function scanBladeFiles()
    {
        $this->bladeFiles = $this->findBladeFiles(__DIR__ . '/resources/views');
        echo "Found " . count($this->bladeFiles) . " blade files\n";
    }

    /**
     * Recursively find all blade files
     */
    private function findBladeFiles($directory)
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && Str::endsWith($file->getFilename(), '.blade.php')) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Analyze all blade files for route references
     */
    public function analyze()
    {
        echo "Analyzing blade files for route references...\n";
        
        foreach ($this->bladeFiles as $file) {
            $this->analyzeBladeFile($file);
        }

        $this->generateReport();
    }

    /**
     * Analyze a single blade file
     */
    private function analyzeBladeFile($filePath)
    {
        $content = file_get_contents($filePath);
        $relativePath = str_replace(__DIR__ . '/', '', $filePath);

        // Find route() calls
        preg_match_all('/route\([\'"]([^\'"]+)[\'"](?:\s*,\s*([^\)]+))?\)/', $content, $routeMatches);
        
        // Find url() calls
        preg_match_all('/url\([\'"]([^\'"]+)[\'"](?:\s*,\s*([^\)]+))?\)/', $content, $urlMatches);
        
        // Find direct URL references (href="/something")
        preg_match_all('/href=[\'"]([\/][^\'"]*)[\'"]/', $content, $directUrlMatches);
        
        // Find action() calls
        preg_match_all('/action\([\'"]([^\'"]+)[\'"](?:\s*,\s*([^\)]+))?\)/', $content, $actionMatches);

        // Store results
        if (!empty($routeMatches[1])) {
            $this->routeReferences[$relativePath] = $routeMatches[1];
        }

        if (!empty($urlMatches[1])) {
            $this->urlReferences[$relativePath] = $urlMatches[1];
        }

        if (!empty($directUrlMatches[1])) {
            $this->directUrlReferences[$relativePath] = $directUrlMatches[1];
        }

        // Check for hardcoded strings that should be translated
        $this->checkForHardcodedStrings($filePath, $content);
    }

    /**
     * Check for hardcoded strings that should be translated
     */
    private function checkForHardcodedStrings($filePath, $content)
    {
        $relativePath = str_replace(__DIR__ . '/', '', $filePath);
        
        // Find text that should be translated (common patterns)
        $patterns = [
            // Text inside tags
            '/>([^<>{}]+)</i',
            // Button/input values
            '/value=[\'"]([A-Za-z\s]+)[\'"]/',
            // Placeholder text
            '/placeholder=[\'"]([A-Za-z\s]+)[\'"]/',
            // Title attributes
            '/title=[\'"]([A-Za-z\s]+)[\'"]/',
        ];

        $hardcodedStrings = [];
        
        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $trimmed = trim($match);
                    // Skip if it's already using translation functions or is just whitespace/numbers
                    if (!empty($trimmed) && 
                        !preg_match('/^[\d\s\.\-\+\(\)]+$/', $trimmed) &&
                        !preg_match('/{{.*__\(.*}}/', $trimmed) &&
                        !preg_match('/@lang\(/', $trimmed) &&
                        strlen($trimmed) > 2) {
                        $hardcodedStrings[] = $trimmed;
                    }
                }
            }
        }

        if (!empty($hardcodedStrings)) {
            $this->warnings[] = [
                'file' => $relativePath,
                'type' => 'hardcoded_strings',
                'strings' => array_unique($hardcodedStrings)
            ];
        }
    }

    /**
     * Generate comprehensive report
     */
    private function generateReport()
    {
        echo "\n=== ROUTE ANALYSIS REPORT ===\n";
        
        // Route references report
        echo "\n--- ROUTE REFERENCES ---\n";
        foreach ($this->routeReferences as $file => $routes) {
            echo "File: $file\n";
            foreach ($routes as $route) {
                echo "  - route('$route')\n";
                $this->validateRoute($route, $file);
            }
            echo "\n";
        }

        // URL references report
        echo "\n--- URL REFERENCES ---\n";
        foreach ($this->urlReferences as $file => $urls) {
            echo "File: $file\n";
            foreach ($urls as $url) {
                echo "  - url('$url')\n";
            }
            echo "\n";
        }

        // Direct URL references report
        echo "\n--- DIRECT URL REFERENCES ---\n";
        foreach ($this->directUrlReferences as $file => $urls) {
            echo "File: $file\n";
            foreach ($urls as $url) {
                echo "  - href=\"$url\"\n";
            }
            echo "\n";
        }

        // Hardcoded strings report
        echo "\n--- HARDCODED STRINGS (NEED TRANSLATION) ---\n";
        foreach ($this->warnings as $warning) {
            if ($warning['type'] === 'hardcoded_strings') {
                echo "File: {$warning['file']}\n";
                foreach ($warning['strings'] as $string) {
                    echo "  - \"$string\"\n";
                }
                echo "\n";
            }
        }

        // Summary
        echo "\n--- SUMMARY ---\n";
        echo "Blade files analyzed: " . count($this->bladeFiles) . "\n";
        echo "Files with route references: " . count($this->routeReferences) . "\n";
        echo "Files with URL references: " . count($this->urlReferences) . "\n";
        echo "Files with direct URLs: " . count($this->directUrlReferences) . "\n";
        echo "Files with hardcoded strings: " . count(array_filter($this->warnings, fn($w) => $w['type'] === 'hardcoded_strings')) . "\n";

        $this->generateJsonReport();
    }

    /**
     * Validate if a route exists
     */
    private function validateRoute($routeName, $file)
    {
        try {
            // This would need to be run in Laravel context to actually check routes
            // For now, we'll just store them for later validation
            $this->results[] = [
                'file' => $file,
                'route' => $routeName,
                'type' => 'route',
                'status' => 'pending_validation'
            ];
        } catch (Exception $e) {
            $this->errors[] = [
                'file' => $file,
                'route' => $routeName,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate JSON report for programmatic processing
     */
    private function generateJsonReport()
    {
        $report = [
            'analysis_date' => date('Y-m-d H:i:s'),
            'files_analyzed' => count($this->bladeFiles),
            'route_references' => $this->routeReferences,
            'url_references' => $this->urlReferences,
            'direct_url_references' => $this->directUrlReferences,
            'hardcoded_strings' => array_filter($this->warnings, fn($w) => $w['type'] === 'hardcoded_strings'),
            'errors' => $this->errors,
            'recommendations' => $this->generateRecommendations()
        ];

        file_put_contents(__DIR__ . '/route_analysis_report.json', json_encode($report, JSON_PRETTY_PRINT));
        echo "\nDetailed JSON report saved to: route_analysis_report.json\n";
    }

    /**
     * Generate recommendations based on analysis
     */
    private function generateRecommendations()
    {
        $recommendations = [];

        // Check for common issues
        $allRoutes = [];
        foreach ($this->routeReferences as $routes) {
            $allRoutes = array_merge($allRoutes, $routes);
        }
        
        $uniqueRoutes = array_unique($allRoutes);
        
        $recommendations[] = [
            'type' => 'route_validation',
            'description' => 'Validate that all ' . count($uniqueRoutes) . ' unique routes exist in routes/web.php',
            'routes' => $uniqueRoutes
        ];

        $hardcodedCount = count(array_filter($this->warnings, fn($w) => $w['type'] === 'hardcoded_strings'));
        if ($hardcodedCount > 0) {
            $recommendations[] = [
                'type' => 'translation',
                'description' => "Convert $hardcodedCount files with hardcoded strings to use JSON translations",
                'priority' => 'high'
            ];
        }

        return $recommendations;
    }
}

// Create Laravel-specific route tester
class LaravelRouteTester
{
    public function testAllRoutes()
    {
        echo "\n=== TESTING LARAVEL ROUTES ===\n";
        
        try {
            // Initialize Laravel app
            $app = require_once __DIR__ . '/bootstrap/app.php';
            $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();

            // Get all registered routes
            $routes = Route::getRoutes();
            
            echo "Found " . count($routes) . " registered routes\n";
            
            foreach ($routes as $route) {
                $this->testRoute($route);
            }
            
        } catch (Exception $e) {
            echo "Error initializing Laravel: " . $e->getMessage() . "\n";
            echo "Run this script with: php artisan route:analyze\n";
        }
    }

    private function testRoute($route)
    {
        $name = $route->getName();
        $uri = $route->uri();
        $methods = implode('|', $route->methods());
        
        echo "Route: $name [$methods] $uri\n";
        
        // Additional route analysis can be added here
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    echo "=== COMPREHENSIVE ROUTE ANALYSIS ===\n";
    echo "Starting analysis...\n\n";

    $analyzer = new RouteAnalyzer();
    $analyzer->analyze();

    // Uncomment to test Laravel routes (requires Laravel context)
    // $routeTester = new LaravelRouteTester();
    // $routeTester->testAllRoutes();

    echo "\nAnalysis complete! Check route_analysis_report.json for detailed results.\n";
    echo "\nNext steps:\n";
    echo "1. Review the route_analysis_report.json file\n";
    echo "2. Fix any broken route references\n";
    echo "3. Convert hardcoded strings to JSON translations\n";
    echo "4. Create missing request validation files\n";
    echo "5. Test routes in browser\n";
} 