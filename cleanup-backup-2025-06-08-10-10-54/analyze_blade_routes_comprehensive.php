<?php

require_once __DIR__ . '/vendor/autoload.php';

class BladeRouteAnalyzer
{
    private $routes = [];
    private $errors = [];
    private $syntaxErrors = [];
    private $missingRoutes = [];
    private $workingRoutes = [];
    private $bladeFiles = [];

    public function __construct()
    {
        // Initialize Laravel app to access routes
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }

    public function analyzeAllBladeFiles()
    {
        echo "🔍 Starting comprehensive blade file analysis...\n\n";
        
        $this->findAllBladeFiles();
        $this->extractRoutesFromBlades();
        $this->checkRoutesExistence();
        $this->findSyntaxErrors();
        $this->generateReport();
    }

    private function findAllBladeFiles()
    {
        $viewsPath = __DIR__ . '/resources/views';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace($viewsPath . '/', '', $file->getPathname());
                if (str_contains($relativePath, '.blade.')) {
                    $this->bladeFiles[] = $file->getPathname();
                }
            }
        }

        echo "📁 Found " . count($this->bladeFiles) . " blade files\n";
    }

    private function extractRoutesFromBlades()
    {
        echo "🔍 Extracting routes from blade files...\n";
        
        $routePatterns = [
            '/route\([\'"]([^\'"]+)[\'"](?:,\s*\[([^\]]*)\])?\)/',
            '/url\([\'"]([^\'"]+)[\'"]\)/',
            '/action\([\'"]([^\'"@]+)@([^\'"]+)[\'"](?:,\s*\[([^\]]*)\])?\)/',
            '/@([a-zA-Z]+)\([\'"]([^\'"]+)[\'"]\)/', // Blade directives
            '/href=[\'"]([^\'"\s]+)[\'"]/',
            '/action=[\'"]([^\'"\s]+)[\'"]/',
        ];

        foreach ($this->bladeFiles as $file) {
            $content = file_get_contents($file);
            $relativePath = str_replace(__DIR__ . '/resources/views/', '', $file);
            
            foreach ($routePatterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches)) {
                    foreach ($matches[1] as $index => $match) {
                        $routeInfo = [
                            'route' => $match,
                            'file' => $relativePath,
                            'line' => $this->getLineNumber($content, $matches[0][$index]),
                            'type' => $this->determineRouteType($pattern, $matches[0][$index]),
                            'full_match' => $matches[0][$index]
                        ];
                        
                        if (isset($matches[2][$index])) {
                            $routeInfo['parameters'] = $matches[2][$index];
                        }
                        
                        $this->routes[] = $routeInfo;
                    }
                }
            }
        }

        echo "📋 Found " . count($this->routes) . " route references\n";
    }

    private function getLineNumber($content, $search)
    {
        $lines = explode("\n", substr($content, 0, strpos($content, $search)));
        return count($lines);
    }

    private function determineRouteType($pattern, $match)
    {
        if (str_contains($match, 'route(')) return 'named_route';
        if (str_contains($match, 'url(')) return 'url';
        if (str_contains($match, 'action(')) return 'action';
        if (str_contains($match, 'href=')) return 'href';
        if (str_contains($match, 'action=')) return 'form_action';
        if (str_contains($match, '@')) return 'blade_directive';
        return 'unknown';
    }

    private function checkRoutesExistence()
    {
        echo "🔗 Checking route existence...\n";
        
        $laravelRoutes = collect(\Route::getRoutes())->map(function ($route) {
            return [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'action' => $route->getActionName()
            ];
        });

        foreach ($this->routes as $route) {
            $routeName = $route['route'];
            
            // Check if named route exists
            if ($route['type'] === 'named_route') {
                $exists = $laravelRoutes->where('name', $routeName)->isNotEmpty();
                if ($exists) {
                    $this->workingRoutes[] = $route;
                } else {
                    $this->missingRoutes[] = $route;
                }
            }
            // Check if URL path exists
            elseif ($route['type'] === 'url' || $route['type'] === 'href') {
                $path = ltrim($routeName, '/');
                $exists = $laravelRoutes->where('uri', $path)->isNotEmpty() ||
                         $laravelRoutes->where('uri', 'like', str_replace('{id}', '*', $path))->isNotEmpty();
                if ($exists) {
                    $this->workingRoutes[] = $route;
                } else {
                    $this->missingRoutes[] = $route;
                }
            }
            // Check if controller action exists
            elseif ($route['type'] === 'action') {
                $actionParts = explode('@', $routeName);
                if (count($actionParts) === 2) {
                    $controller = $actionParts[0];
                    $method = $actionParts[1];
                    
                    $exists = $laravelRoutes->where('action', 'like', "%{$controller}@{$method}")->isNotEmpty();
                    if ($exists) {
                        $this->workingRoutes[] = $route;
                    } else {
                        $this->missingRoutes[] = $route;
                    }
                }
            }
        }

        echo "✅ Working routes: " . count($this->workingRoutes) . "\n";
        echo "❌ Missing routes: " . count($this->missingRoutes) . "\n";
    }

    private function findSyntaxErrors()
    {
        echo "🔍 Checking for syntax errors...\n";
        
        foreach ($this->bladeFiles as $file) {
            $content = file_get_contents($file);
            $relativePath = str_replace(__DIR__ . '/resources/views/', '', $file);
            
            // Common blade syntax error patterns
            $errorPatterns = [
                '/\{\{\s*\$[^}]*\$[^}]*\}\}/' => 'Double variable in blade output',
                '/\@[a-zA-Z]+\([^)]*[^)]\s*$/' => 'Unclosed blade directive',
                '/\{\{[^}]*\{[^}]*\}\}/' => 'Nested blade output',
                '/\@section\([^)]+\).*?\@section\([^)]+\)/s' => 'Nested sections',
                '/\{\{\s*[^}]*\?\?\s*[^}]*\}\}/' => 'Potential null coalescing in blade',
                '/route\([\'"][^\'\"]*[\'\"]\s*[^)]/' => 'Malformed route() call',
                '/\@[a-zA-Z]+\([^\'\"]*[\'\"[^\'\"]*\)/' => 'Unmatched quotes in directive',
            ];

            foreach ($errorPatterns as $pattern => $description) {
                if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    foreach ($matches[0] as $match) {
                        $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                        $this->syntaxErrors[] = [
                            'file' => $relativePath,
                            'line' => $line,
                            'error' => $description,
                            'code' => trim($match[0])
                        ];
                    }
                }
            }
        }

        echo "⚠️  Found " . count($this->syntaxErrors) . " potential syntax errors\n";
    }

    private function generateReport()
    {
        $report = [
            'summary' => [
                'total_blade_files' => count($this->bladeFiles),
                'total_routes_found' => count($this->routes),
                'working_routes' => count($this->workingRoutes),
                'missing_routes' => count($this->missingRoutes),
                'syntax_errors' => count($this->syntaxErrors)
            ],
            'missing_routes' => $this->missingRoutes,
            'syntax_errors' => $this->syntaxErrors,
            'working_routes' => $this->workingRoutes
        ];

        file_put_contents('blade_analysis_report.json', json_encode($report, JSON_PRETTY_PRINT));
        
        $this->printConsoleReport();
    }

    private function printConsoleReport()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "📊 BLADE ANALYSIS REPORT\n";
        echo str_repeat("=", 80) . "\n\n";

        echo "📁 Total Blade Files: " . count($this->bladeFiles) . "\n";
        echo "🔗 Total Routes Found: " . count($this->routes) . "\n";
        echo "✅ Working Routes: " . count($this->workingRoutes) . "\n";
        echo "❌ Missing Routes: " . count($this->missingRoutes) . "\n";
        echo "⚠️  Syntax Errors: " . count($this->syntaxErrors) . "\n\n";

        if (!empty($this->missingRoutes)) {
            echo "❌ MISSING ROUTES:\n";
            echo str_repeat("-", 40) . "\n";
            foreach (array_slice($this->missingRoutes, 0, 10) as $route) {
                echo "• {$route['route']} in {$route['file']}:{$route['line']}\n";
            }
            if (count($this->missingRoutes) > 10) {
                echo "... and " . (count($this->missingRoutes) - 10) . " more\n";
            }
            echo "\n";
        }

        if (!empty($this->syntaxErrors)) {
            echo "⚠️  SYNTAX ERRORS:\n";
            echo str_repeat("-", 40) . "\n";
            foreach (array_slice($this->syntaxErrors, 0, 10) as $error) {
                echo "• {$error['file']}:{$error['line']} - {$error['error']}\n";
                echo "  Code: {$error['code']}\n";
            }
            if (count($this->syntaxErrors) > 10) {
                echo "... and " . (count($this->syntaxErrors) - 10) . " more\n";
            }
            echo "\n";
        }

        echo "📄 Full report saved to: blade_analysis_report.json\n";
        echo str_repeat("=", 80) . "\n";
    }
}

// Run the analysis
try {
    $analyzer = new BladeRouteAnalyzer();
    $analyzer->analyzeAllBladeFiles();
    echo "\n✅ Analysis completed successfully!\n";
} catch (Exception $e) {
    echo "\n❌ Error during analysis: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 