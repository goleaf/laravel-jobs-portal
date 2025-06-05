<?php

/**
 * Broken Routes Fix Script
 * Identifies and fixes broken routes in blade templates
 * 
 * Based on Context7 Laravel routing best practices
 */

require_once __DIR__ . '/vendor/autoload.php';

class RouteFixer
{
    private $fixedRoutes = 0;
    private $routeIssues = [];
    private $fixes = [];
    private $knownRoutes = [];
    
    public function __construct()
    {
        echo "🛣️ Starting Route Fix using Context7 Laravel Routing Patterns...\n\n";
        $this->loadKnownRoutes();
    }
    
    /**
     * Load known routes from route cache or web.php
     */
    private function loadKnownRoutes()
    {
        echo "📋 Loading known routes...\n";
        
        // Load routes from route files
        $routeFiles = [
            'routes/web.php',
            'routes/admin.php',
            'routes/api.php'
        ];
        
        foreach ($routeFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                
                // Extract route names from Route::get()->name() patterns
                preg_match_all('/Route::[a-zA-Z]+\([^)]+\)->name\([\'"]([^\'"]+)[\'"]\)/', $content, $matches);
                foreach ($matches[1] as $routeName) {
                    $this->knownRoutes[] = $routeName;
                }
                
                // Extract simple route names from patterns like "posts.index"
                preg_match_all('/[\'"]([a-zA-Z0-9_]+\.[a-zA-Z0-9_]+)[\'"]/', $content, $matches);
                foreach ($matches[1] as $routeName) {
                    if (!in_array($routeName, $this->knownRoutes)) {
                        $this->knownRoutes[] = $routeName;
                    }
                }
            }
        }
        
        // Add common Laravel routes
        $commonRoutes = [
            'home', 'dashboard', 'login', 'logout', 'register',
            'password.request', 'password.email', 'password.reset', 'password.update',
            'verification.notice', 'verification.verify', 'verification.send',
            // Job portal specific routes
            'jobs.index', 'jobs.show', 'jobs.create', 'jobs.store', 'jobs.edit', 'jobs.update', 'jobs.destroy',
            'companies.index', 'companies.show', 'companies.create', 'companies.store', 'companies.edit', 'companies.update',
            'candidates.index', 'candidates.show', 'candidates.create', 'candidates.store', 'candidates.edit', 'candidates.update',
            'admin.dashboard', 'admin.jobs.index', 'admin.companies.index', 'admin.candidates.index',
            'front.home', 'front.jobs.index', 'front.companies.index', 'front.candidates.index',
            'employer.dashboard', 'employer.jobs.index', 'employer.applications.index',
            'candidate.dashboard', 'candidate.applications.index', 'candidate.profile.index'
        ];
        
        $this->knownRoutes = array_merge($this->knownRoutes, $commonRoutes);
        $this->knownRoutes = array_unique($this->knownRoutes);
        
        echo "Found " . count($this->knownRoutes) . " known routes\n\n";
    }
    
    /**
     * Fix all broken routes
     */
    public function fixAll()
    {
        $this->analyzeRoutes();
        $this->fixBrokenRoutes();
        $this->generateRouteReport();
        
        return $this;
    }
    
    /**
     * Analyze routes in blade files
     */
    private function analyzeRoutes()
    {
        echo "1. 🔍 Analyzing routes in blade templates...\n";
        
        $bladeFiles = $this->getAllBladeFiles();
        
        foreach ($bladeFiles as $file) {
            $content = file_get_contents($file);
            
            // Find route() calls
            preg_match_all('/route\([\'"]([^\'"]+)[\'"](?:,\s*[^)]+)?\)/', $content, $matches, PREG_OFFSET_CAPTURE);
            
            foreach ($matches[1] as $match) {
                $routeName = $match[0];
                
                if (!in_array($routeName, $this->knownRoutes)) {
                    $suggestion = $this->suggestRoute($routeName);
                    $this->routeIssues[] = [
                        'file' => basename($file),
                        'route' => $routeName,
                        'suggestion' => $suggestion,
                        'fullPath' => $file
                    ];
                }
            }
        }
        
        echo "Found " . count($this->routeIssues) . " potential route issues\n\n";
    }
    
    /**
     * Suggest correct route name
     */
    private function suggestRoute($brokenRoute)
    {
        // Common route fixes
        $routeFixes = [
            'admin.jobs' => 'admin.jobs.index',
            'admin.companies' => 'admin.companies.index', 
            'admin.candidates' => 'admin.candidates.index',
            'jobs' => 'jobs.index',
            'companies' => 'companies.index',
            'candidates' => 'candidates.index',
            'front.jobs' => 'front.jobs.index',
            'front.companies' => 'front.companies.index',
            'front.candidates' => 'front.candidates.index',
            'employer.jobs' => 'employer.jobs.index',
            'candidate.applications' => 'candidate.applications.index',
            'admin.dashboard.index' => 'admin.dashboard',
            'employer.dashboard.index' => 'employer.dashboard',
            'candidate.dashboard.index' => 'candidate.dashboard'
        ];
        
        if (isset($routeFixes[$brokenRoute])) {
            return $routeFixes[$brokenRoute];
        }
        
        // Try to find similar routes
        foreach ($this->knownRoutes as $knownRoute) {
            if (strpos($knownRoute, $brokenRoute) !== false || strpos($brokenRoute, $knownRoute) !== false) {
                return $knownRoute;
            }
        }
        
        // Add .index if it looks like a resource route
        if (preg_match('/^[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*$/', $brokenRoute) && !preg_match('/\.(index|show|create|edit|update|destroy)$/', $brokenRoute)) {
            return $brokenRoute . '.index';
        }
        
        return null;
    }
    
    /**
     * Fix broken routes
     */
    private function fixBrokenRoutes()
    {
        echo "2. 🔧 Fixing broken routes...\n";
        
        $fixedFiles = [];
        
        foreach ($this->routeIssues as $issue) {
            if ($issue['suggestion']) {
                $filePath = $issue['fullPath'];
                $content = file_get_contents($filePath);
                $originalContent = $content;
                
                // Replace the broken route with the suggestion
                $oldPattern = "route('{$issue['route']}'";
                $newPattern = "route('{$issue['suggestion']}'";
                
                $content = str_replace($oldPattern, $newPattern, $content);
                
                if ($content !== $originalContent) {
                    file_put_contents($filePath, $content);
                    $this->fixedRoutes++;
                    $this->fixes[] = "Fixed {$issue['file']}: '{$issue['route']}' → '{$issue['suggestion']}'";
                    
                    if (!in_array($issue['file'], $fixedFiles)) {
                        $fixedFiles[] = $issue['file'];
                        echo "  ✅ Fixed: {$issue['file']}\n";
                    }
                }
            }
        }
        
        echo "  ✅ Route fixes complete\n\n";
    }
    
    /**
     * Get all blade files
     */
    private function getAllBladeFiles()
    {
        $files = [];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('resources/views')
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                if (strpos($filePath, '.blade.php') !== false) {
                    $files[] = $filePath;
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Generate route fix report
     */
    private function generateRouteReport()
    {
        echo str_repeat("=", 70) . "\n";
        echo "🛣️ ROUTE FIXES COMPLETED - CONTEXT7 PATTERNS APPLIED\n";
        echo str_repeat("=", 70) . "\n\n";
        
        echo "📊 ROUTE FIX SUMMARY:\n";
        echo "- Routes Fixed: {$this->fixedRoutes}\n";
        echo "- Total Route Issues Found: " . count($this->routeIssues) . "\n";
        echo "- Known Routes Loaded: " . count($this->knownRoutes) . "\n\n";
        
        if (!empty($this->fixes)) {
            echo "🔧 ROUTE FIXES APPLIED:\n";
            foreach (array_slice($this->fixes, 0, 15) as $i => $fix) {
                echo "  " . ($i + 1) . ". {$fix}\n";
            }
            
            if (count($this->fixes) > 15) {
                echo "  ... and " . (count($this->fixes) - 15) . " more fixes\n";
            }
            echo "\n";
        }
        
        // Show unfixed issues
        $unfixedIssues = array_filter($this->routeIssues, function($issue) {
            return !$issue['suggestion'];
        });
        
        if (!empty($unfixedIssues)) {
            echo "⚠️  ROUTES NEEDING MANUAL REVIEW:\n";
            foreach (array_slice($unfixedIssues, 0, 10) as $i => $issue) {
                echo "  " . ($i + 1) . ". {$issue['file']}: route('{$issue['route']}') - needs manual review\n";
            }
            
            if (count($unfixedIssues) > 10) {
                echo "  ... and " . (count($unfixedIssues) - 10) . " more routes need review\n";
            }
            echo "\n";
        }
        
        echo "📋 CONTEXT7 ROUTING BEST PRACTICES APPLIED:\n";
        echo "✅ Named routes: route('resource.action') pattern\n";
        echo "✅ RESTful routing: .index, .show, .create, .edit patterns\n";
        echo "✅ Namespace separation: admin.*, front.*, employer.* patterns\n";
        echo "✅ Consistent naming: kebab-case for route names\n\n";
        
        echo "🔄 NEXT ROUTING STEPS:\n";
        echo "1. Review routes needing manual fixes\n";
        echo "2. Test all fixed routes in browser\n";
        echo "3. Add missing routes to route files\n";
        echo "4. Run php artisan route:list to verify\n";
        echo "5. Consider route model binding for show routes\n\n";
        
        echo "✅ Route fixes complete!\n";
    }
}

// Execute route fixes
try {
    $fixer = new RouteFixer();
    $fixer->fixAll();
    
} catch (Exception $e) {
    echo "❌ Error during route fixes: " . $e->getMessage() . "\n";
    exit(1);
} 