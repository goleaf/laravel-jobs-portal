<?php

/**
 * Context7 Comprehensive Project Analysis
 * Level 4 Complex System Task: Laravel Blade to Vue3 SPA Migration
 * Phase 1: Comprehensive Analysis and Planning
 */

class Context7ComprehensiveProjectAnalysis
{
    private array $analysis = [];
    private array $controllers = [];
    private array $routes = [];
    private array $bladeFiles = [];
    private array $requestFiles = [];
    
    public function executeComprehensiveAnalysis(): void
    {
        echo "🚀 CONTEXT7 COMPREHENSIVE PROJECT ANALYSIS\n";
        echo "==========================================\n";
        echo "Level 4 Complex System Task: Laravel Blade to Vue3 SPA Migration\n";
        echo "Phase 1: Comprehensive Analysis and Planning\n\n";
        
        $this->analyzeControllerFunctions();
        $this->analyzeRequestFileCoverage();
        $this->analyzeRoutes();
        $this->analyzeBladeFiles();
        $this->assessVue3MigrationScope();
        $this->generateComprehensiveReport();
    }
    
    private function analyzeControllerFunctions(): void
    {
        echo "🔍 Analyzing all controller functions...\n";
        
        $controllerDirs = [
            'app/Http/Controllers/Admin',
            'app/Http/Controllers/Api', 
            'app/Http/Controllers/Auth',
            'app/Http/Controllers/Candidate',
            'app/Http/Controllers/Candidates',
            'app/Http/Controllers/Employer',
            'app/Http/Controllers/Front',
            'app/Http/Controllers/Web'
        ];
        
        foreach ($controllerDirs as $dir) {
            $this->scanControllerDirectory($dir);
        }
        
        echo "  ✅ Controller analysis complete\n\n";
    }
    
    private function scanControllerDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        
        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            $this->analyzeControllerFile($file);
        }
        
        // Scan subdirectories
        $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
        foreach ($subdirs as $subdir) {
            $this->scanControllerDirectory($subdir);
        }
    }
    
    private function analyzeControllerFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $className = $this->extractClassName($filePath);
        
        // Extract all public methods
        preg_match_all('/public\s+function\s+(\w+)\s*\([^)]*\)/', $content, $matches);
        
        if (isset($matches[1])) {
            foreach ($matches[1] as $method) {
                if (!in_array($method, ['__construct', '__destruct'])) {
                    $this->controllers[$className][] = [
                        'method' => $method,
                        'file' => $filePath,
                        'has_request' => $this->checkForRequestParameter($content, $method)
                    ];
                }
            }
        }
    }
    
    private function checkForRequestParameter(string $content, string $method): bool
    {
        // Look for method with Request parameter
        $pattern = '/public\s+function\s+' . preg_quote($method) . '\s*\([^)]*Request[^)]*\)/';
        return preg_match($pattern, $content) > 0;
    }
    
    private function extractClassName(string $filePath): string
    {
        return basename($filePath, '.php');
    }
    
    private function analyzeRequestFileCoverage(): void
    {
        echo "📋 Analyzing request file coverage...\n";
        
        $requestDirs = [
            'app/Http/Requests/Admin',
            'app/Http/Requests/Api',
            'app/Http/Requests/Auth', 
            'app/Http/Requests/Candidate',
            'app/Http/Requests/Company',
            'app/Http/Requests/Contact',
            'app/Http/Requests/Enhanced',
            'app/Http/Requests/Financial',
            'app/Http/Requests/Job',
            'app/Http/Requests/Location',
            'app/Http/Requests/MasterData',
            'app/Http/Requests/Skill',
            'app/Http/Requests/Transaction',
            'app/Http/Requests/User',
            'app/Http/Requests/Web'
        ];
        
        foreach ($requestDirs as $dir) {
            $this->scanRequestDirectory($dir);
        }
        
        echo "  ✅ Request file coverage analysis complete\n\n";
    }
    
    private function scanRequestDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        
        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            $this->requestFiles[] = basename($file, '.php');
        }
    }
    
    private function analyzeRoutes(): void
    {
        echo "🛣️ Analyzing all routes...\n";
        
        $routeFiles = [
            'routes/web.php',
            'routes/api.php',
            'routes/admin.php',
            'routes/employer.php',
            'routes/candidate.php'
        ];
        
        foreach ($routeFiles as $file) {
            if (file_exists($file)) {
                $this->analyzeRouteFile($file);
            }
        }
        
        echo "  ✅ Route analysis complete\n\n";
    }
    
    private function analyzeRouteFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        
        // Extract routes
        preg_match_all('/Route::(get|post|put|patch|delete|resource|group)\s*\([^;]+;/', $content, $matches);
        
        if (isset($matches[0])) {
            foreach ($matches[0] as $route) {
                $this->routes[] = [
                    'file' => $filePath,
                    'route' => $route,
                    'type' => $this->extractRouteType($route)
                ];
            }
        }
    }
    
    private function extractRouteType(string $route): string
    {
        if (strpos($route, 'Route::get') !== false) return 'GET';
        if (strpos($route, 'Route::post') !== false) return 'POST';
        if (strpos($route, 'Route::put') !== false) return 'PUT';
        if (strpos($route, 'Route::patch') !== false) return 'PATCH';
        if (strpos($route, 'Route::delete') !== false) return 'DELETE';
        if (strpos($route, 'Route::resource') !== false) return 'RESOURCE';
        if (strpos($route, 'Route::group') !== false) return 'GROUP';
        return 'UNKNOWN';
    }
    
    private function analyzeBladeFiles(): void
    {
        echo "🎨 Analyzing all Blade files...\n";
        
        $this->scanBladeDirectory('resources/views');
        
        echo "  ✅ Blade file analysis complete\n\n";
    }
    
    private function scanBladeDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        
        $files = glob($dir . '/*.blade.php');
        foreach ($files as $file) {
            $this->bladeFiles[] = [
                'file' => $file,
                'size' => filesize($file),
                'components' => $this->analyzeBladeComponents($file)
            ];
        }
        
        // Scan subdirectories
        $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
        foreach ($subdirs as $subdir) {
            $this->scanBladeDirectory($subdir);
        }
    }
    
    private function analyzeBladeComponents(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $components = [];
        
        // Look for common UI patterns
        if (preg_match_all('/@include\([\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            $components['includes'] = $matches[1];
        }
        
        if (preg_match_all('/<x-([a-zA-Z-]+)/', $content, $matches)) {
            $components['x_components'] = $matches[1];
        }
        
        if (preg_match_all('/@component\([\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            $components['components'] = $matches[1];
        }
        
        return $components;
    }
    
    private function assessVue3MigrationScope(): void
    {
        echo "⚡ Assessing Vue3 migration scope...\n";
        
        $this->analysis['migration_scope'] = [
            'blade_files_count' => count($this->bladeFiles),
            'total_blade_size' => array_sum(array_column($this->bladeFiles, 'size')),
            'controller_methods' => $this->countControllerMethods(),
            'routes_count' => count($this->routes),
            'api_endpoints_needed' => $this->estimateApiEndpoints(),
            'vue_components_needed' => $this->estimateVueComponents(),
            'complexity_score' => $this->calculateComplexityScore()
        ];
        
        echo "  ✅ Migration scope assessment complete\n\n";
    }
    
    private function countControllerMethods(): int
    {
        $total = 0;
        foreach ($this->controllers as $methods) {
            $total += count($methods);
        }
        return $total;
    }
    
    private function estimateApiEndpoints(): int
    {
        // Each Blade view typically needs 1-3 API endpoints
        return count($this->bladeFiles) * 2;
    }
    
    private function estimateVueComponents(): int
    {
        // Each Blade file becomes 1-2 Vue components
        return count($this->bladeFiles) * 1.5;
    }
    
    private function calculateComplexityScore(): int
    {
        $score = 0;
        $score += count($this->bladeFiles) * 2; // Blade file complexity
        $score += count($this->routes) * 1; // Route complexity
        $score += $this->countControllerMethods() * 3; // Controller method complexity
        
        return $score;
    }
    
    private function generateComprehensiveReport(): void
    {
        echo "📊 CONTEXT7 COMPREHENSIVE PROJECT ANALYSIS REPORT\n";
        echo "=================================================\n";
        
        echo "\n🎯 CONTROLLER FUNCTION ANALYSIS:\n";
        $totalMethods = $this->countControllerMethods();
        $methodsWithRequests = $this->countMethodsWithRequests();
        $requestCoverage = ($methodsWithRequests / max($totalMethods, 1)) * 100;
        
        echo "  • Total Controllers: " . count($this->controllers) . "\n";
        echo "  • Total Methods: $totalMethods\n";
        echo "  • Methods with Request Files: $methodsWithRequests\n";
        echo "  • Request Coverage: " . number_format($requestCoverage, 1) . "%\n";
        
        echo "\n📋 REQUEST FILE COVERAGE:\n";
        echo "  • Existing Request Files: " . count($this->requestFiles) . "\n";
        echo "  • Missing Request Files: " . ($totalMethods - $methodsWithRequests) . "\n";
        
        echo "\n🛣️ ROUTE ANALYSIS:\n";
        echo "  • Total Routes: " . count($this->routes) . "\n";
        $routeTypes = array_count_values(array_column($this->routes, 'type'));
        foreach ($routeTypes as $type => $count) {
            echo "  • $type Routes: $count\n";
        }
        
        echo "\n🎨 BLADE FILE ANALYSIS:\n";
        echo "  • Total Blade Files: " . count($this->bladeFiles) . "\n";
        echo "  • Total Blade Size: " . number_format($this->analysis['migration_scope']['total_blade_size'] / 1024, 1) . " KB\n";
        
        echo "\n⚡ VUE3 MIGRATION SCOPE:\n";
        echo "  • Estimated API Endpoints Needed: " . $this->analysis['migration_scope']['api_endpoints_needed'] . "\n";
        echo "  • Estimated Vue Components Needed: " . intval($this->analysis['migration_scope']['vue_components_needed']) . "\n";
        echo "  • Migration Complexity Score: " . $this->analysis['migration_scope']['complexity_score'] . "\n";
        
        $this->generateMigrationPlan();
        $this->generateGitStrategy();
    }
    
    private function countMethodsWithRequests(): int
    {
        $count = 0;
        foreach ($this->controllers as $methods) {
            foreach ($methods as $method) {
                if ($method['has_request']) {
                    $count++;
                }
            }
        }
        return $count;
    }
    
    private function generateMigrationPlan(): void
    {
        echo "\n🏗️ RECOMMENDED MIGRATION PLAN:\n";
        echo "============================\n";
        
        $complexity = $this->analysis['migration_scope']['complexity_score'];
        
        if ($complexity > 1000) {
            echo "  🔥 LEVEL 4 COMPLEX SYSTEM MIGRATION\n";
            echo "  • Duration: 3-4 weeks\n";
            echo "  • Phases: 5 phases (Foundation, API, Frontend, Integration, Testing)\n";
            echo "  • Risk: High - Requires careful planning and phased approach\n";
        } else {
            echo "  ⚡ LEVEL 3 FEATURE MIGRATION\n";
            echo "  • Duration: 1-2 weeks\n";
            echo "  • Phases: 3 phases (Setup, Migration, Testing)\n";
            echo "  • Risk: Medium - Standard migration approach\n";
        }
        
        echo "\n📋 MIGRATION PHASES:\n";
        echo "  1. Foundation Setup (Vue3 + Vite + Router + State Management)\n";
        echo "  2. API Development (Laravel API endpoints)\n";
        echo "  3. Frontend Development (Vue3 components)\n";
        echo "  4. Integration & Testing (E2E tests)\n";
        echo "  5. Deployment & Cleanup (Remove Blade files)\n";
    }
    
    private function generateGitStrategy(): void
    {
        echo "\n📦 GIT STRATEGY:\n";
        echo "===============\n";
        echo "  • Create feature branch: feature/vue3-spa-migration\n";
        echo "  • Commit strategy: Phase-based commits\n";
        echo "  • Backup strategy: Tag current state before migration\n";
        echo "  • Review strategy: PR review for each phase\n";
        echo "  • Rollback strategy: Maintain Blade files until Vue3 is stable\n";
        
        echo "\n✅ ANALYSIS COMPLETE - READY FOR MIGRATION PLANNING\n";
        echo "Next: Execute migration plan with Context7 Level 4 workflow\n";
    }
}

// Execute comprehensive analysis
$analyzer = new Context7ComprehensiveProjectAnalysis();
$analyzer->executeComprehensiveAnalysis(); 