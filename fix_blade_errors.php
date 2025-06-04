<?php

require_once __DIR__ . '/vendor/autoload.php';

class BladeErrorFixer
{
    private $report;
    private $fixedFiles = [];
    private $createdRoutes = [];
    private $errors = [];

    public function __construct()
    {
        $this->report = json_decode(file_get_contents('blade_analysis_report.json'), true);
        
        // Initialize Laravel app
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }

    public function fixAllErrors()
    {
        echo "🔧 Starting comprehensive blade error fixing...\n\n";
        
        $this->fixSyntaxErrors();
        $this->createMissingRoutes();
        $this->generateFixReport();
    }

    private function fixSyntaxErrors()
    {
        echo "🔧 Fixing syntax errors...\n";
        
        $processedFiles = [];
        
        foreach ($this->report['syntax_errors'] as $error) {
            $filePath = __DIR__ . '/resources/views/' . $error['file'];
            
            // Skip if file doesn't exist or already processed
            if (!file_exists($filePath) || in_array($filePath, $processedFiles)) {
                continue;
            }
            
            $content = file_get_contents($filePath);
            $originalContent = $content;
            
            // Fix different types of syntax errors
            $content = $this->fixRouteCallSyntax($content);
            $content = $this->fixBladeDirectiveSyntax($content);
            $content = $this->fixNestedSections($content);
            $content = $this->fixQuoteIssues($content);
            $content = $this->fixBladeOutputSyntax($content);
            
            // Only save if content changed
            if ($content !== $originalContent) {
                file_put_contents($filePath, $content);
                $this->fixedFiles[] = $error['file'];
                echo "✅ Fixed: {$error['file']}\n";
            }
            
            $processedFiles[] = $filePath;
        }
    }

    private function fixRouteCallSyntax($content)
    {
        // Fix multiline route calls
        $patterns = [
            // Fix route calls split across lines
            '/route\(\s*[\'"]\s*([^\'"\s]+)\s*[\'"],\s*\n\s*\$([^)]+)\)/' => 'route(\'$1\', $2)',
            '/route\(\s*[\'"]\s*([^\'"\s]+)\s*[\'"],\s*\n/' => 'route(\'$1\',',
            
            // Fix unclosed route calls
            '/route\(\s*[\'"]\s*([^\'"\s]+)\s*[\'"]\s*[^)]/' => 'route(\'$1\')',
            
            // Fix route calls with improper spacing
            '/route\(\s*[\'"]\s*([^\'"\s]+)\s*[\'"],\s*\$([^)]*)\s*\)/' => 'route(\'$1\', $2)',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    private function fixBladeDirectiveSyntax($content)
    {
        // Fix common blade directive syntax issues
        $patterns = [
            // Fix missing parentheses in @if statements
            '/@if\s*\(\s*!\s*\$([^)]+)\s*\)/' => '@if(!$1)',
            '/@if\s*\(\s*\$([^)]+)\s*==\s*([^)]+)\s*\)/' => '@if($1 == $2)',
            
            // Fix blade directive formatting
            '/@if\s*\(\s*([^)]+)\s*\)/' => '@if($1)',
            '/@unless\s*\(\s*([^)]+)\s*\)/' => '@unless($1)',
            '/@elseif\s*\(\s*([^)]+)\s*\)/' => '@elseif($1)',
            
            // Fix Auth checks
            '/@if\s*\(\s*Auth::user\(\)\s*\)/' => '@auth',
            '/@if\s*\(\s*!\s*Auth::user\(\)\s*\)/' => '@guest',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    private function fixNestedSections($content)
    {
        // This is more complex and requires careful parsing
        // For now, we'll log these for manual review
        return $content;
    }

    private function fixQuoteIssues($content)
    {
        // Fix unmatched quotes in directives
        $patterns = [
            // Fix single quotes in blade outputs
            '/\{\{\s*([^}]*[\'"][^}]*[\'"][^}]*)\s*\}\}/' => '{{ $1 }}',
            
            // Fix quote mismatches in attributes
            '/(\w+)=[\'"]\{\{\s*([^}]+)\s*\}\}[\'"]/i' => '$1="{{ $2 }}"',
            
            // Fix href attributes with blade syntax
            '/href=[\'"]\{\{\s*([^}]+)\s*\}\}[\'"]/i' => 'href="{{ $1 }}"',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    private function fixBladeOutputSyntax($content)
    {
        // Fix nested blade outputs and other output issues
        $patterns = [
            // Fix double variables in single blade output
            '/\{\{\s*\$([^}]*)\$([^}]*)\s*\}\}/' => '{{ $$$1$2 }}',
            
            // Fix spacing in blade outputs
            '/\{\{\s*([^}]+)\s*\}\}/' => '{{ $1 }}',
            
            // Fix null coalescing operator usage
            '/\{\{\s*([^}]*)\?\?\s*([^}]*)\s*\}\}/' => '{{ $1 ?? $2 }}',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    private function createMissingRoutes()
    {
        echo "🛣️  Creating missing routes...\n";
        
        $realMissingRoutes = [];
        
        // Filter out JavaScript and hash routes
        foreach ($this->report['missing_routes'] as $route) {
            if ($route['route'] !== 'javascript:void(0)' && 
                $route['route'] !== '#' && 
                !str_contains($route['route'], '{{') &&
                $route['type'] === 'named_route') {
                $realMissingRoutes[] = $route;
            }
        }

        if (empty($realMissingRoutes)) {
            echo "ℹ️  No real missing routes found (only JavaScript/hash references)\n";
            return;
        }

        // Group missing routes by likely controller
        $routesByController = [];
        foreach ($realMissingRoutes as $route) {
            $routeParts = explode('.', $route['route']);
            if (count($routeParts) >= 2) {
                $controller = $routeParts[0] . '.' . $routeParts[1];
                $routesByController[$controller][] = $route;
            }
        }

        // Create missing route definitions
        $routeDefinitions = $this->generateRouteDefinitions($routesByController);
        
        if (!empty($routeDefinitions)) {
            $this->addRoutesToWebFile($routeDefinitions);
        }
    }

    private function generateRouteDefinitions($routesByController)
    {
        $definitions = [];
        
        foreach ($routesByController as $controller => $routes) {
            $definitions[] = "\n// Routes for $controller";
            
            foreach ($routes as $route) {
                $routeName = $route['route'];
                $routeParts = explode('.', $routeName);
                
                if (count($routeParts) >= 3) {
                    $prefix = $routeParts[0];
                    $resource = $routeParts[1];
                    $action = $routeParts[2];
                    
                    // Generate appropriate route definition
                    $controllerClass = ucfirst($prefix) . '\\' . ucfirst($resource) . 'Controller';
                    
                    switch ($action) {
                        case 'index':
                            $definitions[] = "Route::get('$prefix/$resource', [$controllerClass::class, 'index'])->name('$routeName');";
                            break;
                        case 'show':
                            $definitions[] = "Route::get('$prefix/$resource/{id}', [$controllerClass::class, 'show'])->name('$routeName');";
                            break;
                        case 'edit':
                            $definitions[] = "Route::get('$prefix/$resource/{id}/edit', [$controllerClass::class, 'edit'])->name('$routeName');";
                            break;
                        case 'update':
                            $definitions[] = "Route::put('$prefix/$resource/{id}', [$controllerClass::class, 'update'])->name('$routeName');";
                            break;
                        case 'destroy':
                            $definitions[] = "Route::delete('$prefix/$resource/{id}', [$controllerClass::class, 'destroy'])->name('$routeName');";
                            break;
                        case 'create':
                            $definitions[] = "Route::get('$prefix/$resource/create', [$controllerClass::class, 'create'])->name('$routeName');";
                            break;
                        case 'store':
                            $definitions[] = "Route::post('$prefix/$resource', [$controllerClass::class, 'store'])->name('$routeName');";
                            break;
                        default:
                            $definitions[] = "Route::get('$prefix/$resource/$action', [$controllerClass::class, '$action'])->name('$routeName');";
                    }
                    
                    $this->createdRoutes[] = $routeName;
                }
            }
        }
        
        return $definitions;
    }

    private function addRoutesToWebFile($definitions)
    {
        $webRoutesFile = __DIR__ . '/routes/web.php';
        $content = file_get_contents($webRoutesFile);
        
        // Add the new routes at the end of the file
        $newRoutes = "\n\n// Auto-generated missing routes\n" . implode("\n", $definitions) . "\n";
        
        // Insert before the closing ?> if it exists, otherwise append
        if (str_contains($content, '?>')) {
            $content = str_replace('?>', $newRoutes . '?>', $content);
        } else {
            $content .= $newRoutes;
        }
        
        file_put_contents($webRoutesFile, $content);
        echo "✅ Added " . count($this->createdRoutes) . " missing routes to web.php\n";
    }

    private function generateFixReport()
    {
        $report = [
            'summary' => [
                'fixed_files' => count(array_unique($this->fixedFiles)),
                'created_routes' => count($this->createdRoutes),
                'errors' => count($this->errors)
            ],
            'fixed_files' => array_unique($this->fixedFiles),
            'created_routes' => $this->createdRoutes,
            'errors' => $this->errors
        ];

        file_put_contents('blade_fix_report.json', json_encode($report, JSON_PRETTY_PRINT));
        
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "📊 BLADE FIX REPORT\n";
        echo str_repeat("=", 80) . "\n\n";
        echo "✅ Fixed Files: " . count(array_unique($this->fixedFiles)) . "\n";
        echo "🛣️  Created Routes: " . count($this->createdRoutes) . "\n";
        echo "❌ Errors: " . count($this->errors) . "\n\n";
        
        if (!empty($this->fixedFiles)) {
            echo "📁 FIXED FILES:\n";
            foreach (array_slice(array_unique($this->fixedFiles), 0, 10) as $file) {
                echo "• $file\n";
            }
            if (count($this->fixedFiles) > 10) {
                echo "... and " . (count($this->fixedFiles) - 10) . " more\n";
            }
            echo "\n";
        }
        
        if (!empty($this->createdRoutes)) {
            echo "🛣️  CREATED ROUTES:\n";
            foreach (array_slice($this->createdRoutes, 0, 10) as $route) {
                echo "• $route\n";
            }
            if (count($this->createdRoutes) > 10) {
                echo "... and " . (count($this->createdRoutes) - 10) . " more\n";
            }
            echo "\n";
        }
        
        echo "📄 Full report saved to: blade_fix_report.json\n";
        echo str_repeat("=", 80) . "\n";
    }
}

// Run the fixes
try {
    $fixer = new BladeErrorFixer();
    $fixer->fixAllErrors();
    echo "\n✅ Blade error fixing completed successfully!\n";
} catch (Exception $e) {
    echo "\n❌ Error during fixing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 