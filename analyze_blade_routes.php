<?php

/**
 * Comprehensive Blade Route Analysis Script
 * Scans all blade files for route references and analyzes their functionality
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class BladeRouteAnalyzer
{
    private array $routePatterns = [
        'route(' => '/route\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        'url(' => '/url\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        'action(' => '/action\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        '{{ route(' => '/\{\{\s*route\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        '@route(' => '/@route\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        'href="' => '/href\s*=\s*[\'"]([^\'"]+)[\'"]/',
        'action="' => '/action\s*=\s*[\'"]([^\'"]+)[\'"]/',
        'src="' => '/src\s*=\s*[\'"]([^\'"]+)[\'"]/',
        'to_route(' => '/to_route\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        'redirect()->route(' => '/redirect\s*\(\s*\)\s*->\s*route\s*\(\s*[\'"]([^\'"]+)[\'"]/',
    ];

    private array $findings = [];
    private array $errors = [];
    private array $allRoutes = [];

    public function __construct()
    {
        echo "🚀 BLADE ROUTE ANALYZER - Starting Analysis\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
    }

    public function analyzeAll(): void
    {
        $this->loadApplicationRoutes();
        $this->scanBladeDirectories();
        $this->generateReport();
    }

    private function loadApplicationRoutes(): void
    {
        echo "📊 Loading Application Routes...\n";
        
        try {
            // Define the Laravel application bootstrap path
            $app = require_once __DIR__ . '/bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();
            
            // Get all registered routes
            $routes = Route::getRoutes();
            
            foreach ($routes as $route) {
                $this->allRoutes[] = [
                    'name' => $route->getName(),
                    'uri' => $route->uri(),
                    'methods' => $route->methods(),
                    'action' => $route->getAction(),
                ];
            }
            
            echo "✅ Loaded " . count($this->allRoutes) . " routes\n\n";
        } catch (Exception $e) {
            echo "❌ Error loading routes: " . $e->getMessage() . "\n";
            $this->errors[] = "Failed to load application routes: " . $e->getMessage();
        }
    }

    private function scanBladeDirectories(): void
    {
        $directories = [
            'resources/views/admin' => 'Admin Panel Routes',
            'resources/views/front_web' => 'Frontend Routes',
            'resources/views/candidate' => 'Candidate Portal Routes',
            'resources/views/employer' => 'Employer Portal Routes',
            'resources/views/auth' => 'Authentication Routes',
            'resources/views/layouts' => 'Layout Routes',
            'resources/views/components' => 'Component Routes',
            'resources/views' => 'Root Level Routes',
        ];

        foreach ($directories as $dir => $description) {
            echo "🔍 Scanning: $description ($dir)\n";
            $this->scanDirectory($dir, $description);
            echo "\n";
        }
    }

    private function scanDirectory(string $directory, string $description): void
    {
        if (!is_dir($directory)) {
            echo "⚠️  Directory not found: $directory\n";
            return;
        }

        $bladeFiles = $this->findBladeFiles($directory);
        echo "   Found " . count($bladeFiles) . " blade files\n";

        foreach ($bladeFiles as $file) {
            $this->analyzeBladeFile($file, $description);
        }
    }

    private function findBladeFiles(string $directory): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && 
                str_ends_with($file->getFilename(), '.blade.php')) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function analyzeBladeFile(string $filePath, string $section): void
    {
        $content = file_get_contents($filePath);
        $relativePath = str_replace(getcwd() . '/', '', $filePath);
        
        $fileFindings = [
            'file' => $relativePath,
            'section' => $section,
            'routes_found' => [],
            'urls_found' => [],
            'errors' => [],
            'line_count' => substr_count($content, "\n") + 1,
        ];

        foreach ($this->routePatterns as $patternName => $pattern) {
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $routeReference = $match[1] ?? $match[0];
                    
                    if ($patternName === 'route(' || $patternName === '{{ route(' || $patternName === 'to_route(') {
                        $fileFindings['routes_found'][] = [
                            'pattern' => $patternName,
                            'route_name' => $routeReference,
                            'exists' => $this->routeExists($routeReference),
                            'full_match' => $match[0],
                        ];
                    } else {
                        $fileFindings['urls_found'][] = [
                            'pattern' => $patternName,
                            'url' => $routeReference,
                            'full_match' => $match[0],
                        ];
                    }
                }
            }
        }

        // Check for common errors
        $this->checkForCommonErrors($content, $fileFindings);

        $this->findings[] = $fileFindings;
    }

    private function routeExists(string $routeName): bool
    {
        foreach ($this->allRoutes as $route) {
            if ($route['name'] === $routeName) {
                return true;
            }
        }
        return false;
    }

    private function checkForCommonErrors(string $content, array &$fileFindings): void
    {
        // Check for missing @csrf tokens in forms
        if (preg_match('/<form[^>]*method\s*=\s*[\'"]post[\'"][^>]*>/i', $content)) {
            if (!preg_match('/@csrf|{{\s*csrf_field\s*\(\s*\)\s*}}/', $content)) {
                $fileFindings['errors'][] = 'POST form without @csrf token';
            }
        }

        // Check for hardcoded URLs
        if (preg_match_all('/href\s*=\s*[\'"]\/[^\'"]*/i', $content, $matches)) {
            foreach ($matches[0] as $match) {
                if (!preg_match('/\{\{|\@|route\(|url\(/', $match)) {
                    $fileFindings['errors'][] = "Hardcoded URL found: $match";
                }
            }
        }

        // Check for Bootstrap classes (for migration tracking)
        if (preg_match('/class\s*=\s*[\'"][^\'"]*(btn-|container-|row|col-|form-control|nav-|card-|alert-)/i', $content)) {
            $fileFindings['errors'][] = 'Bootstrap classes found (needs TailwindCSS migration)';
        }

        // Check for inline styles
        if (preg_match('/style\s*=\s*[\'"][^\'"]+[\'"]/', $content)) {
            $fileFindings['errors'][] = 'Inline styles found (move to CSS files)';
        }

        // Check for inline scripts
        if (preg_match('/<script[^>]*>/', $content)) {
            $fileFindings['errors'][] = 'Inline scripts found (move to JS files)';
        }
    }

    private function generateReport(): void
    {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "📋 COMPREHENSIVE BLADE ROUTE ANALYSIS REPORT\n";
        echo str_repeat("=", 70) . "\n\n";

        $this->generateSummary();
        $this->generateErrorReport();
        $this->generateRouteIssues();
        $this->generateMigrationReport();
        $this->generateActionItems();
        
        // Save detailed report to file
        $this->saveDetailedReport();
    }

    private function generateSummary(): void
    {
        $totalFiles = count($this->findings);
        $totalRoutes = 0;
        $totalUrls = 0;
        $totalErrors = 0;
        
        foreach ($this->findings as $finding) {
            $totalRoutes += count($finding['routes_found']);
            $totalUrls += count($finding['urls_found']);
            $totalErrors += count($finding['errors']);
        }

        echo "📊 SUMMARY:\n";
        echo "   Files Analyzed: $totalFiles\n";
        echo "   Route References: $totalRoutes\n";
        echo "   URL References: $totalUrls\n";
        echo "   Issues Found: $totalErrors\n";
        echo "   Application Routes: " . count($this->allRoutes) . "\n\n";
    }

    private function generateErrorReport(): void
    {
        echo "🚨 CRITICAL ERRORS:\n";
        
        $criticalErrors = [];
        foreach ($this->findings as $finding) {
            foreach ($finding['routes_found'] as $route) {
                if (!$route['exists']) {
                    $criticalErrors[] = [
                        'file' => $finding['file'],
                        'route' => $route['route_name'],
                        'pattern' => $route['pattern'],
                    ];
                }
            }
        }

        if (empty($criticalErrors)) {
            echo "   ✅ No broken route references found!\n\n";
        } else {
            echo "   Found " . count($criticalErrors) . " broken route references:\n";
            foreach ($criticalErrors as $error) {
                echo "   ❌ {$error['file']}: route('{$error['route']}') - ROUTE NOT FOUND\n";
            }
            echo "\n";
        }
    }

    private function generateRouteIssues(): void
    {
        echo "🔍 ROUTE ANALYSIS BY SECTION:\n";
        
        $sections = [];
        foreach ($this->findings as $finding) {
            $section = $finding['section'];
            if (!isset($sections[$section])) {
                $sections[$section] = [
                    'files' => 0,
                    'routes' => 0,
                    'errors' => 0,
                    'issues' => [],
                ];
            }
            
            $sections[$section]['files']++;
            $sections[$section]['routes'] += count($finding['routes_found']);
            $sections[$section]['errors'] += count($finding['errors']);
            
            foreach ($finding['errors'] as $error) {
                $sections[$section]['issues'][] = $finding['file'] . ': ' . $error;
            }
        }

        foreach ($sections as $sectionName => $data) {
            echo "   📁 $sectionName:\n";
            echo "      Files: {$data['files']}, Routes: {$data['routes']}, Issues: {$data['errors']}\n";
            
            if (!empty($data['issues'])) {
                foreach (array_slice($data['issues'], 0, 3) as $issue) {
                    echo "      ⚠️  $issue\n";
                }
                if (count($data['issues']) > 3) {
                    echo "      ... and " . (count($data['issues']) - 3) . " more issues\n";
                }
            }
            echo "\n";
        }
    }

    private function generateMigrationReport(): void
    {
        echo "🔄 MIGRATION STATUS:\n";
        
        $bootstrapFiles = [];
        $inlineStyleFiles = [];
        $inlineScriptFiles = [];
        
        foreach ($this->findings as $finding) {
            foreach ($finding['errors'] as $error) {
                if (strpos($error, 'Bootstrap') !== false) {
                    $bootstrapFiles[] = $finding['file'];
                }
                if (strpos($error, 'Inline styles') !== false) {
                    $inlineStyleFiles[] = $finding['file'];
                }
                if (strpos($error, 'Inline scripts') !== false) {
                    $inlineScriptFiles[] = $finding['file'];
                }
            }
        }

        echo "   🎨 Bootstrap Migration: " . count($bootstrapFiles) . " files need TailwindCSS conversion\n";
        echo "   📝 Inline Styles: " . count($inlineStyleFiles) . " files need CSS extraction\n";
        echo "   ⚙️  Inline Scripts: " . count($inlineScriptFiles) . " files need JS extraction\n\n";
    }

    private function generateActionItems(): void
    {
        echo "📋 IMMEDIATE ACTION ITEMS:\n";
        echo "   1. 🔥 URGENT: Fix broken route references\n";
        echo "   2. 🔒 SECURITY: Add @csrf tokens to POST forms\n";
        echo "   3. 🎨 MIGRATION: Convert Bootstrap to TailwindCSS\n";
        echo "   4. 📁 CLEANUP: Move inline styles/scripts to separate files\n";
        echo "   5. 🔗 OPTIMIZATION: Replace hardcoded URLs with route helpers\n\n";
    }

    private function saveDetailedReport(): void
    {
        $reportData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'summary' => [
                'total_files' => count($this->findings),
                'total_routes' => array_sum(array_map(fn($f) => count($f['routes_found']), $this->findings)),
                'total_errors' => array_sum(array_map(fn($f) => count($f['errors']), $this->findings)),
            ],
            'findings' => $this->findings,
            'all_routes' => $this->allRoutes,
            'errors' => $this->errors,
        ];

        file_put_contents('blade_route_analysis_report.json', json_encode($reportData, JSON_PRETTY_PRINT));
        echo "💾 Detailed report saved to: blade_route_analysis_report.json\n\n";
    }
}

// Run the analysis
try {
    $analyzer = new BladeRouteAnalyzer();
    $analyzer->analyzeAll();
    
    echo "🎉 Analysis completed successfully!\n";
    echo "   Next steps: Review the report and prioritize fixes\n";
    echo "   Run: php analyze_blade_routes.php\n\n";
    
} catch (Exception $e) {
    echo "❌ Analysis failed: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

?> 