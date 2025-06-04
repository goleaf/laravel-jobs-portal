<?php

require_once __DIR__ . '/vendor/autoload.php';

class ApplicationHealthTester
{
    private $results = [];
    private $errors = [];

    public function __construct()
    {
        // Initialize Laravel app
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }

    public function testApplicationHealth()
    {
        echo "🏥 Testing application health...\n\n";
        
        $this->testDatabaseConnection();
        $this->testCriticalRoutes();
        $this->testViewCompilation();
        $this->generateHealthReport();
    }

    private function testDatabaseConnection()
    {
        echo "🔍 Testing database connection...\n";
        
        try {
            $pdo = DB::connection()->getPdo();
            if ($pdo) {
                $this->results['database'] = 'Connected';
                echo "✅ Database connection: OK\n";
            } else {
                $this->results['database'] = 'Failed';
                echo "❌ Database connection: FAILED\n";
            }
        } catch (Exception $e) {
            $this->results['database'] = 'Error: ' . $e->getMessage();
            echo "❌ Database connection error: " . $e->getMessage() . "\n";
        }
    }

    private function testCriticalRoutes()
    {
        echo "\n🛣️  Testing critical routes...\n";
        
        $criticalRoutes = [
            'front.home' => '/',
            'admin.dashboard.main' => '/admin',
            'admin.candidates.index' => '/admin/candidates',
        ];

        foreach ($criticalRoutes as $routeName => $uri) {
            try {
                $route = Route::getRoutes()->getByName($routeName);
                if ($route) {
                    $this->results['routes'][$routeName] = 'Exists';
                    echo "✅ Route $routeName: OK\n";
                } else {
                    $this->results['routes'][$routeName] = 'Missing';
                    echo "❌ Route $routeName: MISSING\n";
                }
            } catch (Exception $e) {
                $this->results['routes'][$routeName] = 'Error: ' . $e->getMessage();
                echo "❌ Route $routeName error: " . $e->getMessage() . "\n";
            }
        }
    }

    private function testViewCompilation()
    {
        echo "\n👁️  Testing view compilation...\n";
        
        $criticalViews = [
            'welcome',
            'admin.dashboard.main',
            'candidates.index'
        ];

        foreach ($criticalViews as $viewName) {
            try {
                if (View::exists($viewName)) {
                    $this->results['views'][$viewName] = 'Exists';
                    echo "✅ View $viewName: OK\n";
                } else {
                    $this->results['views'][$viewName] = 'Missing';
                    echo "❌ View $viewName: MISSING\n";
                }
            } catch (Exception $e) {
                $this->results['views'][$viewName] = 'Error: ' . $e->getMessage();
                echo "❌ View $viewName error: " . $e->getMessage() . "\n";
            }
        }
    }

    private function generateHealthReport()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "🏥 APPLICATION HEALTH REPORT\n";
        echo str_repeat("=", 80) . "\n\n";

        // Database Status
        echo "💾 DATABASE: " . $this->results['database'] . "\n\n";

        // Routes Status
        if (isset($this->results['routes'])) {
            echo "🛣️  ROUTES:\n";
            foreach ($this->results['routes'] as $route => $status) {
                $icon = str_contains($status, 'Error') || $status === 'Missing' ? '❌' : '✅';
                echo "  $icon $route: $status\n";
            }
            echo "\n";
        }

        // Views Status
        if (isset($this->results['views'])) {
            echo "👁️  VIEWS:\n";
            foreach ($this->results['views'] as $view => $status) {
                $icon = str_contains($status, 'Error') || $status === 'Missing' ? '❌' : '✅';
                echo "  $icon $view: $status\n";
            }
            echo "\n";
        }

        // Overall Status
        $overallStatus = $this->calculateOverallStatus();
        echo "🎯 OVERALL STATUS: $overallStatus\n";
        echo str_repeat("=", 80) . "\n";
    }

    private function calculateOverallStatus()
    {
        $hasErrors = false;
        
        if ($this->results['database'] !== 'Connected') {
            $hasErrors = true;
        }

        if (isset($this->results['routes'])) {
            foreach ($this->results['routes'] as $status) {
                if (str_contains($status, 'Error') || $status === 'Missing') {
                    $hasErrors = true;
                    break;
                }
            }
        }

        if (isset($this->results['views'])) {
            foreach ($this->results['views'] as $status) {
                if (str_contains($status, 'Error') || $status === 'Missing') {
                    $hasErrors = true;
                    break;
                }
            }
        }

        return $hasErrors ? '❌ CRITICAL ISSUES DETECTED' : '✅ HEALTHY';
    }
}

// Run the health test
try {
    $tester = new ApplicationHealthTester();
    $tester->testApplicationHealth();
    echo "\n✅ Health check completed!\n";
} catch (Exception $e) {
    echo "\n❌ Health check failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 