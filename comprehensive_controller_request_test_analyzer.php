<?php

/**
 * 🔍 COMPREHENSIVE CONTROLLER-REQUEST-TEST ANALYZER
 * 
 * Analyzes all controllers in the project to ensure each has:
 * 1. Appropriate request files for validation
 * 2. Corresponding test files for testing
 * 3. Proper implementation patterns
 */

echo "\n🔍 COMPREHENSIVE CONTROLLER-REQUEST-TEST ANALYZER\n";
echo "=" . str_repeat("=", 60) . "\n\n";

class ControllerRequestTestAnalyzer 
{
    private $controllersPath = 'app/Http/Controllers';
    private $requestsPath = 'app/Http/Requests';
    private $testsPath = 'tests';
    
    private $controllers = [];
    private $requests = [];
    private $tests = [];
    
    private $analysis = [
        'total_controllers' => 0,
        'controllers_with_requests' => 0,
        'controllers_with_tests' => 0,
        'controllers_missing_requests' => [],
        'controllers_missing_tests' => [],
        'orphaned_requests' => [],
        'orphaned_tests' => [],
        'controller_methods_needing_requests' => []
    ];

    public function __construct()
    {
        $this->loadControllers();
        $this->loadRequests();
        $this->loadTests();
    }

    private function loadControllers()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->controllersPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace($this->controllersPath . '/', '', $file->getPathname());
                $className = $this->getClassNameFromFile($file->getPathname());
                
                if ($className && $className !== 'Controller' && $className !== 'AppBaseController' && $className !== 'Context7BaseController') {
                    $this->controllers[] = [
                        'file' => $file->getPathname(),
                        'relative_path' => $relativePath,
                        'class_name' => $className,
                        'namespace' => $this->getNamespaceFromFile($file->getPathname()),
                        'methods' => $this->getPublicMethods($file->getPathname())
                    ];
                }
            }
        }
        
        $this->analysis['total_controllers'] = count($this->controllers);
    }

    private function loadRequests()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->requestsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $className = $this->getClassNameFromFile($file->getPathname());
                if ($className) {
                    $this->requests[] = [
                        'file' => $file->getPathname(),
                        'class_name' => $className,
                        'namespace' => $this->getNamespaceFromFile($file->getPathname())
                    ];
                }
            }
        }
    }

    private function loadTests()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->testsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getFilename(), 'Test.php') !== false) {
                $className = $this->getClassNameFromFile($file->getPathname());
                if ($className) {
                    $this->tests[] = [
                        'file' => $file->getPathname(),
                        'class_name' => $className,
                        'namespace' => $this->getNamespaceFromFile($file->getPathname())
                    ];
                }
            }
        }
    }

    private function getClassNameFromFile($filePath)
    {
        $content = file_get_contents($filePath);
        preg_match('/class\s+(\w+)/', $content, $matches);
        return $matches[1] ?? null;
    }

    private function getNamespaceFromFile($filePath)
    {
        $content = file_get_contents($filePath);
        preg_match('/namespace\s+([^;]+);/', $content, $matches);
        return $matches[1] ?? null;
    }

    private function getPublicMethods($filePath)
    {
        $content = file_get_contents($filePath);
        preg_match_all('/public\s+function\s+(\w+)\s*\([^)]*(\w*Request\s+\$\w+|Request\s+\$\w+)?[^)]*\)/i', $content, $matches, PREG_SET_ORDER);
        
        $methods = [];
        foreach ($matches as $match) {
            $methodName = $match[1];
            $hasRequest = isset($match[2]) && !empty($match[2]);
            $requestType = $hasRequest ? (strpos($match[2], 'Request $') !== false ? 'generic' : 'specific') : 'none';
            
            $methods[] = [
                'name' => $methodName,
                'has_request' => $hasRequest,
                'request_type' => $requestType,
                'full_signature' => $match[0]
            ];
        }
        
        return $methods;
    }

    public function analyze()
    {
        echo "📊 **STARTING COMPREHENSIVE ANALYSIS**\n";
        echo "-" . str_repeat("-", 45) . "\n\n";

        $this->analyzeControllerRequestCoverage();
        $this->analyzeControllerTestCoverage();
        $this->analyzeOrphanedFiles();
        $this->analyzeMethodValidation();
        
        $this->generateReport();
    }

    private function analyzeControllerRequestCoverage()
    {
        echo "1️⃣ **ANALYZING CONTROLLER-REQUEST COVERAGE**\n";
        
        foreach ($this->controllers as $controller) {
            $controllerName = $controller['class_name'];
            $hasRequestFile = false;
            
            // Look for corresponding request files
            $possibleRequestNames = [
                $controllerName . 'Request',
                str_replace('Controller', 'Request', $controllerName),
                'Store' . str_replace('Controller', 'Request', $controllerName),
                'Update' . str_replace('Controller', 'Request', $controllerName),
                'Create' . str_replace('Controller', 'Request', $controllerName),
            ];
            
            foreach ($this->requests as $request) {
                if (in_array($request['class_name'], $possibleRequestNames) || 
                    strpos($request['class_name'], str_replace('Controller', '', $controllerName)) !== false) {
                    $hasRequestFile = true;
                    break;
                }
            }
            
            if ($hasRequestFile) {
                $this->analysis['controllers_with_requests']++;
                echo "   ✅ {$controllerName}: Has request files\n";
            } else {
                $this->analysis['controllers_missing_requests'][] = $controller;
                echo "   ❌ {$controllerName}: Missing request files\n";
            }
        }
        
        echo "\n";
    }

    private function analyzeControllerTestCoverage()
    {
        echo "2️⃣ **ANALYZING CONTROLLER-TEST COVERAGE**\n";
        
        foreach ($this->controllers as $controller) {
            $controllerName = $controller['class_name'];
            $hasTestFile = false;
            
            // Look for corresponding test files
            $possibleTestNames = [
                $controllerName . 'Test',
                str_replace('Controller', 'Test', $controllerName),
                str_replace('Controller', 'ControllerTest', $controllerName),
            ];
            
            foreach ($this->tests as $test) {
                if (in_array($test['class_name'], $possibleTestNames) || 
                    strpos($test['class_name'], str_replace('Controller', '', $controllerName)) !== false) {
                    $hasTestFile = true;
                    break;
                }
            }
            
            if ($hasTestFile) {
                $this->analysis['controllers_with_tests']++;
                echo "   ✅ {$controllerName}: Has test files\n";
            } else {
                $this->analysis['controllers_missing_tests'][] = $controller;
                echo "   ❌ {$controllerName}: Missing test files\n";
            }
        }
        
        echo "\n";
    }

    private function analyzeOrphanedFiles()
    {
        echo "3️⃣ **ANALYZING ORPHANED FILES**\n";
        
        // Find requests without corresponding controllers
        foreach ($this->requests as $request) {
            $requestName = $request['class_name'];
            $hasController = false;
            
            foreach ($this->controllers as $controller) {
                $controllerName = $controller['class_name'];
                if (strpos($requestName, str_replace('Controller', '', $controllerName)) !== false) {
                    $hasController = true;
                    break;
                }
            }
            
            if (!$hasController) {
                $this->analysis['orphaned_requests'][] = $request;
                echo "   ⚠️  Orphaned request: {$requestName}\n";
            }
        }
        
        // Find tests without corresponding controllers
        foreach ($this->tests as $test) {
            $testName = $test['class_name'];
            $hasController = false;
            
            foreach ($this->controllers as $controller) {
                $controllerName = $controller['class_name'];
                $baseControllerName = str_replace('Controller', '', $controllerName);
                if (strpos($testName, $baseControllerName) !== false || 
                    strpos($testName, $controllerName) !== false) {
                    $hasController = true;
                    break;
                }
            }
            
            if (!$hasController) {
                $this->analysis['orphaned_tests'][] = $test;
                echo "   ⚠️  Orphaned test: {$testName}\n";
            }
        }
        
        echo "\n";
    }

    private function analyzeMethodValidation()
    {
        echo "4️⃣ **ANALYZING METHOD VALIDATION PATTERNS**\n";
        
        foreach ($this->controllers as $controller) {
            $controllerName = $controller['class_name'];
            
            foreach ($controller['methods'] as $method) {
                $methodName = $method['name'];
                
                // Skip methods that typically don't need validation
                if (in_array($methodName, ['index', 'show', 'edit', 'create', '__construct'])) {
                    continue;
                }
                
                if ($method['request_type'] === 'generic') {
                    $this->analysis['controller_methods_needing_requests'][] = [
                        'controller' => $controllerName,
                        'method' => $methodName,
                        'issue' => 'Using generic Request instead of FormRequest',
                        'file' => $controller['file']
                    ];
                    echo "   ⚠️  {$controllerName}::{$methodName}() uses generic Request\n";
                } elseif ($method['request_type'] === 'none' && in_array($methodName, ['store', 'update', 'destroy'])) {
                    $this->analysis['controller_methods_needing_requests'][] = [
                        'controller' => $controllerName,
                        'method' => $methodName,
                        'issue' => 'No request validation',
                        'file' => $controller['file']
                    ];
                    echo "   ❌ {$controllerName}::{$methodName}() has no request validation\n";
                }
            }
        }
        
        echo "\n";
    }

    private function generateReport()
    {
        echo "📋 **COMPREHENSIVE ANALYSIS REPORT**\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
        
        // Summary Statistics
        echo "📊 **SUMMARY STATISTICS:**\n";
        echo "   • Total Controllers: {$this->analysis['total_controllers']}\n";
        echo "   • Total Request Files: " . count($this->requests) . "\n";
        echo "   • Total Test Files: " . count($this->tests) . "\n";
        echo "   • Controllers with Requests: {$this->analysis['controllers_with_requests']}\n";
        echo "   • Controllers with Tests: {$this->analysis['controllers_with_tests']}\n";
        
        // Coverage Percentages
        $requestCoverage = round(($this->analysis['controllers_with_requests'] / $this->analysis['total_controllers']) * 100, 1);
        $testCoverage = round(($this->analysis['controllers_with_tests'] / $this->analysis['total_controllers']) * 100, 1);
        
        echo "\n📈 **COVERAGE PERCENTAGES:**\n";
        echo "   • Request Coverage: {$requestCoverage}%\n";
        echo "   • Test Coverage: {$testCoverage}%\n";
        
        // Missing Request Files
        echo "\n❌ **CONTROLLERS MISSING REQUEST FILES (" . count($this->analysis['controllers_missing_requests']) . "):**\n";
        foreach ($this->analysis['controllers_missing_requests'] as $controller) {
            echo "   • {$controller['class_name']} ({$controller['relative_path']})\n";
        }
        
        // Missing Test Files
        echo "\n❌ **CONTROLLERS MISSING TEST FILES (" . count($this->analysis['controllers_missing_tests']) . "):**\n";
        foreach ($this->analysis['controllers_missing_tests'] as $controller) {
            echo "   • {$controller['class_name']} ({$controller['relative_path']})\n";
        }
        
        // Methods Needing Validation
        echo "\n⚠️  **METHODS NEEDING PROPER VALIDATION (" . count($this->analysis['controller_methods_needing_requests']) . "):**\n";
        foreach ($this->analysis['controller_methods_needing_requests'] as $method) {
            echo "   • {$method['controller']}::{$method['method']}() - {$method['issue']}\n";
        }
        
        // Orphaned Files
        if (!empty($this->analysis['orphaned_requests'])) {
            echo "\n🗂️  **ORPHANED REQUEST FILES (" . count($this->analysis['orphaned_requests']) . "):**\n";
            foreach ($this->analysis['orphaned_requests'] as $request) {
                echo "   • {$request['class_name']}\n";
            }
        }
        
        if (!empty($this->analysis['orphaned_tests'])) {
            echo "\n🗂️  **ORPHANED TEST FILES (" . count($this->analysis['orphaned_tests']) . "):**\n";
            foreach ($this->analysis['orphaned_tests'] as $test) {
                echo "   • {$test['class_name']}\n";
            }
        }
        
        $this->generateActionPlan();
    }

    private function generateActionPlan()
    {
        echo "\n🚀 **ACTION PLAN FOR IMPROVEMENT**\n";
        echo "=" . str_repeat("=", 40) . "\n\n";
        
        echo "🎯 **PRIORITY 1: Missing Request Files**\n";
        foreach ($this->analysis['controllers_missing_requests'] as $controller) {
            $controllerName = str_replace('Controller', '', $controller['class_name']);
            echo "   php artisan make:request Create{$controllerName}Request\n";
            echo "   php artisan make:request Update{$controllerName}Request\n";
        }
        
        echo "\n🎯 **PRIORITY 2: Missing Test Files**\n";
        foreach ($this->analysis['controllers_missing_tests'] as $controller) {
            echo "   php artisan make:test {$controller['class_name']}Test\n";
        }
        
        echo "\n🎯 **PRIORITY 3: Fix Method Validation**\n";
        $methodIssues = array_unique(array_column($this->analysis['controller_methods_needing_requests'], 'controller'));
        foreach ($methodIssues as $controllerName) {
            echo "   Review and update {$controllerName} methods to use FormRequest classes\n";
        }
        
        echo "\n✨ **RECOMMENDATION: Use Context7 Patterns**\n";
        echo "   • Implement Context7BaseController for all controllers\n";
        echo "   • Use Context7 request validation patterns\n";
        echo "   • Add Context7 test patterns for comprehensive coverage\n";
        echo "   • Follow Context7 MCP best practices\n";
        
        echo "\n🎉 **COMPLETION GOAL:**\n";
        echo "   • 100% Request Coverage for validation methods\n";
        echo "   • 95%+ Test Coverage for all controllers\n";
        echo "   • Zero orphaned files\n";
        echo "   • Consistent Context7 patterns\n";
    }
}

// Run the analysis
try {
    $analyzer = new ControllerRequestTestAnalyzer();
    $analyzer->analyze();
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "🔍 COMPREHENSIVE CONTROLLER-REQUEST-TEST ANALYSIS COMPLETE!\n";
    echo str_repeat("=", 70) . "\n";
    
} catch (Exception $e) {
    echo "❌ Analysis Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} 