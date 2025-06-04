<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Route;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request instance
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

class BladeRouteAnalyzer
{
    private $bladeFiles = [];
    private $routes = [];
    private $routeErrors = [];
    private $routeTests = [];
    private $totalRoutes = 0;
    private $workingRoutes = 0;
    private $brokenRoutes = 0;

    public function __construct()
    {
        $this->findBladeFiles();
        $this->extractRoutes();
        $this->testRoutes();
        $this->generateReport();
    }

    private function findBladeFiles()
    {
        $directories = [
            'resources/views',
        ];

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir)
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getExtension() === 'php') {
                        $this->bladeFiles[] = $file->getPathname();
                    }
                }
            }
        }

        echo "Found " . count($this->bladeFiles) . " blade files\n";
    }

    private function extractRoutes()
    {
        $routePatterns = [
            // route() function calls
            '/route\s*\(\s*[\'"]([^\'\"]+)[\'"](?:\s*,\s*([^\)]+))?\s*\)/',
            // url() function calls with route names
            '/url\s*\(\s*route\s*\(\s*[\'"]([^\'\"]+)[\'"](?:\s*,\s*([^\)]+))?\s*\)\s*\)/',
            // @route directive
            '/@route\s*\(\s*[\'"]([^\'\"]+)[\'"](?:\s*,\s*([^\)]+))?\s*\)/',
            // action attribute in forms
            '/action\s*=\s*[\'"]{{?\s*route\s*\(\s*[\'"]([^\'\"]+)[\'"](?:\s*,\s*([^\)]+))?\s*\)\s*}}?[\'"]/',
            // href attributes with route()
            '/href\s*=\s*[\'"]{{?\s*route\s*\(\s*[\'"]([^\'\"]+)[\'"](?:\s*,\s*([^\)]+))?\s*\)\s*}}?[\'"]/',
        ];

        foreach ($this->bladeFiles as $file) {
            $content = file_get_contents($file);
            $relativePath = str_replace(getcwd() . '/', '', $file);

            foreach ($routePatterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $routeName = $match[1];
                        $parameters = isset($match[2]) ? trim($match[2]) : null;
                        
                        if (!isset($this->routes[$routeName])) {
                            $this->routes[$routeName] = [
                                'name' => $routeName,
                                'files' => [],
                                'parameters' => [],
                                'tested' => false,
                                'working' => false,
                                'error' => null
                            ];
                        }

                        $this->routes[$routeName]['files'][] = $relativePath;
                        if ($parameters) {
                            $this->routes[$routeName]['parameters'][] = $parameters;
                        }
                    }
                }
            }
        }

        $this->totalRoutes = count($this->routes);
        echo "Found " . $this->totalRoutes . " unique routes\n";
    }

    private function testRoutes()
    {
        echo "Testing routes...\n";
        
        // Get all registered routes
        $registeredRoutes = collect(Route::getRoutes())->mapWithKeys(function ($route) {
            return [$route->getName() => $route];
        })->filter();

        foreach ($this->routes as $routeName => &$routeData) {
            try {
                if ($registeredRoutes->has($routeName)) {
                    $route = $registeredRoutes->get($routeName);
                    
                    // Check if route requires parameters
                    $requiredParams = $this->getRequiredParameters($route);
                    
                    if (empty($requiredParams)) {
                        // Test route without parameters
                        $url = route($routeName);
                        $routeData['working'] = true;
                        $routeData['url'] = $url;
                        $this->workingRoutes++;
                    } else {
                        // Test with dummy parameters
                        $dummyParams = $this->generateDummyParameters($requiredParams);
                        try {
                            $url = route($routeName, $dummyParams);
                            $routeData['working'] = true;
                            $routeData['url'] = $url;
                            $routeData['requires_params'] = true;
                            $this->workingRoutes++;
                        } catch (Exception $e) {
                            $routeData['working'] = false;
                            $routeData['error'] = "Parameter error: " . $e->getMessage();
                            $this->brokenRoutes++;
                        }
                    }
                } else {
                    $routeData['working'] = false;
                    $routeData['error'] = "Route not found in routes file";
                    $this->brokenRoutes++;
                }
                
                $routeData['tested'] = true;
                
            } catch (Exception $e) {
                $routeData['working'] = false;
                $routeData['error'] = $e->getMessage();
                $routeData['tested'] = true;
                $this->brokenRoutes++;
            }
        }

        echo "Testing complete: {$this->workingRoutes} working, {$this->brokenRoutes} broken\n";
    }

    private function getRequiredParameters($route)
    {
        $parameters = [];
        $uri = $route->uri();
        
        if (preg_match_all('/\{([^}?]+)\??\}/', $uri, $matches)) {
            foreach ($matches[1] as $param) {
                if (!str_contains($param, '?')) {
                    $parameters[] = $param;
                }
            }
        }
        
        return $parameters;
    }

    private function generateDummyParameters($parameters)
    {
        $dummyParams = [];
        foreach ($parameters as $param) {
            switch ($param) {
                case 'id':
                case 'user':
                case 'job':
                case 'company':
                case 'candidate':
                    $dummyParams[$param] = 1;
                    break;
                case 'slug':
                    $dummyParams[$param] = 'test-slug';
                    break;
                default:
                    $dummyParams[$param] = 'test';
            }
        }
        return $dummyParams;
    }

    private function generateReport()
    {
        $report = [
            'summary' => [
                'total_blade_files' => count($this->bladeFiles),
                'total_routes_found' => $this->totalRoutes,
                'working_routes' => $this->workingRoutes,
                'broken_routes' => $this->brokenRoutes,
                'success_rate' => $this->totalRoutes > 0 ? round(($this->workingRoutes / $this->totalRoutes) * 100, 2) : 0
            ],
            'working_routes' => [],
            'broken_routes' => [],
            'route_details' => $this->routes
        ];

        foreach ($this->routes as $routeName => $routeData) {
            if ($routeData['working']) {
                $report['working_routes'][] = [
                    'name' => $routeName,
                    'url' => $routeData['url'] ?? null,
                    'files' => array_unique($routeData['files']),
                    'requires_params' => $routeData['requires_params'] ?? false
                ];
            } else {
                $report['broken_routes'][] = [
                    'name' => $routeName,
                    'error' => $routeData['error'],
                    'files' => array_unique($routeData['files'])
                ];
            }
        }

        // Save detailed report
        file_put_contents('blade_route_analysis_comprehensive.json', json_encode($report, JSON_PRETTY_PRINT));

        // Generate summary report
        $this->generateSummaryReport($report);
    }

    private function generateSummaryReport($report)
    {
        $summary = "# Blade Route Analysis - Comprehensive Report\n\n";
        $summary .= "## Summary\n";
        $summary .= "- **Total Blade Files**: " . $report['summary']['total_blade_files'] . "\n";
        $summary .= "- **Total Routes Found**: " . $report['summary']['total_routes_found'] . "\n";
        $summary .= "- **Working Routes**: " . $report['summary']['working_routes'] . "\n";
        $summary .= "- **Broken Routes**: " . $report['summary']['broken_routes'] . "\n";
        $summary .= "- **Success Rate**: " . $report['summary']['success_rate'] . "%\n\n";

        if (!empty($report['broken_routes'])) {
            $summary .= "## Broken Routes\n\n";
            foreach ($report['broken_routes'] as $route) {
                $summary .= "### `{$route['name']}`\n";
                $summary .= "- **Error**: {$route['error']}\n";
                $summary .= "- **Used in files**:\n";
                foreach ($route['files'] as $file) {
                    $summary .= "  - {$file}\n";
                }
                $summary .= "\n";
            }
        }

        if (!empty($report['working_routes'])) {
            $summary .= "## Working Routes (Sample)\n\n";
            $sampleRoutes = array_slice($report['working_routes'], 0, 10);
            foreach ($sampleRoutes as $route) {
                $summary .= "- `{$route['name']}` → {$route['url']}\n";
            }
            if (count($report['working_routes']) > 10) {
                $summary .= "- ... and " . (count($report['working_routes']) - 10) . " more\n";
            }
        }

        file_put_contents('BLADE_ROUTE_ANALYSIS_SUMMARY.md', $summary);

        echo "\n" . $summary;
        echo "\nDetailed report saved to: blade_route_analysis_comprehensive.json\n";
        echo "Summary report saved to: BLADE_ROUTE_ANALYSIS_SUMMARY.md\n";
    }
}

// Run the analysis
echo "Starting comprehensive blade route analysis...\n";
new BladeRouteAnalyzer();
echo "Analysis complete!\n"; 