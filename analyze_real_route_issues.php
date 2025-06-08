<?php

/**
 * Real Route Issues Analyzer - Universal
 * Focuses on actual route problems, filtering out JavaScript/hash placeholders
 */

require_once __DIR__ . '/vendor/autoload.php';

class RealRouteIssuesAnalyzer
{
    private $bladeFiles = [];
    private $realRouteIssues = [];
    private $syntaxErrors = [];
    private $allRoutes = [];
    private $totalAnalyzed = 0;
    private $realIssueCount = 0;

    public function __construct()
    {
        echo "🎯 REAL ROUTE ISSUES ANALYZER - Universal\n";
        echo "=" . str_repeat("=", 50) . "\n";
        echo "🔍 Filtering out JavaScript/hash placeholders\n";
        echo "🎯 Focusing on actual Laravel route issues\n\n";
        
        $this->initializeLaravel();
        $this->loadAllRoutes();
    }

    private function initializeLaravel()
    {
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }

    private function loadAllRoutes()
    {
        echo "📊 Loading registered Laravel routes...\n";
        
        $routes = app('router')->getRoutes();
        foreach ($routes as $route) {
            $this->allRoutes[] = [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'action' => $route->getActionName(),
            ];
        }
        
        echo "✅ Loaded " . count($this->allRoutes) . " registered routes\n\n";
    }

    public function analyzeRealIssues()
    {
        $this->findBladeFiles();
        $this->analyzeRouteReferences();
        $this->analyzeSyntaxErrors();
        $this->generateReport();
        $this->createFixScript();
    }

    private function findBladeFiles()
    {
        echo "📁 Scanning blade files...\n";
        
        $viewsPath = __DIR__ . '/resources/views';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && 
                str_contains($file->getFilename(), '.blade.')) {
                $this->bladeFiles[] = $file->getPathname();
            }
        }

        $this->totalAnalyzed = count($this->bladeFiles);
        echo "✅ Found {$this->totalAnalyzed} blade files\n\n";
    }

    private function analyzeRouteReferences()
    {
        echo "🔗 Analyzing route references for real issues...\n";
        
        $realRoutePatterns = [
            '/route\s*\(\s*[\'"]([^\'"\s]+)[\'"]\s*[,)]/' => 'named_route',
            '/to_route\s*\(\s*[\'"]([^\'"\s]+)[\'"]\s*[,)]/' => 'redirect_route',
            '/action\s*=\s*[\'"]{{[^}]*route\s*\(\s*[\'"]([^\'"\s]+)[\'"]\s*[^}]*}}[\'"]/' => 'form_action_route',
        ];

        foreach ($this->bladeFiles as $file) {
            $content = file_get_contents($file);
            $relativePath = str_replace(__DIR__ . '/resources/views/', '', $file);
            
            foreach ($realRoutePatterns as $pattern => $type) {
                if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    foreach ($matches[1] as $match) {
                        $routeName = $match[0];
                        
                        // Skip obvious placeholders
                        if ($this->isPlaceholder($routeName)) {
                            continue;
                        }
                        
                        if (!$this->routeExists($routeName)) {
                            $line = $this->getLineNumber($content, $match[1]);
                            $this->realRouteIssues[] = [
                                'route' => $routeName,
                                'file' => $relativePath,
                                'line' => $line,
                                'type' => $type,
                                'context' => $this->getContext($content, $match[1])
                            ];
                            $this->realIssueCount++;
                        }
                    }
                }
            }
        }

        echo "❌ Found {$this->realIssueCount} real route issues\n\n";
    }

    private function isPlaceholder($route)
    {
        $placeholders = [
            'javascript:void(0)',
            '#',
            'void(0)',
            'javascript:',
            '',
            'http',
            'https',
            '{{',
            '{{'
        ];
        
        foreach ($placeholders as $placeholder) {
            if (str_contains($route, $placeholder)) {
                return true;
            }
        }
        
        return false;
    }

    private function routeExists($routeName)
    {
        foreach ($this->allRoutes as $route) {
            if ($route['name'] === $routeName) {
                return true;
            }
        }
        return false;
    }

    private function getLineNumber($content, $offset)
    {
        return substr_count(substr($content, 0, $offset), "\n") + 1;
    }

    private function getContext($content, $offset)
    {
        $lines = explode("\n", $content);
        $lineNumber = $this->getLineNumber($content, $offset) - 1;
        
        $context = [];
        for ($i = max(0, $lineNumber - 1); $i <= min(count($lines) - 1, $lineNumber + 1); $i++) {
            $context[] = ($i + 1) . ': ' . trim($lines[$i]);
        }
        
        return implode("\n", $context);
    }

    private function analyzeSyntaxErrors()
    {
        echo "⚠️  Analyzing critical syntax errors...\n";
        
        $criticalPatterns = [
            '/\@[a-zA-Z]+\([^\'\"]*[\'\"[^\'\"]*\)/' => 'Unmatched quotes in Blade directive',
            '/route\s*\([\'"][^\'\"]*[\'\"]\s*[^)]/' => 'Malformed route() call',
            '/\{\{\s*route\s*\([^}]*$/' => 'Unclosed route() in Blade output',
            '/\@if\s*\(\s*[^$][^)]*\)/' => 'Missing $ in @if condition',
            '/\@foreach\s*\([^$][^)]*\)/' => 'Missing $ in @foreach',
        ];

        foreach ($this->bladeFiles as $file) {
            $content = file_get_contents($file);
            $relativePath = str_replace(__DIR__ . '/resources/views/', '', $file);
            
            foreach ($criticalPatterns as $pattern => $description) {
                if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    foreach ($matches[0] as $match) {
                        $line = $this->getLineNumber($content, $match[1]);
                        $this->syntaxErrors[] = [
                            'file' => $relativePath,
                            'line' => $line,
                            'error' => $description,
                            'code' => trim($match[0]),
                            'context' => $this->getContext($content, $match[1])
                        ];
                    }
                }
            }
        }

        echo "⚠️  Found " . count($this->syntaxErrors) . " critical syntax errors\n\n";
    }

    private function generateReport()
    {
        echo "📊 COMPREHENSIVE ANALYSIS RESULTS\n";
        echo "=" . str_repeat("=", 50) . "\n\n";

        echo "📁 Total Blade Files Analyzed: {$this->totalAnalyzed}\n";
        echo "🔗 Registered Laravel Routes: " . count($this->allRoutes) . "\n";
        echo "❌ Real Route Issues Found: {$this->realIssueCount}\n";
        echo "⚠️  Critical Syntax Errors: " . count($this->syntaxErrors) . "\n\n";

        if (!empty($this->realRouteIssues)) {
            echo "🚨 REAL ROUTE ISSUES (Top 20):\n";
            echo str_repeat("-", 50) . "\n";
            
            $groupedIssues = [];
            foreach ($this->realRouteIssues as $issue) {
                $groupedIssues[$issue['route']][] = $issue;
            }
            
            $count = 0;
            foreach ($groupedIssues as $routeName => $issues) {
                if ($count >= 20) break;
                echo "• Route: '{$routeName}' (Used in " . count($issues) . " file(s))\n";
                foreach (array_slice($issues, 0, 3) as $issue) {
                    echo "  - {$issue['file']}:{$issue['line']}\n";
                }
                if (count($issues) > 3) {
                    echo "  - ... and " . (count($issues) - 3) . " more\n";
                }
                echo "\n";
                $count++;
            }
        }

        if (!empty($this->syntaxErrors)) {
            echo "⚠️  CRITICAL SYNTAX ERRORS (Top 10):\n";
            echo str_repeat("-", 50) . "\n";
            
            foreach (array_slice($this->syntaxErrors, 0, 10) as $error) {
                echo "• {$error['file']}:{$error['line']}\n";
                echo "  Error: {$error['error']}\n";
                echo "  Code: {$error['code']}\n\n";
            }
        }

        // Save detailed report
        $detailedReport = [
            'timestamp' => date('Y-m-d H:i:s'),
            'summary' => [
                'total_files' => $this->totalAnalyzed,
                'total_routes' => count($this->allRoutes),
                'real_route_issues' => $this->realIssueCount,
                'syntax_errors' => count($this->syntaxErrors)
            ],
            'real_route_issues' => $this->realRouteIssues,
            'syntax_errors' => $this->syntaxErrors,
            'all_routes' => array_map(fn($r) => $r['name'], array_filter($this->allRoutes, fn($r) => $r['name']))
        ];

        file_put_contents('real_route_issues_report.json', json_encode($detailedReport, JSON_PRETTY_PRINT));
        echo "💾 Detailed report saved: real_route_issues_report.json\n\n";
    }

    private function createFixScript()
    {
        if (empty($this->realRouteIssues) && empty($this->syntaxErrors)) {
            echo "✅ No fixes needed - all routes and syntax are correct!\n";
            return;
        }

        echo "🔧 Creating fix script...\n";
        
        $fixScript = "<?php\n\n/**\n * Auto-generated fix script for real route issues\n */\n\n";
        $fixScript .= "echo \"🔧 FIXING REAL ROUTE ISSUES\\n\";\n\n";

        // Group missing routes
        $missingRoutes = [];
        foreach ($this->realRouteIssues as $issue) {
            $missingRoutes[] = $issue['route'];
        }
        $missingRoutes = array_unique($missingRoutes);

        $fixScript .= "// Missing routes to create:\n";
        foreach ($missingRoutes as $route) {
            $fixScript .= "// - {$route}\n";
        }

        $fixScript .= "\necho \"✅ Review and implement the routes listed above\\n\";\n";
        $fixScript .= "echo \"📝 Check real_route_issues_report.json for details\\n\";\n";

        file_put_contents('fix_real_route_issues.php', $fixScript);
        echo "💾 Fix script created: fix_real_route_issues.php\n";
    }
}

// Run the analysis
try {
    $analyzer = new RealRouteIssuesAnalyzer();
    $analyzer->analyzeRealIssues();
    
    echo "🎉 Real route issues analysis completed!\n";
    echo "📝 Next: Review real_route_issues_report.json for actionable fixes\n\n";
    
} catch (Exception $e) {
    echo "❌ Analysis failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 