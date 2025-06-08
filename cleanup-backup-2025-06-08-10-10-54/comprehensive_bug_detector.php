<?php
/**
 * Comprehensive Bug Detection & Feature Validation Script
 * Systematically checks all aspects of the Laravel Vue3 SPA
 */

// Bootstrap Laravel application
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class ComprehensiveBugDetector
{
    private array $bugs = [];
    private array $features = [];
    private array $warnings = [];

    public function __construct()
    {
        echo "🔍 COMPREHENSIVE BUG DETECTION & FEATURE VALIDATION\n";
        echo "==================================================\n\n";
    }

    public function runFullAudit(): array
    {
        $this->checkLaravelConfiguration();
        $this->checkApiRoutes();
        $this->checkControllers();
        $this->checkRequestValidation();
        $this->checkModels();
        $this->checkVueComponents();
        $this->checkBuildConfiguration();
        $this->checkSecurity();
        $this->checkPerformance();
        $this->checkDatabase();
        
        return $this->generateReport();
    }

    private function checkLaravelConfiguration()
    {
        echo "🔧 Checking Laravel Configuration...\n";
        
        // Check .env file
        if (!file_exists('.env')) {
            $this->bugs[] = 'CRITICAL: .env file missing';
        }
        
        // Check app key
        $appKey = env('APP_KEY');
        if (empty($appKey)) {
            $this->bugs[] = 'CRITICAL: APP_KEY not set';
        }
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->features[] = '✅ Database connection working';
        } catch (Exception $e) {
            $this->bugs[] = 'CRITICAL: Database connection failed - ' . $e->getMessage();
        }
        
        // Check cache configuration
        if (config('cache.default') === 'file') {
            $this->warnings[] = 'Cache using file driver (consider Redis for production)';
        }
        
        // Check session configuration
        if (config('session.driver') === 'file') {
            $this->warnings[] = 'Session using file driver (consider database/Redis for production)';
        }
        
        echo "   Completed Laravel configuration check\n";
    }

    private function checkApiRoutes()
    {
        echo "🛣️  Checking API Routes...\n";
        
        $routes = Route::getRoutes();
        $apiRoutes = 0;
        $webRoutes = 0;
        $missingMiddleware = [];
        
        foreach ($routes as $route) {
            $uri = $route->uri();
            
            if (str_starts_with($uri, 'api/')) {
                $apiRoutes++;
                
                // Check for API middleware
                $middleware = $route->middleware();
                if (!in_array('api', $middleware)) {
                    $missingMiddleware[] = $uri;
                }
            } else {
                $webRoutes++;
            }
        }
        
        $this->features[] = "✅ Found {$apiRoutes} API routes";
        $this->features[] = "✅ Found {$webRoutes} web routes";
        
        if (!empty($missingMiddleware)) {
            $this->warnings[] = 'API routes missing middleware: ' . implode(', ', array_slice($missingMiddleware, 0, 5));
        }
        
        echo "   Completed API routes check\n";
    }

    private function checkControllers()
    {
        echo "🎮 Checking Controllers...\n";
        
        $controllerPath = app_path('Http/Controllers');
        $controllers = $this->getPhpFiles($controllerPath);
        
        $validControllers = 0;
        $issues = [];
        
        foreach ($controllers as $controller) {
            try {
                $className = $this->getClassNameFromFile($controller);
                if ($className && class_exists($className)) {
                    $validControllers++;
                    
                    // Check for proper methods
                    $reflection = new ReflectionClass($className);
                    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
                    
                    if (count($methods) === 0) {
                        $issues[] = "{$className} has no public methods";
                    }
                } else {
                    $issues[] = "Cannot load controller: {$controller}";
                }
            } catch (Exception $e) {
                $issues[] = "Error checking {$controller}: " . $e->getMessage();
            }
        }
        
        $this->features[] = "✅ Found {$validControllers} valid controllers";
        
        if (!empty($issues)) {
            $this->warnings[] = 'Controller issues: ' . implode(', ', array_slice($issues, 0, 3));
        }
        
        echo "   Completed controllers check\n";
    }

    private function checkRequestValidation()
    {
        echo "📝 Checking Request Validation...\n";
        
        $requestPath = app_path('Http/Requests');
        $requests = $this->getPhpFiles($requestPath);
        
        $validRequests = 0;
        $issues = [];
        
        foreach ($requests as $request) {
            try {
                $className = $this->getClassNameFromFile($request);
                if ($className && class_exists($className)) {
                    $validRequests++;
                    
                    $reflection = new ReflectionClass($className);
                    if (!$reflection->hasMethod('rules')) {
                        $issues[] = "{$className} missing rules() method";
                    }
                    if (!$reflection->hasMethod('authorize')) {
                        $issues[] = "{$className} missing authorize() method";
                    }
                }
            } catch (Exception $e) {
                $issues[] = "Error checking {$request}: " . $e->getMessage();
            }
        }
        
        $this->features[] = "✅ Found {$validRequests} request validation classes";
        
        if (!empty($issues)) {
            $this->warnings[] = 'Request validation issues: ' . implode(', ', array_slice($issues, 0, 3));
        }
        
        echo "   Completed request validation check\n";
    }

    private function checkModels()
    {
        echo "🗄️  Checking Models...\n";
        
        $modelPath = app_path('Models');
        $models = $this->getPhpFiles($modelPath);
        
        $validModels = 0;
        $issues = [];
        
        foreach ($models as $model) {
            try {
                $className = $this->getClassNameFromFile($model);
                if ($className && class_exists($className)) {
                    $validModels++;
                    
                    $reflection = new ReflectionClass($className);
                    if (!$reflection->isSubclassOf(\Illuminate\Database\Eloquent\Model::class)) {
                        $issues[] = "{$className} doesn't extend Eloquent Model";
                    }
                }
            } catch (Exception $e) {
                $issues[] = "Error checking {$model}: " . $e->getMessage();
            }
        }
        
        $this->features[] = "✅ Found {$validModels} Eloquent models";
        
        if (!empty($issues)) {
            $this->warnings[] = 'Model issues: ' . implode(', ', array_slice($issues, 0, 3));
        }
        
        echo "   Completed models check\n";
    }

    private function checkVueComponents()
    {
        echo "⚡ Checking Vue Components...\n";
        
        $vueFiles = [];
        $this->findVueFiles('resources/js', $vueFiles);
        
        $validComponents = 0;
        $issues = [];
        
        foreach ($vueFiles as $vueFile) {
            $content = file_get_contents($vueFile);
            
            if (str_contains($content, '<template>') && str_contains($content, '<script')) {
                $validComponents++;
            } else {
                $issues[] = basename($vueFile) . ' missing template or script sections';
            }
            
            // Check for TypeScript
            if (str_contains($content, 'lang="ts"')) {
                // TypeScript component - good
            } else {
                $this->warnings[] = basename($vueFile) . ' not using TypeScript';
            }
        }
        
        $this->features[] = "✅ Found {$validComponents} valid Vue components";
        
        if (!empty($issues)) {
            $this->warnings[] = 'Vue component issues: ' . implode(', ', array_slice($issues, 0, 3));
        }
        
        echo "   Completed Vue components check\n";
    }

    private function checkBuildConfiguration()
    {
        echo "🏗️  Checking Build Configuration...\n";
        
        // Check Vite config
        if (file_exists('vite.config.js') || file_exists('vite.config.ts')) {
            $this->features[] = '✅ Vite configuration found';
        } else {
            $this->bugs[] = 'Vite configuration missing';
        }
        
        // Check package.json
        if (file_exists('package.json')) {
            $packageJson = json_decode(file_get_contents('package.json'), true);
            
            if (isset($packageJson['dependencies']['vue'])) {
                $this->features[] = '✅ Vue dependency found';
            } else {
                $this->bugs[] = 'Vue dependency missing';
            }
            
            if (isset($packageJson['devDependencies']['typescript'])) {
                $this->features[] = '✅ TypeScript dependency found';
            } else {
                $this->warnings[] = 'TypeScript dependency missing';
            }
        } else {
            $this->bugs[] = 'package.json missing';
        }
        
        // Check if node_modules exists
        if (is_dir('node_modules')) {
            $this->features[] = '✅ Node modules installed';
        } else {
            $this->bugs[] = 'Node modules not installed (run npm install)';
        }
        
        echo "   Completed build configuration check\n";
    }

    private function checkSecurity()
    {
        echo "🔒 Checking Security...\n";
        
        // Check CSRF protection
        if (config('session.same_site') === 'strict') {
            $this->features[] = '✅ Strict SameSite cookie policy';
        } else {
            $this->warnings[] = 'Consider setting session.same_site to strict';
        }
        
        // Check HTTPS enforcement
        if (config('session.secure') === true) {
            $this->features[] = '✅ Secure cookies enabled';
        } else {
            $this->warnings[] = 'Consider enabling secure cookies for production';
        }
        
        // Check debug mode
        if (config('app.debug') === true && config('app.env') !== 'local') {
            $this->bugs[] = 'DEBUG mode enabled in non-local environment';
        }
        
        echo "   Completed security check\n";
    }

    private function checkPerformance()
    {
        echo "⚡ Checking Performance...\n";
        
        // Check for compiled views
        if (is_dir('storage/framework/views') && count(glob('storage/framework/views/*.php')) > 0) {
            $this->features[] = '✅ Compiled views found';
        } else {
            $this->warnings[] = 'No compiled views found';
        }
        
        // Check for route caching
        if (file_exists('bootstrap/cache/routes-v7.php')) {
            $this->features[] = '✅ Route cache exists';
        } else {
            $this->warnings[] = 'Routes not cached (run php artisan route:cache)';
        }
        
        // Check for config caching
        if (file_exists('bootstrap/cache/config.php')) {
            $this->features[] = '✅ Config cache exists';
        } else {
            $this->warnings[] = 'Config not cached (run php artisan config:cache)';
        }
        
        echo "   Completed performance check\n";
    }

    private function checkDatabase()
    {
        echo "🗃️  Checking Database...\n";
        
        try {
            // Check if migrations table exists
            if (Schema::hasTable('migrations')) {
                $this->features[] = '✅ Migrations table exists';
                
                $migrationCount = DB::table('migrations')->count();
                $this->features[] = "✅ {$migrationCount} migrations applied";
            } else {
                $this->bugs[] = 'Migrations table missing (run php artisan migrate)';
            }
            
            // Check for common tables
            $tables = ['users', 'jobs', 'companies'];
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    $this->features[] = "✅ {$table} table exists ({$count} records)";
                } else {
                    $this->warnings[] = "{$table} table missing";
                }
            }
            
        } catch (Exception $e) {
            $this->bugs[] = 'Database check failed: ' . $e->getMessage();
        }
        
        echo "   Completed database check\n";
    }

    private function getPhpFiles(string $directory): array
    {
        if (!is_dir($directory)) {
            return [];
        }
        
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }

    private function findVueFiles(string $directory, array &$files)
    {
        if (!is_dir($directory)) {
            return;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'vue') {
                $files[] = $file->getPathname();
            }
        }
    }

    private function getClassNameFromFile(string $file): ?string
    {
        $content = file_get_contents($file);
        
        // Extract namespace
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];
        } else {
            return null;
        }
        
        // Extract class name
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            $className = $matches[1];
            return $namespace . '\\' . $className;
        }
        
        return null;
    }

    private function generateReport(): array
    {
        echo "\n🎯 GENERATING COMPREHENSIVE REPORT\n";
        echo "==================================\n\n";
        
        echo "📈 SUMMARY:\n";
        echo "  ✅ Features Working: " . count($this->features) . "\n";
        echo "  ⚠️  Warnings: " . count($this->warnings) . "\n";
        echo "  🐛 Bugs Found: " . count($this->bugs) . "\n\n";
        
        if (!empty($this->features)) {
            echo "✅ WORKING FEATURES:\n";
            foreach ($this->features as $feature) {
                echo "  {$feature}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->warnings)) {
            echo "⚠️  WARNINGS:\n";
            foreach ($this->warnings as $warning) {
                echo "  ⚠️  {$warning}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->bugs)) {
            echo "🐛 BUGS TO FIX:\n";
            foreach ($this->bugs as $bug) {
                echo "  🐛 {$bug}\n";
            }
            echo "\n";
        }
        
        return [
            'features' => $this->features,
            'warnings' => $this->warnings,
            'bugs' => $this->bugs,
            'summary' => [
                'features_count' => count($this->features),
                'warnings_count' => count($this->warnings),
                'bugs_count' => count($this->bugs)
            ]
        ];
    }
}

// Run the comprehensive audit
try {
    $detector = new ComprehensiveBugDetector();
    $report = $detector->runFullAudit();
    
    // Save report to file
    file_put_contents('bug_detection_report.json', json_encode($report, JSON_PRETTY_PRINT));
    
    echo "📁 Full report saved to bug_detection_report.json\n";
    
} catch (Exception $e) {
    echo "❌ Error running bug detection: " . $e->getMessage() . "\n";
} 