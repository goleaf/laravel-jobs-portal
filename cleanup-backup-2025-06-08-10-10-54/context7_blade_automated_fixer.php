<?php

/**
 * Context7 Automated Blade Fixer System
 * Laravel 12 Job Portal - Systematic Issue Resolution
 * 
 * Fixes identified issues:
 * - Critical syntax errors (prevents compilation)
 * - Incomplete route calls (route('admin.') → proper routes)
 * - XSS vulnerabilities ({!! !!} → {{ }} where safe)
 * - Bootstrap to TailwindCSS migration
 */

require_once __DIR__ . '/vendor/autoload.php';

class Context7BladeFixer
{
    private $reportData = [];
    private $fixedFiles = [];
    private $fixStats = [
        'syntax_fixes' => 0,
        'route_fixes' => 0,
        'xss_fixes' => 0,
        'bootstrap_fixes' => 0,
        'total_files_fixed' => 0
    ];

    public function __construct()
    {
        echo "🔧 Context7 Automated Blade Fixer Starting...\n";
        echo "🛠️ Laravel 12 Job Portal - Systematic Issue Resolution\n\n";
        $this->loadAnalysisReport();
    }

    /**
     * Load the analysis report data
     */
    private function loadAnalysisReport()
    {
        $reportFile = 'context7_blade_analysis_report.json';
        if (!file_exists($reportFile)) {
            echo "❌ Analysis report not found. Please run analysis first.\n";
            exit(1);
        }

        $this->reportData = json_decode(file_get_contents($reportFile), true);
        echo "📋 Loaded analysis report with " . count($this->reportData['issues']) . " files to fix\n\n";
    }

    /**
     * Context7 Pattern: Systematic Issue Resolution
     */
    public function fixAllIssues()
    {
        echo "🚀 Starting Context7 automated fixes...\n\n";

        // Phase 1: Critical Syntax Errors (prevents compilation)
        $this->fixSyntaxErrors();

        // Phase 2: Route Issues (improves functionality)
        $this->fixRouteIssues();

        // Phase 3: XSS Security Issues (security critical)
        $this->fixXSSIssues();

        // Phase 4: Bootstrap Migration (CSS framework migration)
        $this->fixBootstrapUsage();

        $this->generateFixReport();
    }

    /**
     * Context7 Pattern: Critical Syntax Error Resolution
     */
    private function fixSyntaxErrors()
    {
        echo "🔧 Phase 1: Fixing Critical Syntax Errors...\n";
        
        foreach ($this->reportData['issues'] as $filePath => $issues) {
            if (isset($issues['syntax'])) {
                $this->fixFileSyntaxErrors($filePath, $issues['syntax']);
            }
        }
        
        echo "✅ Phase 1 Complete: Fixed " . $this->fixStats['syntax_fixes'] . " syntax errors\n\n";
    }

    /**
     * Fix syntax errors in a specific file
     */
    private function fixFileSyntaxErrors($filePath, $syntaxIssues)
    {
        if (!file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fixed = false;

        foreach ($syntaxIssues as $issue) {
            if (strpos($issue, 'Nested echo syntax') !== false) {
                // Fix nested echo syntax: {{ {{ }} → {{ ... }}
                $content = preg_replace('/\{\{([^}]*)\{\{([^}]*)\}\}([^}]*)\}\}/', '{{ $1$2$3 }}', $content);
                $fixed = true;
            }

            if (strpos($issue, 'Double dollar sign error') !== false) {
                // Fix double dollar signs: $$variable → $variable
                $content = preg_replace('/\$\$([a-zA-Z_][a-zA-Z0-9_]*)/', '$$$1', $content);
                $fixed = true;
            }

            if (strpos($issue, 'Unclosed Blade comment') !== false) {
                // Fix unclosed blade comments
                $content = preg_replace('/\{\{--\s*([^}]*?)(?:--\}\})?$/', '{{-- $1 --}}', $content);
                $fixed = true;
            }
        }

        if ($fixed && $content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->fixStats['syntax_fixes']++;
            $this->fixedFiles[] = $filePath;
            echo "  ✓ Fixed syntax in: " . basename($filePath) . "\n";
        }
    }

    /**
     * Context7 Pattern: Route Issue Resolution
     */
    private function fixRouteIssues()
    {
        echo "🔗 Phase 2: Fixing Route Issues...\n";
        
        foreach ($this->reportData['issues'] as $filePath => $issues) {
            if (isset($issues['routes'])) {
                $this->fixFileRouteIssues($filePath, $issues['routes']);
            }
        }
        
        echo "✅ Phase 2 Complete: Fixed " . $this->fixStats['route_fixes'] . " route issues\n\n";
    }

    /**
     * Fix route issues in a specific file
     */
    private function fixFileRouteIssues($filePath, $routeIssues)
    {
        if (!file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fixed = false;

        // Context7 Route Mappings based on Laravel 12 patterns
        $routeMappings = [
            // Admin routes
            "route('admin.')" => "route('admin.dashboard')",
            "route('admin.candidates.')" => "route('admin.candidates.index')",
            "route('admin.jobs.')" => "route('admin.jobs.index')",
            "route('admin.companies.')" => "route('companies.index')",
            
            // Candidate routes
            "route('candidate.')" => "route('candidate.dashboard')",
            "route('candidate.profile.')" => "route('candidate.profile')",
            "route('candidate.applications.')" => "route('candidate.applications.index')",
            
            // Employer routes
            "route('employer.')" => "route('employer.dashboard')",
            "route('employer.jobs.')" => "route('employer.jobs.index')",
            "route('employer.company.')" => "route('employer.company')",
            
            // Notification routes
            "route('notification.')" => "route('jobnotification.index')",
            
            // Other common incomplete routes
            "route('front.')" => "route('front.home')",
            "route('api.')" => "route('api.jobs.index')",
        ];

        foreach ($routeIssues as $issue) {
            if (strpos($issue, 'Incomplete route:') !== false) {
                preg_match("/route\('([^']*\.?)'\)/", $issue, $matches);
                if (isset($matches[1])) {
                    $incompleteRoute = "route('" . $matches[1] . "')";
                    
                    if (isset($routeMappings[$incompleteRoute])) {
                        $content = str_replace($incompleteRoute, $routeMappings[$incompleteRoute], $content);
                        $fixed = true;
                    }
                }
            }
        }

        if ($fixed && $content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->fixStats['route_fixes']++;
            if (!in_array($filePath, $this->fixedFiles)) {
                $this->fixedFiles[] = $filePath;
            }
            echo "  ✓ Fixed routes in: " . basename($filePath) . "\n";
        }
    }

    /**
     * Context7 Pattern: XSS Security Issue Resolution
     */
    private function fixXSSIssues()
    {
        echo "🛡️ Phase 3: Fixing XSS Security Issues...\n";
        
        foreach ($this->reportData['issues'] as $filePath => $issues) {
            if (isset($issues['xss'])) {
                $this->fixFileXSSIssues($filePath, $issues['xss']);
            }
        }
        
        echo "✅ Phase 3 Complete: Fixed " . $this->fixStats['xss_fixes'] . " XSS issues\n\n";
    }

    /**
     * Fix XSS issues in a specific file
     */
    private function fixFileXSSIssues($filePath, $xssIssues)
    {
        if (!file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fixed = false;

        foreach ($xssIssues as $issue) {
            // Only fix obviously safe conversions
            if (strpos($issue, 'nl2br(e(') !== false) {
                // {!! nl2br(e($variable)) !!} → {{ nl2br(e($variable)) }}
                $content = preg_replace('/\{!!\s*nl2br\(e\(([^)]+)\)\)([^}]*)\s*!!\}/', '{{ nl2br(e($1))$2 }}', $content);
                $fixed = true;
            }

            // Fix other safe patterns
            if (preg_match('/\{!!\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*!!\}/', $issue)) {
                // Only convert simple variables, leave complex expressions
                $content = preg_replace('/\{!!\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*!!\}/', '{{ $$1 }}', $content);
                $fixed = true;
            }
        }

        if ($fixed && $content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->fixStats['xss_fixes']++;
            if (!in_array($filePath, $this->fixedFiles)) {
                $this->fixedFiles[] = $filePath;
            }
            echo "  ✓ Fixed XSS in: " . basename($filePath) . "\n";
        }
    }

    /**
     * Context7 Pattern: Bootstrap to TailwindCSS Migration
     */
    private function fixBootstrapUsage()
    {
        echo "🎨 Phase 4: Migrating Bootstrap to TailwindCSS...\n";
        
        foreach ($this->reportData['issues'] as $filePath => $issues) {
            if (isset($issues['bootstrap'])) {
                $this->fixFileBootstrapUsage($filePath, $issues['bootstrap']);
            }
        }
        
        echo "✅ Phase 4 Complete: Migrated " . $this->fixStats['bootstrap_fixes'] . " Bootstrap usages\n\n";
    }

    /**
     * Fix Bootstrap usage in a specific file
     */
    private function fixFileBootstrapUsage($filePath, $bootstrapIssues)
    {
        if (!file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fixed = false;

        // Context7 Bootstrap → TailwindCSS mappings
        $bootstrapMappings = [
            // Buttons
            'btn btn-primary' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500',
            'btn btn-secondary' => 'rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500',
            'btn btn-success' => 'rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500',
            'btn btn-danger' => 'rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500',
            'btn' => 'rounded-md px-4 py-2 text-sm font-semibold focus:outline-none',
            
            // Forms
            'form-control' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
            'form-group' => 'mb-4',
            'form-label' => 'block text-sm font-medium text-gray-700 mb-1',
            'form-check' => 'flex items-center',
            
            // Layout
            'container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
            'container-fluid' => 'w-full px-4',
            'row' => 'flex flex-wrap -mx-4',
            'col-md-6' => 'w-full md:w-1/2 px-4',
            'col-md-4' => 'w-full md:w-1/3 px-4',
            'col-md-3' => 'w-full md:w-1/4 px-4',
            'col-12' => 'w-full px-4',
            
            // Cards
            'card' => 'bg-white shadow rounded-lg',
            'card-body' => 'p-6',
            'card-header' => 'px-6 py-4 border-b border-gray-200',
            'card-footer' => 'px-6 py-4 border-t border-gray-200',
            
            // Tables
            'table' => 'min-w-full divide-y divide-gray-200',
            'table-striped' => 'min-w-full divide-y divide-gray-200 odd:bg-gray-50',
            'table-bordered' => 'min-w-full divide-y divide-gray-200 border border-gray-300',
            
            // Alerts
            'alert alert-success' => 'rounded-md bg-green-50 p-4 border border-green-200 text-green-800',
            'alert alert-danger' => 'rounded-md bg-red-50 p-4 border border-red-200 text-red-800',
            'alert alert-warning' => 'rounded-md bg-yellow-50 p-4 border border-yellow-200 text-yellow-800',
            'alert' => 'rounded-md p-4 border',
        ];

        foreach ($bootstrapMappings as $bootstrap => $tailwind) {
            $pattern = '/class=(["\'])([^"\']*\b' . preg_quote($bootstrap, '/') . '\b[^"\']*)\1/';
            $content = preg_replace_callback($pattern, function($matches) use ($bootstrap, $tailwind) {
                $quote = $matches[1];
                $classes = $matches[2];
                $newClasses = str_replace($bootstrap, $tailwind, $classes);
                return 'class=' . $quote . $newClasses . $quote;
            }, $content);
            
            if ($content !== $originalContent) {
                $fixed = true;
            }
        }

        if ($fixed && $content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->fixStats['bootstrap_fixes']++;
            if (!in_array($filePath, $this->fixedFiles)) {
                $this->fixedFiles[] = $filePath;
            }
            echo "  ✓ Migrated Bootstrap in: " . basename($filePath) . "\n";
        }
    }

    /**
     * Generate comprehensive fix report
     */
    private function generateFixReport()
    {
        $this->fixStats['total_files_fixed'] = count($this->fixedFiles);
        
        echo "📊 CONTEXT7 AUTOMATED FIX REPORT\n";
        echo "================================\n\n";

        echo "🎯 FIX STATISTICS:\n";
        echo "- Syntax Errors Fixed: " . $this->fixStats['syntax_fixes'] . "\n";
        echo "- Route Issues Fixed: " . $this->fixStats['route_fixes'] . "\n";
        echo "- XSS Issues Fixed: " . $this->fixStats['xss_fixes'] . "\n";
        echo "- Bootstrap Migrations: " . $this->fixStats['bootstrap_fixes'] . "\n";
        echo "- Total Files Modified: " . $this->fixStats['total_files_fixed'] . "\n\n";

        // Calculate improvement
        $originalIssues = $this->reportData['stats']['syntax_errors'] + 
                         $this->reportData['stats']['route_issues'] + 
                         $this->reportData['stats']['xss_vulnerabilities'] + 
                         $this->reportData['stats']['bootstrap_usage'];
        
        $fixedIssues = $this->fixStats['syntax_fixes'] + 
                      $this->fixStats['route_fixes'] + 
                      $this->fixStats['xss_fixes'] + 
                      $this->fixStats['bootstrap_fixes'];
        
        $improvementRate = $originalIssues > 0 ? round(($fixedIssues / $originalIssues) * 100, 1) : 0;

        echo "🚀 IMPROVEMENT METRICS:\n";
        echo "- Issues Resolved: {$fixedIssues} / {$originalIssues}\n";
        echo "- Fix Success Rate: {$improvementRate}%\n";
        echo "- Project Quality Improvement: " . ($improvementRate > 80 ? "Excellent" : ($improvementRate > 60 ? "Good" : "Needs More Work")) . "\n\n";

        echo "📋 CONTEXT7 NEXT STEPS:\n";
        echo "1. 🧪 Run tests to validate fixes\n";
        echo "2. 🔍 Clear view cache: php artisan view:clear\n";
        echo "3. 🏗️ Build assets: npm run build\n";
        echo "4. 🌐 Test routes in browser\n";
        echo "5. 🔄 Re-run analysis to verify improvements\n\n";

        // Save fix report
        $fixReport = [
            'timestamp' => date('Y-m-d H:i:s'),
            'fix_stats' => $this->fixStats,
            'fixed_files' => $this->fixedFiles,
            'improvement_rate' => $improvementRate
        ];

        file_put_contents('context7_fix_report.json', json_encode($fixReport, JSON_PRETTY_PRINT));
        echo "💾 Fix report saved to: context7_fix_report.json\n";
    }
}

// Execute Context7 Automated Fixes
$fixer = new Context7BladeFixer();
$fixer->fixAllIssues();

echo "\n🎉 Context7 Automated Blade Fixes Complete!\n";
echo "🔧 Review the fixes and proceed with testing using Laravel 12 patterns.\n"; 