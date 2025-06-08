<?php

/**
 * Comprehensive Blade Syntax Fixer - Universal
 * Fixes critical syntax errors in blade files
 */

require_once __DIR__ . '/vendor/autoload.php';

class BladeSyntaxFixer
{
    private $fixedFiles = [];
    private $fixCount = 0;
    private $errorCount = 0;
    private $syntaxPatterns = [];

    public function __construct()
    {
        echo "🔧 COMPREHENSIVE BLADE SYNTAX FIXER - Universal\n";
        echo "=" . str_repeat("=", 50) . "\n";
        echo "🎯 Fixing critical syntax errors in blade files\n\n";
        
        $this->setupFixPatterns();
    }

    private function setupFixPatterns()
    {
        $this->syntaxPatterns = [
            // Fix missing $ in @if conditions
            '/\@if\s*\(\s*([^$!][^)]*)\s*\)/' => '@if($1)',
            
            // Fix missing $ in variables within directives
            '/\@if\s*\(\s*!([a-zA-Z_][a-zA-Z0-9_-]*(?:\->[a-zA-Z0-9_-]+)*)\s*\)/' => '@if(!$$1)',
            
            // Fix missing $ in @foreach
            '/\@foreach\s*\(\s*([^$][^)]*)\s*\)/' => '@foreach($$1)',
            
            // Fix missing $ in other directives
            '/\@(endif|endforeach|empty|isset)\s*\(\s*([^$][^)]*)\s*\)/' => '@$1($$2)',
            
            // Fix unmatched quotes in blade outputs (conservative)
            '/\{\{\s*([^}]*)\s*\}\}\s*\{\{\s*([^}]*)\s*\}\}/' => '{{ $1 }} {{ $2 }}',
            
            // Fix common variable patterns
            '/\brow\->/g' => '$row->',
            '/\buser\->/g' => '$user->',
            '/\bcandidate\->/g' => '$candidate->',
            '/\bjob\->/g' => '$job->',
            '/\bcompany\->/g' => '$company->',
        ];
    }

    public function fixAllSyntaxErrors()
    {
        $this->loadSyntaxErrors();
        $this->processBladeFiles();
        $this->generateReport();
    }

    private function loadSyntaxErrors()
    {
        if (!file_exists('real_route_issues_report.json')) {
            echo "❌ Report file not found. Please run analyze_real_route_issues.php first.\n";
            exit(1);
        }
        
        $report = json_decode(file_get_contents('real_route_issues_report.json'), true);
        echo "📊 Loaded " . $report['summary']['syntax_errors'] . " syntax errors to fix\n\n";
    }

    private function processBladeFiles()
    {
        echo "🔧 Processing blade files with syntax fixes...\n";
        
        $viewsPath = __DIR__ . '/resources/views';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && 
                str_contains($file->getFilename(), '.blade.')) {
                $this->fixBladeFile($file->getPathname());
            }
        }

        echo "✅ Processed blade files\n";
        echo "🔧 Fixed {$this->fixCount} syntax issues in " . count($this->fixedFiles) . " files\n\n";
    }

    private function fixBladeFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fileFixed = false;
        $relativePath = str_replace(__DIR__ . '/resources/views/', '', $filePath);

        // Apply specific fixes based on common patterns
        $content = $this->fixMissingDollarSigns($content);
        $content = $this->fixCommonVariablePatterns($content);
        $content = $this->fixBladeDirectiveIssues($content);
        
        // Check if file was modified
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->fixedFiles[] = $relativePath;
            $this->fixCount++;
            $fileFixed = true;
            
            echo "✅ Fixed: {$relativePath}\n";
        }

        return $fileFixed;
    }

    private function fixMissingDollarSigns($content)
    {
        // Fix missing $ in @if conditions with variable names
        $content = preg_replace('/\@if\s*\(\s*([a-zA-Z_][a-zA-Z0-9_]*(?:\->[a-zA-Z0-9_]+)*)\s*\)/', '@if($$1)', $content);
        
        // Fix missing $ in @if conditions with negation
        $content = preg_replace('/\@if\s*\(\s*!([a-zA-Z_][a-zA-Z0-9_]*(?:\->[a-zA-Z0-9_]+)*)\s*\)/', '@if(!$$1)', $content);
        
        // Fix missing $ in @foreach
        $content = preg_replace('/\@foreach\s*\(\s*([a-zA-Z_][a-zA-Z0-9_]*(?:\->[a-zA-Z0-9_]+)*)\s+as\s+/', '@foreach($$1 as ', $content);
        
        // Fix missing $ in @isset
        $content = preg_replace('/\@isset\s*\(\s*([a-zA-Z_][a-zA-Z0-9_]*(?:\->[a-zA-Z0-9_]+)*)\s*\)/', '@isset($$1)', $content);
        
        return $content;
    }

    private function fixCommonVariablePatterns($content)
    {
        // Fix specific variable patterns that are missing $
        $variablePatterns = [
            '/\brow\->/' => '$row->',
            '/\buser\->/' => '$user->',
            '/\bcandidate\->/' => '$candidate->',
            '/\bjob\->/' => '$job->',
            '/\bcompany\->/' => '$company->',
            '/\bapplication\->/' => '$application->',
            '/\bitem\->/' => '$item->',
            '/\bmodel\->/' => '$model->',
        ];

        foreach ($variablePatterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    private function fixBladeDirectiveIssues($content)
    {
        // Fix Auth::user() calls that should have $ in conditionals
        $content = preg_replace('/\@if\s*\(\s*Auth::user\(\)/', '@if(Auth::user()', $content);
        
        // Fix spacing issues in blade outputs
        $content = preg_replace('/\{\{\s*([^}]+)\s*\}\}/', '{{ $1 }}', $content);
        
        // Fix malformed route calls (these are often false positives)
        // Most route() calls are actually correct, so we'll be conservative here
        
        return $content;
    }

    private function generateReport()
    {
        echo "📊 BLADE SYNTAX FIX REPORT\n";
        echo "=" . str_repeat("=", 50) . "\n\n";

        echo "🔧 Total Files Fixed: " . count($this->fixedFiles) . "\n";
        echo "🔧 Total Syntax Issues Fixed: {$this->fixCount}\n";
        echo "❌ Errors Encountered: {$this->errorCount}\n\n";

        if (!empty($this->fixedFiles)) {
            echo "📝 FIXED FILES:\n";
            echo str_repeat("-", 30) . "\n";
            foreach ($this->fixedFiles as $file) {
                echo "• {$file}\n";
            }
            echo "\n";
        }

        // Create summary report
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'summary' => [
                'files_fixed' => count($this->fixedFiles),
                'syntax_issues_fixed' => $this->fixCount,
                'errors' => $this->errorCount
            ],
            'fixed_files' => $this->fixedFiles
        ];

        file_put_contents('blade_syntax_fix_report.json', json_encode($report, JSON_PRETTY_PRINT));
        echo "💾 Fix report saved: blade_syntax_fix_report.json\n\n";
    }
}

// Run the fixer
try {
    $fixer = new BladeSyntaxFixer();
    $fixer->fixAllSyntaxErrors();
    
    echo "🎉 Blade syntax fixing completed!\n";
    echo "📝 Review the fixed files and test the application\n";
    echo "🔄 You may want to run the analyzer again to verify fixes\n\n";
    
} catch (Exception $e) {
    echo "❌ Syntax fixing failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 