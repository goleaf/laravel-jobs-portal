<?php

require_once 'vendor/autoload.php';

class BladeTemplateAnalyzer
{
    private $viewsPath = 'resources/views';
    private $routesFile = 'routes/web.php';
    private $apiRoutesFile = 'routes/api.php';
    private $errors = [];
    private $warnings = [];
    private $routeUsage = [];
    private $definedRoutes = [];
    private $missingRoutes = [];
    private $unusedRoutes = [];

    public function analyzeAll()
    {
        echo "=== COMPREHENSIVE BLADE TEMPLATE ANALYSIS ===\n\n";
        
        // Load defined routes
        $this->loadDefinedRoutes();
        
        // Analyze all blade templates
        $bladeFiles = $this->getAllBladeFiles();
        
        foreach ($bladeFiles as $file) {
            $this->analyzeBladeFile($file);
        }
        
        // Generate comprehensive report
        $this->generateReport();
        
        // Generate fixes
        $this->generateFixSuggestions();
    }

    private function loadDefinedRoutes()
    {
        echo "Loading defined routes...\n";
        
        // Load web routes
        if (file_exists($this->routesFile)) {
            $content = file_get_contents($this->routesFile);
            $this->extractRoutesFromContent($content, 'web');
        }
        
        // Load API routes
        if (file_exists($this->apiRoutesFile)) {
            $content = file_get_contents($this->apiRoutesFile);
            $this->extractRoutesFromContent($content, 'api');
        }
        
        echo "Found " . count($this->definedRoutes) . " defined routes\n\n";
    }

    private function extractRoutesFromContent($content, $type)
    {
        // Extract Route::get, Route::post, etc.
        preg_match_all('/Route::(get|post|put|patch|delete|resource|apiResource)\s*\(\s*[\'"]([^\'"]*)[\'"].*?->name\s*\(\s*[\'"]([^\'"]*)[\'"]/', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $method = $match[1];
            $uri = $match[2];
            $name = $match[3];
            
            $this->definedRoutes[$name] = [
                'method' => $method,
                'uri' => $uri,
                'type' => $type,
                'used' => false
            ];
        }
        
        // Extract named routes without explicit name() method
        preg_match_all('/Route::(get|post|put|patch|delete)\s*\(\s*[\'"]([^\'"]*)[\'"].*?\[([^\]]*Controller[^\]]*)\]/', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $method = $match[1];
            $uri = $match[2];
            $controller = $match[3];
            
            // Generate probable route name from URI
            $routeName = str_replace(['/', '{', '}'], ['.', '', ''], $uri);
            $routeName = trim($routeName, '.');
            
            if (!isset($this->definedRoutes[$routeName])) {
                $this->definedRoutes[$routeName] = [
                    'method' => $method,
                    'uri' => $uri,
                    'type' => $type,
                    'controller' => $controller,
                    'used' => false
                ];
            }
        }
    }

    private function getAllBladeFiles()
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->viewsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }

    private function analyzeBladeFile($filePath)
    {
        $relativePath = str_replace(getcwd() . '/', '', $filePath);
        echo "Analyzing: $relativePath\n";
        
        $content = file_get_contents($filePath);
        $errors = [];
        $warnings = [];
        $routes = [];
        
        // Check for route() function calls
        preg_match_all('/route\s*\(\s*[\'"]([^\'"]*)[\'"]/', $content, $routeMatches);
        foreach ($routeMatches[1] as $routeName) {
            $routes[] = $routeName;
            
            if (isset($this->definedRoutes[$routeName])) {
                $this->definedRoutes[$routeName]['used'] = true;
            } else {
                $this->missingRoutes[] = [
                    'route' => $routeName,
                    'file' => $relativePath,
                    'line' => $this->getLineNumber($content, "route('$routeName')")
                ];
                $errors[] = "Missing route: '$routeName'";
            }
        }
        
        // Check for URL helper calls
        preg_match_all('/url\s*\(\s*[\'"]([^\'"]*)[\'"]/', $content, $urlMatches);
        foreach ($urlMatches[1] as $url) {
            $warnings[] = "Hardcoded URL found: '$url' - Consider using route() helper";
        }
        
        // Check for asset() calls with external URLs
        preg_match_all('/asset\s*\(\s*[\'"]([^\'"]*)[\'"]/', $content, $assetMatches);
        foreach ($assetMatches[1] as $asset) {
            if (strpos($asset, 'http') === 0 || strpos($asset, '//') === 0) {
                $warnings[] = "External asset URL: '$asset' - Should use local assets";
            }
        }
        
        // Check for broken @include and @extends
        preg_match_all('/@(include|extends)\s*\(\s*[\'"]([^\'"]*)[\'"]/', $content, $includeMatches, PREG_SET_ORDER);
        foreach ($includeMatches as $match) {
            $directive = $match[1];
            $viewName = $match[2];
            $viewPath = str_replace('.', '/', $viewName) . '.blade.php';
            $fullPath = $this->viewsPath . '/' . $viewPath;
            
            if (!file_exists($fullPath)) {
                $errors[] = "Missing view file for @$directive: '$viewName' (expected: $viewPath)";
            }
        }
        
        // Check for JavaScript and CSS in blade files
        if (preg_match('/<script[^>]*>[\s\S]*<\/script>/', $content)) {
            $warnings[] = "JavaScript code found in blade file - Should be moved to separate JS file";
        }
        
        if (preg_match('/<style[^>]*>[\s\S]*<\/style>/', $content)) {
            $warnings[] = "CSS code found in blade file - Should be moved to separate CSS file";
        }
        
        // Check for CDN usage
        if (preg_match('/cdn\.|jsdelivr\.|unpkg\.|cdnjs\./', $content)) {
            $warnings[] = "CDN usage detected - Should use local assets with npm";
        }
        
        // Check for Bootstrap classes (need to be converted to Tailwind)
        $bootstrapClasses = ['container-fluid', 'row', 'col-', 'btn-primary', 'btn-secondary', 'card-header', 'card-body', 'form-group', 'form-control'];
        foreach ($bootstrapClasses as $class) {
            if (strpos($content, $class) !== false) {
                $warnings[] = "Bootstrap class '$class' found - Needs conversion to TailwindCSS";
            }
        }
        
        // Store results
        if (!empty($errors) || !empty($warnings) || !empty($routes)) {
            $this->routeUsage[$relativePath] = [
                'routes' => $routes,
                'errors' => $errors,
                'warnings' => $warnings
            ];
        }
        
        $this->errors = array_merge($this->errors, $errors);
        $this->warnings = array_merge($this->warnings, $warnings);
    }

    private function getLineNumber($content, $searchString)
    {
        $lines = explode("\n", $content);
        foreach ($lines as $lineNum => $line) {
            if (strpos($line, $searchString) !== false) {
                return $lineNum + 1;
            }
        }
        return 'Unknown';
    }

    private function generateReport()
    {
        echo "\n=== ANALYSIS SUMMARY ===\n";
        echo "Total files analyzed: " . count($this->routeUsage) . "\n";
        echo "Total errors: " . count($this->errors) . "\n";
        echo "Total warnings: " . count($this->warnings) . "\n";
        echo "Missing routes: " . count($this->missingRoutes) . "\n\n";
        
        // Find unused routes
        foreach ($this->definedRoutes as $routeName => $routeData) {
            if (!$routeData['used']) {
                $this->unusedRoutes[] = $routeName;
            }
        }
        
        echo "Unused routes: " . count($this->unusedRoutes) . "\n\n";
        
        if (!empty($this->errors)) {
            echo "=== CRITICAL ERRORS ===\n";
            foreach (array_unique($this->errors) as $error) {
                echo "❌ $error\n";
            }
            echo "\n";
        }
        
        if (!empty($this->warnings)) {
            echo "=== WARNINGS (First 10) ===\n";
            foreach (array_slice(array_unique($this->warnings), 0, 10) as $warning) {
                echo "⚠️  $warning\n";
            }
            if (count($this->warnings) > 10) {
                echo "... and " . (count($this->warnings) - 10) . " more warnings\n";
            }
            echo "\n";
        }
        
        if (!empty($this->missingRoutes)) {
            echo "=== MISSING ROUTES ===\n";
            foreach (array_slice($this->missingRoutes, 0, 10) as $missing) {
                echo "🔍 Route '{$missing['route']}' in {$missing['file']} (line {$missing['line']})\n";
            }
            if (count($this->missingRoutes) > 10) {
                echo "... and " . (count($this->missingRoutes) - 10) . " more missing routes\n";
            }
            echo "\n";
        }
        
        if (!empty($this->unusedRoutes)) {
            echo "=== UNUSED ROUTES (First 10) ===\n";
            foreach (array_slice($this->unusedRoutes, 0, 10) as $unused) {
                echo "🗑️  Route '$unused' is defined but never used\n";
            }
            if (count($this->unusedRoutes) > 10) {
                echo "... and " . (count($this->unusedRoutes) - 10) . " more unused routes\n";
            }
            echo "\n";
        }
    }

    private function generateFixSuggestions()
    {
        echo "=== FIX SUGGESTIONS ===\n\n";
        
        echo "1. MISSING ROUTES - Create these routes in routes/web.php:\n";
        $uniqueMissingRoutes = array_unique(array_column($this->missingRoutes, 'route'));
        foreach (array_slice($uniqueMissingRoutes, 0, 5) as $route) {
            echo "   Route::get('/$route', [SomeController::class, 'method'])->name('$route');\n";
        }
        echo "\n";
        
        echo "2. CDN TO LOCAL MIGRATION:\n";
        echo "   - Run: npm install bootstrap tailwindcss\n";
        echo "   - Replace CDN links with @vite(['resources/css/app.css', 'resources/js/app.js'])\n";
        echo "   - Remove all external CDN references\n\n";
        
        echo "3. BOOTSTRAP TO TAILWIND CONVERSION:\n";
        echo "   - Replace 'container-fluid' with 'w-full max-w-none'\n";
        echo "   - Replace 'row' with 'flex flex-wrap'\n";
        echo "   - Replace 'col-*' with 'w-full md:w-*'\n";
        echo "   - Replace 'btn-primary' with 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'\n\n";
        
        echo "4. JAVASCRIPT/CSS CLEANUP:\n";
        echo "   - Move inline JavaScript to resources/js/\n";
        echo "   - Move inline CSS to resources/css/\n";
        echo "   - Use Vite for asset compilation\n\n";
        
        echo "5. COMPONENTS CREATION:\n";
        echo "   - Create reusable components for repeated UI elements\n";
        echo "   - Use @component directive for complex UI blocks\n";
        echo "   - Implement a consistent layout structure\n\n";
        
        echo "=== NEXT STEPS ===\n";
        echo "1. Fix critical route errors first\n";
        echo "2. Remove CDN dependencies\n";
        echo "3. Convert Bootstrap to TailwindCSS\n";
        echo "4. Create blade components\n";
        echo "5. Clean up unused routes\n";
        
        echo "\n=== COMPLETED ===\n";
    }

    public function getResults()
    {
        return [
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'missing_routes' => $this->missingRoutes,
            'unused_routes' => $this->unusedRoutes,
            'route_usage' => $this->routeUsage
        ];
    }
}

// Run the analyzer
$analyzer = new BladeTemplateAnalyzer();
$analyzer->analyzeAll();

// Save results to JSON for further processing
$results = $analyzer->getResults();
file_put_contents('blade_analysis_results.json', json_encode($results, JSON_PRETTY_PRINT));

echo "\n📄 Results saved to blade_analysis_results.json\n"; 