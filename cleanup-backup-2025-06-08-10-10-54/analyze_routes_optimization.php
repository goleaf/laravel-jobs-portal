<?php

/**
 * COMPREHENSIVE ROUTE OPTIMIZATION ANALYSIS
 * Following Laravel routing best practices from Universal documentation
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

// Initialize Laravel application for route analysis
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class RouteOptimizer
{
    private $routes = [];
    private $duplicateRoutes = [];
    private $unusedRoutes = [];
    private $conflictingRoutes = [];
    private $optimizationSuggestions = [];

    public function analyze()
    {
        $this->collectRoutes();
        $this->findDuplicates();
        $this->findConflicts();
        $this->analyzeGroupingOpportunities();
        $this->checkMiddlewareOptimization();
        $this->findUnusedRoutes();
        $this->generateReport();
    }

    private function collectRoutes()
    {
        $routeCollection = Route::getRoutes();
        
        foreach ($routeCollection as $route) {
            $this->routes[] = [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'action' => $route->getActionName(),
                'middleware' => $route->middleware(),
                'prefix' => $route->getPrefix(),
                'domain' => $route->getDomain(),
                'compiled' => $route->getCompiled(),
            ];
        }
        
        echo "✅ Collected " . count($this->routes) . " routes for analysis\n";
    }

    private function findDuplicates()
    {
        $uriMap = [];
        $nameMap = [];
        
        foreach ($this->routes as $index => $route) {
            // Check for duplicate URIs with same methods
            $key = implode('|', $route['methods']) . ':' . $route['uri'];
            if (isset($uriMap[$key])) {
                $this->duplicateRoutes[] = [
                    'type' => 'duplicate_uri_methods',
                    'routes' => [$uriMap[$key], $index],
                    'issue' => "Duplicate URI with same methods: {$route['uri']}"
                ];
            } else {
                $uriMap[$key] = $index;
            }
            
            // Check for duplicate route names
            if ($route['name'] && isset($nameMap[$route['name']])) {
                $this->duplicateRoutes[] = [
                    'type' => 'duplicate_name',
                    'routes' => [$nameMap[$route['name']], $index],
                    'issue' => "Duplicate route name: {$route['name']}"
                ];
            } elseif ($route['name']) {
                $nameMap[$route['name']] = $index;
            }
        }
        
        echo "🔍 Found " . count($this->duplicateRoutes) . " duplicate route issues\n";
    }

    private function findConflicts()
    {
        foreach ($this->routes as $index => $route) {
            foreach ($this->routes as $otherIndex => $otherRoute) {
                if ($index >= $otherIndex) continue;
                
                // Check for conflicting routes (same prefix but different patterns)
                if ($this->routesConflict($route, $otherRoute)) {
                    $this->conflictingRoutes[] = [
                        'routes' => [$index, $otherIndex],
                        'issue' => "Potential route conflict between {$route['uri']} and {$otherRoute['uri']}"
                    ];
                }
            }
        }
        
        echo "⚠️  Found " . count($this->conflictingRoutes) . " potential route conflicts\n";
    }

    private function routesConflict($route1, $route2)
    {
        // Check if routes have overlapping methods
        $commonMethods = array_intersect($route1['methods'], $route2['methods']);
        if (empty($commonMethods)) return false;
        
        // Check if URIs could conflict
        $uri1 = str_replace(['{', '}'], ['', ''], $route1['uri']);
        $uri2 = str_replace(['{', '}'], ['', ''], $route2['uri']);
        
        // Simple conflict detection - could be improved
        return Str::startsWith($uri1, $uri2) || Str::startsWith($uri2, $uri1);
    }

    private function analyzeGroupingOpportunities()
    {
        $prefixGroups = [];
        $middlewareGroups = [];
        
        foreach ($this->routes as $route) {
            // Group by prefix
            $prefix = $route['prefix'] ?? 'root';
            $prefixGroups[$prefix][] = $route;
            
            // Group by middleware
            $middlewareKey = implode(',', $route['middleware']);
            $middlewareGroups[$middlewareKey][] = $route;
        }
        
        // Find optimization opportunities
        foreach ($prefixGroups as $prefix => $routes) {
            if (count($routes) >= 3 && $prefix !== 'root') {
                $routeNames = array_column($routes, 'name');
                $this->optimizationSuggestions[] = [
                    'type' => 'group_by_prefix',
                    'message' => "Consider grouping " . count($routes) . " routes under prefix '{$prefix}'",
                    'routes' => $routeNames,
                    'benefit' => 'Reduces code duplication and improves maintainability'
                ];
            }
        }
        
        foreach ($middlewareGroups as $middleware => $routes) {
            if (count($routes) >= 5 && !empty($middleware)) {
                $routeNames = array_column($routes, 'name');
                $this->optimizationSuggestions[] = [
                    'type' => 'group_by_middleware',
                    'message' => "Consider grouping " . count($routes) . " routes with middleware '{$middleware}'",
                    'routes' => $routeNames,
                    'benefit' => 'Simplifies middleware management and improves performance'
                ];
            }
        }
        
        echo "💡 Generated " . count($this->optimizationSuggestions) . " optimization suggestions\n";
    }

    private function checkMiddlewareOptimization()
    {
        $middlewareUsage = [];
        
        foreach ($this->routes as $route) {
            foreach ($route['middleware'] as $middleware) {
                $middlewareUsage[$middleware] = ($middlewareUsage[$middleware] ?? 0) + 1;
            }
        }
        
        // Find rarely used middleware
        foreach ($middlewareUsage as $middleware => $count) {
            if ($count === 1) {
                $this->optimizationSuggestions[] = [
                    'type' => 'single_use_middleware',
                    'message' => "Middleware '{$middleware}' is only used once - consider inline logic",
                    'benefit' => 'Reduces middleware overhead for single-use cases'
                ];
            }
        }
        
        // Find heavily used middleware that should be global
        foreach ($middlewareUsage as $middleware => $count) {
            if ($count > 20) {
                $this->optimizationSuggestions[] = [
                    'type' => 'global_middleware',
                    'message' => "Middleware '{$middleware}' is used {$count} times - consider making it global",
                    'benefit' => 'Improves performance by reducing repeated middleware application'
                ];
            }
        }
    }

    private function findUnusedRoutes()
    {
        // This is a simplified check - in a real application, you'd analyze controller usage
        $suspiciousRoutes = [];
        
        foreach ($this->routes as $route) {
            // Check for routes with generic names or actions
            if (
                Str::contains($route['action'], 'Closure') ||
                Str::contains($route['uri'], '/test') ||
                Str::contains($route['uri'], '/debug') ||
                !$route['name']
            ) {
                $suspiciousRoutes[] = $route;
            }
        }
        
        $this->unusedRoutes = $suspiciousRoutes;
        echo "🧹 Found " . count($this->unusedRoutes) . " potentially unused routes\n";
    }

    private function generateReport()
    {
        $report = [
            'analysis_date' => date('Y-m-d H:i:s'),
            'total_routes' => count($this->routes),
            'duplicate_routes' => $this->duplicateRoutes,
            'conflicting_routes' => $this->conflictingRoutes,
            'optimization_suggestions' => $this->optimizationSuggestions,
            'potentially_unused_routes' => $this->unusedRoutes,
            'recommendations' => $this->generateRecommendations()
        ];
        
        file_put_contents('route_optimization_report.json', json_encode($report, JSON_PRETTY_PRINT));
        $this->printSummary($report);
    }

    private function generateRecommendations()
    {
        return [
            'high_priority' => [
                'Remove duplicate routes to prevent conflicts',
                'Group related routes using Route::group() for better organization',
                'Implement route caching for production: php artisan route:cache',
                'Use route model binding for cleaner parameter handling'
            ],
            'medium_priority' => [
                'Optimize middleware usage by grouping routes with common middleware',
                'Consider using resource controllers for CRUD operations',
                'Implement rate limiting for API endpoints',
                'Use named routes consistently for better maintainability'
            ],
            'low_priority' => [
                'Remove unused test/debug routes from production',
                'Implement subdomain routing if applicable',
                'Consider using route groups with namespaces for controllers',
                'Add route documentation for complex routing logic'
            ],
            'universal_best_practices' => [
                'Use Route::pattern() for global parameter constraints',
                'Implement fallback routes for better UX',
                'Use Route::domain() for multi-tenant applications',
                'Apply rate limiting with Redis for better performance',
                'Use route groups with prefixes for API versioning'
            ]
        ];
    }

    private function printSummary($report)
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "🎯 ROUTE OPTIMIZATION ANALYSIS SUMMARY\n";
        echo str_repeat("=", 80) . "\n";
        
        echo "📊 STATISTICS:\n";
        echo "   Total Routes: {$report['total_routes']}\n";
        echo "   Duplicate Issues: " . count($report['duplicate_routes']) . "\n";
        echo "   Conflicts: " . count($report['conflicting_routes']) . "\n";
        echo "   Optimization Opportunities: " . count($report['optimization_suggestions']) . "\n";
        echo "   Potentially Unused: " . count($report['potentially_unused_routes']) . "\n\n";
        
        if (!empty($report['duplicate_routes'])) {
            echo "🚨 CRITICAL ISSUES (Duplicates):\n";
            foreach (array_slice($report['duplicate_routes'], 0, 5) as $duplicate) {
                echo "   - {$duplicate['issue']}\n";
            }
            echo "\n";
        }
        
        if (!empty($report['optimization_suggestions'])) {
            echo "💡 TOP OPTIMIZATION SUGGESTIONS:\n";
            foreach (array_slice($report['optimization_suggestions'], 0, 5) as $suggestion) {
                echo "   - {$suggestion['message']}\n";
                echo "     Benefit: {$suggestion['benefit']}\n";
            }
            echo "\n";
        }
        
        echo "🔧 NEXT STEPS:\n";
        foreach ($report['recommendations']['high_priority'] as $rec) {
            echo "   1. {$rec}\n";
        }
        
        echo "\n📁 Full report saved to: route_optimization_report.json\n";
        echo str_repeat("=", 80) . "\n";
    }
}

// Run the analysis
echo "🚀 Starting Route Optimization Analysis...\n\n";
$optimizer = new RouteOptimizer();
$optimizer->analyze(); 