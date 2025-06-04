<?php

/**
 * Comprehensive Route Analysis Script
 * Analyzes all blade files for route references and checks their validity
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteAnalyzer
{
    private $bladeFiles = [];
    private $routeReferences = [];
    private $existingRoutes = [];
    private $missingRoutes = [];
    private $errors = [];
    private $stats = [
        'total_blade_files' => 0,
        'total_route_references' => 0,
        'unique_routes' => 0,
        'missing_routes' => 0,
        'valid_routes' => 0
    ];

    public function __construct()
    {
        $this->loadLaravelApp();
        $this->scanBladeFiles();
        $this->extractRouteReferences();
        $this->checkRouteExistence();
        $this->generateReport();
    }

    private function loadLaravelApp()
    {
        try {
            // Bootstrap Laravel application
            $app = require_once __DIR__ . '/bootstrap/app.php';
            $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
            
            // Load all routes
            require __DIR__ . '/routes/web.php';
            if (file_exists(__DIR__ . '/routes/api.php')) {
                require __DIR__ . '/routes/api.php';
            }
            
            $this->existingRoutes = collect(Route::getRoutes())->mapWithKeys(function ($route) {
                return [$route->getName() => $route];
            })->filter()->toArray();
            
            echo "✅ Laravel application loaded successfully\n";
            echo "📊 Found " . count($this->existingRoutes) . " named routes\n\n";
            
        } catch (Exception $e) {
            echo "❌ Error loading Laravel: " . $e->getMessage() . "\n";
            $this->errors[] = "Laravel loading error: " . $e->getMessage();
        }
    }

    private function scanBladeFiles()
    {
        $directories = [
            'resources/views',
            'resources/views/admin',
            'resources/views/candidate',
            'resources/views/employer',
            'resources/views/front_web',
            'resources/views/front_web_template'
        ];

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $this->scanDirectory($dir);
            }
        }

        $this->stats['total_blade_files'] = count($this->bladeFiles);
        echo "📁 Scanned " . $this->stats['total_blade_files'] . " blade files\n";
    }

    private function scanDirectory($directory)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php' && Str::contains($file->getFilename(), '.blade.')) {
                $this->bladeFiles[] = $file->getPathname();
            }
        }
    }

    private function extractRouteReferences()
    {
        $routePatterns = [
            // route() function calls
            '/route\s*\(\s*[\'"]([^\'\"]+)[\'"]\s*(?:,\s*\[[^\]]*\])?\s*\)/',
            // Named route references in forms and links
            '/action\s*=\s*[\'"]{{?\s*route\s*\(\s*[\'"]([^\'\"]+)[\'"]\s*(?:,\s*[^}]*)?\s*}}?[\'"]/',
            // href route references
            '/href\s*=\s*[\'"]{{?\s*route\s*\(\s*[\'"]([^\'\"]+)[\'"]\s*(?:,\s*[^}]*)?\s*}}?[\'"]/',
            // @route directive (if exists)
            '/@route\s*\(\s*[\'"]([^\'\"]+)[\'"]\s*(?:,\s*[^)]*)?\s*\)/',
            // URL::route calls
            '/URL::route\s*\(\s*[\'"]([^\'\"]+)[\'"]\s*(?:,\s*[^)]*)?\s*\)/',
            // Redirect::route calls
            '/Redirect::route\s*\(\s*[\'"]([^\'\"]+)[\'"]\s*(?:,\s*[^)]*)?\s*\)/',
            // redirect()->route calls
            '/redirect\s*\(\s*\)\s*->\s*route\s*\(\s*[\'"]([^\'\"]+)[\'"]\s*(?:,\s*[^)]*)?\s*\)/'
        ];

        foreach ($this->bladeFiles as $file) {
            $content = file_get_contents($file);
            $relativePath = str_replace(__DIR__ . '/', '', $file);

            foreach ($routePatterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches)) {
                    foreach ($matches[1] as $routeName) {
                        $this->routeReferences[] = [
                            'route' => $routeName,
                            'file' => $relativePath,
                            'exists' => false
                        ];
                    }
                }
            }
        }

        $this->stats['total_route_references'] = count($this->routeReferences);
        $uniqueRoutes = array_unique(array_column($this->routeReferences, 'route'));
        $this->stats['unique_routes'] = count($uniqueRoutes);
        
        echo "🔍 Found " . $this->stats['total_route_references'] . " route references\n";
        echo "🎯 Found " . $this->stats['unique_routes'] . " unique routes\n";
    }

    private function checkRouteExistence()
    {
        $routeCounts = [];
        
        foreach ($this->routeReferences as &$reference) {
            $routeName = $reference['route'];
            
            if (isset($this->existingRoutes[$routeName])) {
                $reference['exists'] = true;
                $this->stats['valid_routes']++;
            } else {
                $this->missingRoutes[] = $reference;
                $this->stats['missing_routes']++;
            }
            
            // Count occurrences
            if (!isset($routeCounts[$routeName])) {
                $routeCounts[$routeName] = 0;
            }
            $routeCounts[$routeName]++;
        }

        // Sort missing routes by frequency
        $missingRouteCounts = [];
        foreach ($this->missingRoutes as $missing) {
            $route = $missing['route'];
            if (!isset($missingRouteCounts[$route])) {
                $missingRouteCounts[$route] = 0;
            }
            $missingRouteCounts[$route]++;
        }
        
        arsort($missingRouteCounts);
        
        echo "\n📈 Route Analysis Complete:\n";
        echo "✅ Valid routes: " . $this->stats['valid_routes'] . "\n";
        echo "❌ Missing routes: " . $this->stats['missing_routes'] . "\n";
        echo "📊 Success rate: " . round(($this->stats['valid_routes'] / $this->stats['total_route_references']) * 100, 2) . "%\n\n";
        
        if (!empty($missingRouteCounts)) {
            echo "🚨 TOP MISSING ROUTES (by frequency):\n";
            $count = 0;
            foreach ($missingRouteCounts as $route => $frequency) {
                if ($count++ >= 20) break; // Show top 20
                echo "   • $route ($frequency occurrences)\n";
            }
            echo "\n";
        }
    }

    private function generateReport()
    {
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'statistics' => $this->stats,
            'existing_routes' => array_keys($this->existingRoutes),
            'missing_routes' => $this->getMissingRoutesList(),
            'route_references' => $this->routeReferences,
            'errors' => $this->errors
        ];

        // Save detailed JSON report
        file_put_contents('blade_route_analysis_comprehensive.json', json_encode($report, JSON_PRETTY_PRINT));
        
        // Generate summary markdown
        $this->generateMarkdownSummary($report);
        
        echo "📄 Reports generated:\n";
        echo "   • blade_route_analysis_comprehensive.json (detailed)\n";
        echo "   • BLADE_ROUTE_ANALYSIS_SUMMARY.md (summary)\n\n";
    }

    private function getMissingRoutesList()
    {
        $missing = [];
        foreach ($this->missingRoutes as $route) {
            $routeName = $route['route'];
            if (!isset($missing[$routeName])) {
                $missing[$routeName] = [
                    'route' => $routeName,
                    'files' => [],
                    'count' => 0
                ];
            }
            $missing[$routeName]['files'][] = $route['file'];
            $missing[$routeName]['count']++;
        }
        
        // Sort by count descending
        uasort($missing, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return array_values($missing);
    }

    private function generateMarkdownSummary($report)
    {
        $markdown = "# Blade Route Analysis Summary\n\n";
        $markdown .= "**Generated:** " . $report['timestamp'] . "\n\n";
        
        $markdown .= "## 📊 Statistics\n\n";
        $markdown .= "| Metric | Count |\n";
        $markdown .= "|--------|-------|\n";
        foreach ($report['statistics'] as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            $markdown .= "| $label | $value |\n";
        }
        
        $successRate = round(($report['statistics']['valid_routes'] / $report['statistics']['total_route_references']) * 100, 2);
        $markdown .= "| Success Rate | {$successRate}% |\n\n";
        
        if (!empty($report['missing_routes'])) {
            $markdown .= "## ❌ Missing Routes (Top 20)\n\n";
            $markdown .= "| Route Name | Occurrences | Files |\n";
            $markdown .= "|------------|-------------|-------|\n";
            
            $count = 0;
            foreach ($report['missing_routes'] as $missing) {
                if ($count++ >= 20) break;
                $files = implode(', ', array_slice($missing['files'], 0, 3));
                if (count($missing['files']) > 3) {
                    $files .= '... +' . (count($missing['files']) - 3) . ' more';
                }
                $markdown .= "| `{$missing['route']}` | {$missing['count']} | {$files} |\n";
            }
            $markdown .= "\n";
        }
        
        if (!empty($report['errors'])) {
            $markdown .= "## 🚨 Errors\n\n";
            foreach ($report['errors'] as $error) {
                $markdown .= "- $error\n";
            }
            $markdown .= "\n";
        }
        
        $markdown .= "## 🎯 Next Steps\n\n";
        $markdown .= "1. **Fix Missing Routes**: Add route definitions for the missing routes listed above\n";
        $markdown .= "2. **Verify Controllers**: Ensure all controller methods exist for the routes\n";
        $markdown .= "3. **Test Routes**: Manually test critical routes in browser\n";
        $markdown .= "4. **Update Middleware**: Verify authentication and authorization requirements\n";
        $markdown .= "5. **Run Tests**: Execute feature tests to validate route functionality\n\n";
        
        $markdown .= "## 📋 Route Categories to Fix\n\n";
        $markdown .= "Based on the missing routes, focus on these areas:\n\n";
        
        $categories = $this->categorizeRoutes($report['missing_routes']);
        foreach ($categories as $category => $routes) {
            $markdown .= "### $category\n";
            foreach (array_slice($routes, 0, 10) as $route) {
                $markdown .= "- `{$route['route']}`\n";
            }
            if (count($routes) > 10) {
                $markdown .= "- ... and " . (count($routes) - 10) . " more\n";
            }
            $markdown .= "\n";
        }
        
        file_put_contents('BLADE_ROUTE_ANALYSIS_SUMMARY.md', $markdown);
    }

    private function categorizeRoutes($missingRoutes)
    {
        $categories = [
            'Admin Routes' => [],
            'Candidate Routes' => [],
            'Employer Routes' => [],
            'Authentication Routes' => [],
            'API Routes' => [],
            'Frontend Routes' => [],
            'Other Routes' => []
        ];

        foreach ($missingRoutes as $missing) {
            $route = $missing['route'];
            
            if (Str::startsWith($route, 'admin.')) {
                $categories['Admin Routes'][] = $missing;
            } elseif (Str::startsWith($route, 'candidate.')) {
                $categories['Candidate Routes'][] = $missing;
            } elseif (Str::startsWith($route, 'employer.')) {
                $categories['Employer Routes'][] = $missing;
            } elseif (Str::contains($route, ['login', 'register', 'password', 'auth', 'verification'])) {
                $categories['Authentication Routes'][] = $missing;
            } elseif (Str::startsWith($route, 'api.')) {
                $categories['API Routes'][] = $missing;
            } elseif (Str::startsWith($route, ['front.', 'web.'])) {
                $categories['Frontend Routes'][] = $missing;
            } else {
                $categories['Other Routes'][] = $missing;
            }
        }

        return array_filter($categories, function($routes) {
            return !empty($routes);
        });
    }
}

// Run the analysis
echo "🚀 Starting Comprehensive Blade Route Analysis...\n\n";
new RouteAnalyzer();
echo "✅ Analysis complete!\n"; 