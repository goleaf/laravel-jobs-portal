<?php

/**
 * Comprehensive Route Testing and Test Runner
 * Following Laravel Best Practices from Universal
 * 
 * This script:
 * 1. Tests all routes for accessibility
 * 2. Runs comprehensive test suites
 * 3. Generates detailed reports
 * 4. Follows Single Responsibility Principle
 * 5. Uses Laravel's built-in testing capabilities
 */

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

class ComprehensiveTestRunner
{
    private array $results = [];
    private int $totalRoutes = 0;
    private int $passedRoutes = 0;
    private int $failedRoutes = 0;
    private array $testResults = [];
    
    public function __construct()
    {
        $this->bootApplication();
    }
    
    /**
     * Boot Laravel application for testing
     */
    private function bootApplication(): void
    {
        $app = require_once __DIR__.'/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }
    
    /**
     * Run comprehensive route testing
     */
    public function testAllRoutes(): array
    {
        $this->output("🚀 Starting Comprehensive Route Testing...\n");
        
        $routes = $this->getAllRoutes();
        $this->totalRoutes = count($routes);
        
        foreach ($routes as $route) {
            $this->testSingleRoute($route);
        }
        
        return $this->generateRouteReport();
    }
    
    /**
     * Get all registered routes
     */
    private function getAllRoutes(): array
    {
        $routes = [];
        
        foreach (Route::getRoutes() as $route) {
            $routes[] = [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $route->middleware(),
            ];
        }
        
        return $routes;
    }
    
    /**
     * Test a single route for accessibility
     */
    private function testSingleRoute(array $route): void
    {
        try {
            $this->output("Testing route: {$route['uri']} [{$route['name']}]");
            
            // Skip routes that require parameters
            if ($this->hasRequiredParameters($route['uri'])) {
                $this->recordRouteResult($route, 'skipped', 'Route requires parameters');
                return;
            }
            
            // Test GET method if available
            if (in_array('GET', $route['methods'])) {
                $result = $this->makeRouteRequest('GET', $route['uri']);
                $this->recordRouteResult($route, $result['status'], $result['message']);
            } else {
                $this->recordRouteResult($route, 'skipped', 'No GET method available');
            }
            
        } catch (Exception $e) {
            $this->recordRouteResult($route, 'error', $e->getMessage());
        }
    }
    
    /**
     * Check if route has required parameters
     */
    private function hasRequiredParameters(string $uri): bool
    {
        return strpos($uri, '{') !== false;
    }
    
    /**
     * Make HTTP request to route
     */
    private function makeRouteRequest(string $method, string $uri): array
    {
        try {
            // Create test request
            $request = Request::create($uri, $method);
            
            // Simulate basic response check
            $response = app()->handle($request);
            $statusCode = $response->getStatusCode();
            
            if ($statusCode < 400) {
                return ['status' => 'passed', 'message' => "HTTP {$statusCode}"];
            } else {
                return ['status' => 'failed', 'message' => "HTTP {$statusCode}"];
            }
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Record route test result
     */
    private function recordRouteResult(array $route, string $status, string $message): void
    {
        $this->results[] = [
            'route' => $route,
            'status' => $status,
            'message' => $message,
            'timestamp' => now()
        ];
        
        if ($status === 'passed') {
            $this->passedRoutes++;
            $this->output(" ✅ PASSED");
        } elseif ($status === 'failed') {
            $this->failedRoutes++;
            $this->output(" ❌ FAILED: {$message}");
        } else {
            $this->output(" ⏭️ SKIPPED: {$message}");
        }
    }
    
    /**
     * Run all test suites
     */
    public function runAllTests(): array
    {
        $this->output("\n🧪 Running All Test Suites...\n");
        
        $testSuites = [
            'unit' => 'tests/Unit/',
            'feature' => 'tests/Feature/',
            'integration' => 'tests/Integration/',
        ];
        
        foreach ($testSuites as $suite => $path) {
            if (is_dir($path)) {
                $this->runTestSuite($suite, $path);
            }
        }
        
        return $this->testResults;
    }
    
    /**
     * Run specific test suite
     */
    private function runTestSuite(string $suiteName, string $path): void
    {
        $this->output("Running {$suiteName} tests...");
        
        try {
            $output = [];
            $returnCode = 0;
            
            // Run PHPUnit for specific path
            exec("vendor/bin/phpunit {$path} --stop-on-failure 2>&1", $output, $returnCode);
            
            $this->testResults[$suiteName] = [
                'return_code' => $returnCode,
                'output' => implode("\n", $output),
                'status' => $returnCode === 0 ? 'passed' : 'failed'
            ];
            
            if ($returnCode === 0) {
                $this->output(" ✅ {$suiteName} tests PASSED");
            } else {
                $this->output(" ❌ {$suiteName} tests FAILED");
            }
            
        } catch (Exception $e) {
            $this->testResults[$suiteName] = [
                'return_code' => -1,
                'output' => $e->getMessage(),
                'status' => 'error'
            ];
            $this->output(" ❌ {$suiteName} tests ERROR: " . $e->getMessage());
        }
    }
    
    /**
     * Generate comprehensive route report
     */
    private function generateRouteReport(): array
    {
        $report = [
            'summary' => [
                'total_routes' => $this->totalRoutes,
                'passed_routes' => $this->passedRoutes,
                'failed_routes' => $this->failedRoutes,
                'skipped_routes' => $this->totalRoutes - $this->passedRoutes - $this->failedRoutes,
                'success_rate' => $this->totalRoutes > 0 ? round(($this->passedRoutes / $this->totalRoutes) * 100, 2) : 0
            ],
            'results' => $this->results,
            'timestamp' => now()
        ];
        
        $this->output("\n📊 Route Testing Summary:");
        $this->output("Total Routes: {$report['summary']['total_routes']}");
        $this->output("Passed: {$report['summary']['passed_routes']}");
        $this->output("Failed: {$report['summary']['failed_routes']}");
        $this->output("Skipped: {$report['summary']['skipped_routes']}");
        $this->output("Success Rate: {$report['summary']['success_rate']}%");
        
        return $report;
    }
    
    /**
     * Generate comprehensive test report
     */
    public function generateTestReport(): array
    {
        $report = [
            'route_testing' => $this->generateRouteReport(),
            'test_suites' => $this->testResults,
            'overall_status' => $this->calculateOverallStatus(),
            'recommendations' => $this->generateRecommendations(),
            'timestamp' => now()
        ];
        
        // Save report to file
        file_put_contents(
            'comprehensive_test_report.json',
            json_encode($report, JSON_PRETTY_PRINT)
        );
        
        $this->output("\n📋 Comprehensive Test Report Generated: comprehensive_test_report.json");
        
        return $report;
    }
    
    /**
     * Calculate overall testing status
     */
    private function calculateOverallStatus(): string
    {
        $allTestsPassed = true;
        
        foreach ($this->testResults as $suite) {
            if ($suite['status'] !== 'passed') {
                $allTestsPassed = false;
                break;
            }
        }
        
        $routeSuccessRate = $this->totalRoutes > 0 ? 
            ($this->passedRoutes / $this->totalRoutes) * 100 : 0;
        
        if ($allTestsPassed && $routeSuccessRate > 80) {
            return 'excellent';
        } elseif ($routeSuccessRate > 60) {
            return 'good';
        } elseif ($routeSuccessRate > 40) {
            return 'needs_improvement';
        } else {
            return 'critical';
        }
    }
    
    /**
     * Generate testing recommendations
     */
    private function generateRecommendations(): array
    {
        $recommendations = [];
        
        if ($this->failedRoutes > 0) {
            $recommendations[] = "Fix {$this->failedRoutes} failing routes";
        }
        
        foreach ($this->testResults as $suite => $result) {
            if ($result['status'] !== 'passed') {
                $recommendations[] = "Address issues in {$suite} test suite";
            }
        }
        
        if (empty($recommendations)) {
            $recommendations[] = "All tests passing! Consider adding more edge case tests";
        }
        
        return $recommendations;
    }
    
    /**
     * Output helper for consistent messaging
     */
    private function output(string $message): void
    {
        echo $message . "\n";
    }
    
    /**
     * Run complete testing workflow
     */
    public function runCompleteTest(): array
    {
        $this->output("🎯 Starting Comprehensive Testing Workflow");
        $this->output("=========================================\n");
        
        // Test all routes
        $routeResults = $this->testAllRoutes();
        
        // Run all test suites
        $testResults = $this->runAllTests();
        
        // Generate comprehensive report
        $report = $this->generateTestReport();
        
        $this->output("\n🎉 Comprehensive Testing Complete!");
        $this->output("Overall Status: " . strtoupper($report['overall_status']));
        
        return $report;
    }
}

// Execute if run directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $runner = new ComprehensiveTestRunner();
    $report = $runner->runCompleteTest();
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "FINAL SUMMARY\n";
    echo str_repeat("=", 50) . "\n";
    echo "Overall Status: " . strtoupper($report['overall_status']) . "\n";
    echo "Route Success Rate: " . $report['route_testing']['summary']['success_rate'] . "%\n";
    echo "Recommendations:\n";
    foreach ($report['recommendations'] as $rec) {
        echo "- " . $rec . "\n";
    }
    echo "\n";
} 