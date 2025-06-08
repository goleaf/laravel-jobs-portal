<?php

/**
 * Comprehensive Request Validation Coverage Analyzer
 * Analyzes controllers, request files, and validation patterns
 */

require 'vendor/autoload.php';

class RequestValidationAnalyzer 
{
    private $controllers = [];
    private $requests = [];
    private $issues = [];
    private $stats = [
        'total_controllers' => 0,
        'total_methods' => 0,
        'methods_with_requests' => 0,
        'methods_needing_validation' => 0,
        'request_files' => 0,
        'validation_coverage' => 0
    ];

    public function analyze()
    {
        echo "🔍 Starting Comprehensive Request Validation Analysis...\n\n";
        
        $this->scanControllers();
        $this->scanRequestFiles();
        $this->analyzeValidationCoverage();
        $this->generateReport();
    }

    private function scanControllers()
    {
        echo "📂 Scanning Controllers...\n";
        
        $controllerFiles = glob('app/Http/Controllers/**/*.php');
        $this->stats['total_controllers'] = count($controllerFiles);
        
        foreach ($controllerFiles as $file) {
            $this->analyzeController($file);
        }
        
        echo "   Found {$this->stats['total_controllers']} controller files\n";
        echo "   Found {$this->stats['total_methods']} controller methods\n\n";
    }

    private function analyzeController($file)
    {
        $content = file_get_contents($file);
        $className = $this->extractClassName($file);
        
        // Find all public methods that likely need validation
        preg_match_all('/public function\s+(\w+)\s*\([^)]*Request[^)]*/', $content, $matches);
        $methodsWithRequests = $matches[1] ?? [];
        
        // Find all public methods
        preg_match_all('/public function\s+(\w+)\s*\([^)]*/', $content, $allMatches);
        $allMethods = $allMatches[1] ?? [];
        
        // Filter out constructor, magic methods, and getter methods
        $validationMethods = array_filter($allMethods, function($method) {
            return !in_array($method, ['__construct', '__invoke']) && 
                   !str_starts_with($method, 'get') &&
                   !str_starts_with($method, 'is') &&
                   !str_starts_with($method, 'has') &&
                   in_array($method, ['store', 'update', 'create', 'edit', 'destroy', 'upload', 'import']);
        });
        
        $this->stats['total_methods'] += count($allMethods);
        $this->stats['methods_with_requests'] += count($methodsWithRequests);
        $this->stats['methods_needing_validation'] += count($validationMethods);
        
        if (!empty($validationMethods)) {
            $missingValidation = array_diff($validationMethods, $methodsWithRequests);
            
            $this->controllers[$className] = [
                'file' => $file,
                'all_methods' => $allMethods,
                'validation_methods' => $validationMethods,
                'methods_with_requests' => $methodsWithRequests,
                'missing_validation' => $missingValidation,
                'validation_coverage' => count($validationMethods) > 0 ? 
                    (count($methodsWithRequests) / count($validationMethods)) * 100 : 100
            ];
        }
    }

    private function scanRequestFiles()
    {
        echo "📝 Scanning Request Files...\n";
        
        $requestFiles = glob('app/Http/Requests/**/*.php');
        $this->stats['request_files'] = count($requestFiles);
        
        foreach ($requestFiles as $file) {
            $this->analyzeRequestFile($file);
        }
        
        echo "   Found {$this->stats['request_files']} request files\n\n";
    }

    private function analyzeRequestFile($file)
    {
        $content = file_get_contents($file);
        $className = $this->extractClassName($file);
        
        // Check for rules method
        $hasRules = strpos($content, 'function rules()') !== false || 
                   strpos($content, 'function rules(') !== false;
        
        // Check for messages method
        $hasMessages = strpos($content, 'function messages()') !== false;
        
        // Check for authorize method
        $hasAuthorize = strpos($content, 'function authorize()') !== false;
        
        // Count validation rules
        preg_match_all('/[\'"][a-zA-Z_][a-zA-Z0-9_]*[\'"][\\s]*=>[\\s]*[\'"][^\'"]/', $content, $rulesMatches);
        $rulesCount = count($rulesMatches[0] ?? []);
        
        $this->requests[$className] = [
            'file' => $file,
            'has_rules' => $hasRules,
            'has_messages' => $hasMessages,
            'has_authorize' => $hasAuthorize,
            'rules_count' => $rulesCount,
            'quality_score' => $this->calculateRequestQuality($hasRules, $hasMessages, $hasAuthorize, $rulesCount)
        ];
        
        if (!$hasRules) {
            $this->issues[] = "⚠️  Request file {$className} missing rules() method";
        }
        
        if ($rulesCount === 0) {
            $this->issues[] = "⚠️  Request file {$className} has no validation rules";
        }
    }

    private function calculateRequestQuality($hasRules, $hasMessages, $hasAuthorize, $rulesCount)
    {
        $score = 0;
        $score += $hasRules ? 40 : 0;
        $score += $hasMessages ? 20 : 0;
        $score += $hasAuthorize ? 20 : 0;
        $score += min($rulesCount * 2, 20);
        return $score;
    }

    private function analyzeValidationCoverage()
    {
        echo "📊 Calculating Validation Coverage...\n";
        
        $this->stats['validation_coverage'] = $this->stats['methods_needing_validation'] > 0 ? 
            ($this->stats['methods_with_requests'] / $this->stats['methods_needing_validation']) * 100 : 100;
        
        echo "   Validation Coverage: " . round($this->stats['validation_coverage'], 1) . "%\n\n";
    }

    private function generateReport()
    {
        echo "📋 COMPREHENSIVE VALIDATION ANALYSIS REPORT\n";
        echo str_repeat("=", 60) . "\n\n";
        
        echo "📈 STATISTICS:\n";
        echo "   Total Controllers: {$this->stats['total_controllers']}\n";
        echo "   Total Methods: {$this->stats['total_methods']}\n";
        echo "   Methods Needing Validation: {$this->stats['methods_needing_validation']}\n";
        echo "   Methods With Request Classes: {$this->stats['methods_with_requests']}\n";
        echo "   Request Files: {$this->stats['request_files']}\n";
        echo "   Validation Coverage: " . round($this->stats['validation_coverage'], 1) . "%\n\n";
        
        echo "🚨 TOP ISSUES TO ADDRESS:\n";
        foreach (array_slice($this->issues, 0, 10) as $issue) {
            echo "   $issue\n";
        }
        echo "\n";
        
        echo "🏆 TOP PERFORMING CONTROLLERS:\n";
        $sortedControllers = $this->controllers;
        uasort($sortedControllers, function($a, $b) {
            return $b['validation_coverage'] <=> $a['validation_coverage'];
        });
        
        $count = 0;
        foreach ($sortedControllers as $name => $controller) {
            if ($count++ >= 10) break;
            echo "   ✅ $name: " . round($controller['validation_coverage'], 1) . "% coverage\n";
        }
        echo "\n";
        
        echo "⚠️  CONTROLLERS NEEDING ATTENTION:\n";
        $lowCoverage = array_filter($this->controllers, function($controller) {
            return $controller['validation_coverage'] < 50;
        });
        
        foreach (array_slice($lowCoverage, 0, 10) as $name => $controller) {
            echo "   🔴 $name: " . round($controller['validation_coverage'], 1) . "% coverage\n";
            foreach ($controller['missing_validation'] as $method) {
                echo "      - Missing validation for: $method()\n";
            }
        }
        echo "\n";
        
        echo "💎 REQUEST FILE QUALITY:\n";
        $highQualityRequests = array_filter($this->requests, function($request) {
            return $request['quality_score'] >= 80;
        });
        
        $lowQualityRequests = array_filter($this->requests, function($request) {
            return $request['quality_score'] < 60;
        });
        
        echo "   High Quality Requests (≥80%): " . count($highQualityRequests) . "\n";
        echo "   Low Quality Requests (<60%): " . count($lowQualityRequests) . "\n";
        echo "\n";
        
        echo "🎯 RECOMMENDED ACTIONS:\n";
        
        if ($this->stats['validation_coverage'] < 80) {
            echo "   1. 🔥 PRIORITY: Improve validation coverage from " . 
                 round($this->stats['validation_coverage'], 1) . "% to 80%+\n";
        }
        
        if (count($lowQualityRequests) > 0) {
            echo "   2. 📝 Enhance " . count($lowQualityRequests) . " request files with better validation\n";
        }
        
        echo "   3. 🧪 Add validation tests for all request classes\n";
        echo "   4. 🌍 Add multilingual validation messages\n";
        echo "   5. 🔒 Implement authorization logic in request files\n";
        echo "\n";
        
        $this->saveReport();
    }

    private function saveReport()
    {
        $reportData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'stats' => $this->stats,
            'controllers' => $this->controllers,
            'requests' => $this->requests,
            'issues' => $this->issues
        ];
        
        file_put_contents('request_validation_analysis_report.json', json_encode($reportData, JSON_PRETTY_PRINT));
        echo "💾 Full report saved to: request_validation_analysis_report.json\n";
    }

    private function extractClassName($file)
    {
        $content = file_get_contents($file);
        preg_match('/class\s+(\w+)/', $content, $matches);
        return $matches[1] ?? basename($file, '.php');
    }
}

// Run the analysis
$analyzer = new RequestValidationAnalyzer();
$analyzer->analyze();

echo "\n🎉 Analysis Complete! Check the JSON report for detailed findings.\n"; 