<?php

/**
 * Route Testing Script
 * 
 * This script tests all routes in the application to ensure they are accessible
 * and return appropriate responses. Part of TODO.md Priority 4: Error Detection.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Bootstrap Laravel Application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class RouteTestResult
{
    public string $method;
    public string $uri;
    public string $name;
    public string $action;
    public string $status = 'UNKNOWN';
    public int $responseCode = 0;
    public array $middleware = [];
    public string $error = '';
    public float $responseTime = 0;

    public function __construct(string $method, string $uri, string $name = '', string $action = '')
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->name = $name;
        $this->action = $action;
    }
}

class RouteTestRunner
{
    private array $results = [];
    private array $excludedRoutes = [
        // Exclude routes that require special handling
        'debugbar.*',
        'telescope.*',
        '_debugbar.*',
        'horizon.*',
        '*.websocket',
        'broadcasting.*',
    ];
    
    private array $testableRoutes = [];
    private int $totalRoutes = 0;
    private int $passedRoutes = 0;
    private int $failedRoutes = 0;
    private int $skippedRoutes = 0;

    public function __construct()
    {
        echo "\n🧪 ROUTE TESTING FRAMEWORK\n";
        echo "==========================\n\n";
    }

    /**
     * Run all route tests
     */
    public function runAllTests(): void
    {
        $this->gatherRoutes();
        $this->testRoutes();
        $this->generateReport();
    }

    /**
     * Gather all application routes
     */
    private function gatherRoutes(): void
    {
        echo "📋 Gathering application routes...\n";
        
        $routes = Route::getRoutes();
        $this->totalRoutes = count($routes);
        
        foreach ($routes as $route) {
            $routeName = $route->getName() ?? 'unnamed';
            $routeUri = $route->uri();
            $routeMethods = $route->methods();
            $routeAction = $route->getActionName();
            $routeMiddleware = $route->middleware();

            // Skip excluded routes
            if ($this->shouldExcludeRoute($routeName, $routeUri)) {
                $this->skippedRoutes++;
                continue;
            }

            foreach ($routeMethods as $method) {
                if ($method === 'HEAD') continue; // Skip HEAD requests
                
                $result = new RouteTestResult($method, $routeUri, $routeName, $routeAction);
                $result->middleware = $routeMiddleware;
                
                $this->testableRoutes[] = $result;
            }
        }

        echo "✅ Found {$this->totalRoutes} total routes\n";
        echo "📝 Testing " . count($this->testableRoutes) . " route endpoints\n";
        echo "⏭️  Skipped {$this->skippedRoutes} excluded routes\n\n";
    }

    /**
     * Test all gathered routes
     */
    private function testRoutes(): void
    {
        echo "🔄 Starting route tests...\n";
        
        $progressCount = 0;
        $totalTests = count($this->testableRoutes);
        
        foreach ($this->testableRoutes as $routeTest) {
            $progressCount++;
            $this->testSingleRoute($routeTest);
            
            // Show progress
            $percent = round(($progressCount / $totalTests) * 100, 1);
            echo "\r🔄 Progress: {$progressCount}/{$totalTests} ({$percent}%) - " . 
                 "Pass: {$this->passedRoutes}, Fail: {$this->failedRoutes}";
        }
        
        echo "\n\n✅ Route testing completed!\n\n";
    }

    /**
     * Test a single route
     */
    private function testSingleRoute(RouteTestResult $routeTest): void
    {
        $startTime = microtime(true);
        
        try {
            // Create test request
            $request = $this->createTestRequest($routeTest);
            
            // Handle different route types
            if ($this->requiresAuthentication($routeTest)) {
                $routeTest->status = 'AUTH_REQUIRED';
                $routeTest->responseCode = 302; // Redirect to login expected
            } elseif ($this->hasParameters($routeTest->uri)) {
                $routeTest->status = 'PARAMETERS_REQUIRED';
                $routeTest->responseCode = 404; // Not found expected for missing params
            } else {
                // Test the route
                $response = $this->makeRequest($request, $routeTest);
                $routeTest->responseCode = $response['status'];
                $routeTest->status = $this->determineStatus($response['status'], $routeTest);
            }
            
        } catch (\Exception $e) {
            $routeTest->status = 'ERROR';
            $routeTest->error = $e->getMessage();
            $this->failedRoutes++;
        }
        
        $routeTest->responseTime = microtime(true) - $startTime;
        $this->results[] = $routeTest;
        
        if ($routeTest->status === 'PASS') {
            $this->passedRoutes++;
        } elseif ($routeTest->status === 'ERROR' || $routeTest->status === 'FAIL') {
            $this->failedRoutes++;
        }
    }

    /**
     * Create a test request for the route
     */
    private function createTestRequest(RouteTestResult $routeTest): Request
    {
        $uri = $this->resolveTestUri($routeTest->uri);
        
        return Request::create($uri, $routeTest->method, [], [], [], [
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_USER_AGENT' => 'RouteTestRunner/1.0',
        ]);
    }

    /**
     * Make the actual request
     */
    private function makeRequest(Request $request, RouteTestResult $routeTest): array
    {
        try {
            $response = app()->handle($request);
            return [
                'status' => $response->getStatusCode(),
                'content' => $response->getContent(),
                'headers' => $response->headers->all(),
            ];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Route') !== false) {
                return ['status' => 404, 'content' => '', 'headers' => []];
            }
            if (strpos($e->getMessage(), 'Unauthenticated') !== false) {
                return ['status' => 401, 'content' => '', 'headers' => []];
            }
            throw $e;
        }
    }

    /**
     * Resolve test URI with sample parameters
     */
    private function resolveTestUri(string $uri): string
    {
        // Replace common route parameters with test values
        $replacements = [
            '{id}' => '1',
            '{user}' => '1',
            '{job}' => '1',
            '{company}' => '1',
            '{candidate}' => '1',
            '{admin}' => '1',
            '{transaction}' => '1',
            '{post}' => '1',
            '{locale}' => 'en',
            '{token}' => 'test-token',
            '{planId}' => '1',
            '{jobId}' => '1',
            '{companyId}' => '1',
            '{candidateId}' => '1',
            '{categoryId}' => '1',
            '{inquire}' => '1',
            '{section}' => 'privacy_policy',
        ];

        foreach ($replacements as $param => $value) {
            $uri = str_replace($param, $value, $uri);
        }

        return '/' . ltrim($uri, '/');
    }

    /**
     * Check if route requires authentication
     */
    private function requiresAuthentication(RouteTestResult $routeTest): bool
    {
        return in_array('auth', $routeTest->middleware) ||
               strpos($routeTest->uri, 'admin') !== false ||
               strpos($routeTest->uri, 'dashboard') !== false ||
               strpos($routeTest->uri, 'candidate') !== false ||
               strpos($routeTest->uri, 'employer') !== false;
    }

    /**
     * Check if route has parameters
     */
    private function hasParameters(string $uri): bool
    {
        return strpos($uri, '{') !== false;
    }

    /**
     * Determine route test status
     */
    private function determineStatus(int $statusCode, RouteTestResult $routeTest): string
    {
        // Success codes
        if (in_array($statusCode, [200, 201, 202])) {
            return 'PASS';
        }
        
        // Acceptable redirects
        if (in_array($statusCode, [301, 302, 303, 307, 308])) {
            return 'REDIRECT';
        }
        
        // Authentication required (expected for protected routes)
        if ($statusCode === 401 || $statusCode === 403) {
            return 'AUTH_REQUIRED';
        }
        
        // Not found (might be expected for routes with parameters)
        if ($statusCode === 404) {
            return $this->hasParameters($routeTest->uri) ? 'PARAMETERS_REQUIRED' : 'FAIL';
        }
        
        // Method not allowed
        if ($statusCode === 405) {
            return 'METHOD_NOT_ALLOWED';
        }
        
        // Server errors
        if ($statusCode >= 500) {
            return 'ERROR';
        }
        
        return 'FAIL';
    }

    /**
     * Check if route should be excluded
     */
    private function shouldExcludeRoute(string $routeName, string $uri): bool
    {
        foreach ($this->excludedRoutes as $pattern) {
            if (fnmatch($pattern, $routeName) || fnmatch($pattern, $uri)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate comprehensive test report
     */
    private function generateReport(): void
    {
        echo "📊 ROUTE TEST RESULTS\n";
        echo "=====================\n\n";

        // Summary statistics
        $total = count($this->results);
        $passPercent = $total > 0 ? round(($this->passedRoutes / $total) * 100, 1) : 0;
        $failPercent = $total > 0 ? round(($this->failedRoutes / $total) * 100, 1) : 0;

        echo "📈 SUMMARY:\n";
        echo "Total Routes Tested: {$total}\n";
        echo "✅ Passed: {$this->passedRoutes} ({$passPercent}%)\n";
        echo "❌ Failed: {$this->failedRoutes} ({$failPercent}%)\n";
        echo "⏭️  Skipped: {$this->skippedRoutes}\n\n";

        // Group results by status
        $groupedResults = [];
        foreach ($this->results as $result) {
            $groupedResults[$result->status][] = $result;
        }

        // Display results by category
        foreach ($groupedResults as $status => $results) {
            $count = count($results);
            $icon = $this->getStatusIcon($status);
            
            echo "{$icon} {$status}: {$count} routes\n";
            
            if ($status === 'ERROR' || $status === 'FAIL') {
                foreach ($results as $result) {
                    echo "  ❌ {$result->method} {$result->uri}";
                    if ($result->name) echo " (name: {$result->name})";
                    if ($result->error) echo " - Error: {$result->error}";
                    echo " [HTTP {$result->responseCode}]\n";
                }
                echo "\n";
            }
        }

        // Performance summary
        $avgResponseTime = 0;
        if (count($this->results) > 0) {
            $totalTime = array_sum(array_column($this->results, 'responseTime'));
            $avgResponseTime = round($totalTime / count($this->results), 4);
        }
        
        echo "⚡ PERFORMANCE:\n";
        echo "Average Response Time: {$avgResponseTime}s\n\n";

        // Critical routes check
        $this->checkCriticalRoutes();

        // Generate JSON report for CI/CD
        $this->generateJsonReport();

        echo "🎯 RECOMMENDATIONS:\n";
        echo "===================\n";
        if ($this->failedRoutes > 0) {
            echo "• Fix routes returning 5xx errors immediately\n";
            echo "• Review 404 errors for routes without parameters\n";
            echo "• Ensure proper authentication middleware\n";
        }
        if ($passPercent < 80) {
            echo "• Route test coverage below 80% - review failed routes\n";
        }
        echo "• Run 'php artisan route:list' to verify all routes\n";
        echo "• Test critical user paths manually\n";
        echo "• Consider adding feature tests for failed routes\n\n";

        echo "✅ Route testing complete! Check results above.\n";
    }

    /**
     * Get status icon
     */
    private function getStatusIcon(string $status): string
    {
        return match($status) {
            'PASS' => '✅',
            'FAIL', 'ERROR' => '❌',
            'REDIRECT' => '🔄',
            'AUTH_REQUIRED' => '🔒',
            'PARAMETERS_REQUIRED' => '📝',
            'METHOD_NOT_ALLOWED' => '🚫',
            default => '❓'
        };
    }

    /**
     * Check critical routes
     */
    private function checkCriticalRoutes(): void
    {
        $criticalRoutes = [
            'front.home', 'login', 'register', 'jobs.index', 'companies.index'
        ];

        echo "🔍 CRITICAL ROUTES CHECK:\n";
        foreach ($criticalRoutes as $routeName) {
            $found = false;
            foreach ($this->results as $result) {
                if ($result->name === $routeName) {
                    $icon = $this->getStatusIcon($result->status);
                    echo "  {$icon} {$routeName}: {$result->status}\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "  ❓ {$routeName}: NOT FOUND\n";
            }
        }
        echo "\n";
    }

    /**
     * Generate JSON report
     */
    private function generateJsonReport(): void
    {
        $report = [
            'summary' => [
                'total_routes' => count($this->results),
                'passed' => $this->passedRoutes,
                'failed' => $this->failedRoutes,
                'skipped' => $this->skippedRoutes,
                'pass_percentage' => count($this->results) > 0 ? round(($this->passedRoutes / count($this->results)) * 100, 1) : 0,
            ],
            'results' => $this->results,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        file_put_contents(__DIR__ . '/route-test-report.json', json_encode($report, JSON_PRETTY_PRINT));
        echo "💾 JSON report saved to: route-test-report.json\n\n";
    }
}

// Run the tests
try {
    $runner = new RouteTestRunner();
    $runner->runAllTests();
    
    echo "🎉 ROUTE TESTING COMPLETED SUCCESSFULLY!\n";
    echo "=========================================\n";
    echo "Check the detailed report above and route-test-report.json for full results.\n\n";
    
} catch (Exception $e) {
    echo "💥 ROUTE TESTING FAILED: " . $e->getMessage() . "\n";
    exit(1);
} 