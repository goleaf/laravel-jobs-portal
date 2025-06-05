<?php

/**
 * 🔧 CONTEXT7 FORMREQUEST INTEGRATOR
 * 
 * Integrates FormRequest classes into controller methods using Context7 MCP patterns
 * Replaces generic Request parameters with specific FormRequest validation
 */

echo "\n🔧 CONTEXT7 FORMREQUEST INTEGRATOR\n";
echo "=" . str_repeat("=", 45) . "\n\n";

class Context7FormRequestIntegrator
{
    private $controllersPath = 'app/Http/Controllers';
    private $requestsPath = 'app/Http/Requests';
    
    private $methodsNeedingRequests = [
        'MaritalStatusController' => ['store', 'update', 'destroy'],
        'TranslationManagerController' => ['store', 'update'],
        'NotificationSettingsController' => ['update'],
        'JobCategoryController' => ['store', 'update', 'destroy'],
        'TestimonialsController' => ['store', 'update', 'destroy'],
        'CompanyController' => ['store', 'update', 'destroy'],
        'StateController' => ['store', 'update', 'destroy'],
        'CandidateController' => ['store', 'update', 'destroy'],
        'JobController' => ['store', 'update', 'destroy'],
        'TransactionController' => ['store', 'update', 'destroy'],
        'JobNotificationController' => ['store'],
        'FrontSettingsController' => ['update'],
        'EmailTemplateController' => ['update'],
        'SalaryPeriodController' => ['store', 'update', 'destroy'],
        'SettingController' => ['update'],
        'JobStageController' => ['store', 'update', 'destroy'],
        'OwnerShipTypeController' => ['store', 'update', 'destroy'],
        'PostController' => ['store', 'update', 'destroy'],
        'InquiryController' => ['destroy'],
        'ImageSliderController' => ['store', 'update', 'destroy'],
        'PostCategoryController' => ['store', 'update', 'destroy'],
        'BrandingSliderController' => ['store', 'update', 'destroy'],
        'CmsServicesController' => ['update'],
        'PlanController' => ['store', 'update', 'destroy'],
        'RequiredDegreeLevelController' => ['store', 'update', 'destroy'],
        'PrivacyPolicyController' => ['update'],
        'CityController' => ['store', 'update', 'destroy'],
        'CareerLevelController' => ['store', 'update', 'destroy'],
        'JobShiftController' => ['store', 'update', 'destroy'],
        'SubscriberController' => ['destroy'],
        'TagController' => ['store', 'update', 'destroy'],
        'NoticeboardController' => ['store', 'update', 'destroy'],
        'LanguageController' => ['store', 'update', 'destroy'],
        'JobApplicationController' => ['destroy'],
        'FunctionalAreaController' => ['store', 'update', 'destroy'],
        'SalaryCurrencyController' => ['store', 'update', 'destroy'],
        'HeaderSliderController' => ['store', 'update', 'destroy'],
        'FAQController' => ['store', 'update', 'destroy'],
        'JobTypeController' => ['store', 'update', 'destroy'],
        'CountryController' => ['store', 'update', 'destroy'],
        'SkillController' => ['store', 'update', 'destroy'],
        'IndustryController' => ['store', 'update', 'destroy'],
        'CompanySizeController' => ['store', 'update', 'destroy'],
        'AdminController' => ['store', 'update', 'destroy'],
        'BlogCommentController' => ['store'],
    ];

    private $integrationResults = [
        'controllers_updated' => 0,
        'methods_updated' => 0,
        'use_statements_added' => 0,
        'errors' => []
    ];

    public function integrateAll()
    {
        echo "🔄 **STARTING FORMREQUEST INTEGRATION**\n";
        echo "-" . str_repeat("-", 40) . "\n\n";

        foreach ($this->methodsNeedingRequests as $controller => $methods) {
            $this->integrateController($controller, $methods);
        }

        $this->generateIntegrationReport();
    }

    private function integrateController($controllerName, $methods)
    {
        $controllerFile = $this->findControllerFile($controllerName);
        
        if (!$controllerFile) {
            $this->integrationResults['errors'][] = "Controller file not found: {$controllerName}";
            echo "   ❌ {$controllerName}: File not found\n";
            return;
        }

        $content = file_get_contents($controllerFile);
        $originalContent = $content;
        $methodsUpdated = 0;

        echo "🔧 Processing: {$controllerName}\n";

        foreach ($methods as $method) {
            $requestClass = $this->determineRequestClass($controllerName, $method);
            
            if ($requestClass && $this->requestFileExists($requestClass)) {
                $content = $this->updateMethodSignature($content, $method, $requestClass);
                $content = $this->addUseStatement($content, $requestClass);
                $methodsUpdated++;
                echo "   ✅ Updated {$method}() -> {$requestClass}\n";
            } else {
                echo "   ⚠️  Skipped {$method}() -> Request class not found\n";
            }
        }

        if ($content !== $originalContent) {
            file_put_contents($controllerFile, $content);
            $this->integrationResults['controllers_updated']++;
            $this->integrationResults['methods_updated'] += $methodsUpdated;
            echo "   📝 Saved {$controllerName} with {$methodsUpdated} method updates\n";
        }

        echo "\n";
    }

    private function findControllerFile($controllerName)
    {
        $possiblePaths = [
            "{$this->controllersPath}/{$controllerName}.php",
            "{$this->controllersPath}/Admin/{$controllerName}.php",
            "{$this->controllersPath}/Auth/{$controllerName}.php",
            "{$this->controllersPath}/Web/{$controllerName}.php",
            "{$this->controllersPath}/Candidates/{$controllerName}.php",
            "{$this->controllersPath}/Employer/{$controllerName}.php",
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Search recursively
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->controllersPath)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === "{$controllerName}.php") {
                return $file->getPathname();
            }
        }

        return null;
    }

    private function determineRequestClass($controllerName, $methodName)
    {
        $controllerBase = str_replace('Controller', '', $controllerName);
        
        $requestMappings = [
            'store' => "Store{$controllerBase}Request",
            'update' => "Update{$controllerBase}Request",
            'destroy' => "Delete{$controllerBase}Request",
        ];

        return $requestMappings[$methodName] ?? null;
    }

    private function requestFileExists($requestClass)
    {
        $possiblePaths = [
            "{$this->requestsPath}/{$requestClass}.php",
            "{$this->requestsPath}/Admin/{$requestClass}.php",
            "{$this->requestsPath}/Auth/{$requestClass}.php",
            "{$this->requestsPath}/Web/{$requestClass}.php",
            "{$this->requestsPath}/Enhanced/{$requestClass}.php",
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return true;
            }
        }

        return false;
    }

    private function updateMethodSignature($content, $methodName, $requestClass)
    {
        // Pattern to match method signatures with Request parameter
        $patterns = [
            // public function methodName(Request $request)
            "/public\s+function\s+{$methodName}\s*\(\s*Request\s+\\\$request\s*\)/",
            // public function methodName(Request $request, $id)
            "/public\s+function\s+{$methodName}\s*\(\s*Request\s+\\\$request\s*,([^)]*)\)/",
            // public function methodName($id, Request $request)
            "/public\s+function\s+{$methodName}\s*\(([^,]*),\s*Request\s+\\\$request\s*\)/",
        ];

        $replacements = [
            "public function {$methodName}({$requestClass} \$request)",
            "public function {$methodName}({$requestClass} \$request,$1)",
            "public function {$methodName}($1, {$requestClass} \$request)",
        ];

        foreach ($patterns as $index => $pattern) {
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacements[$index], $content);
                break;
            }
        }

        return $content;
    }

    private function addUseStatement($content, $requestClass)
    {
        // Check if use statement already exists
        if (strpos($content, "use App\\Http\\Requests\\{$requestClass};") !== false) {
            return $content;
        }

        // Find the appropriate namespace for the request
        $requestNamespace = $this->findRequestNamespace($requestClass);
        $useStatement = "use App\\Http\\Requests\\{$requestNamespace}{$requestClass};";

        // Check if this specific use statement already exists
        if (strpos($content, $useStatement) !== false) {
            return $content;
        }

        // Find where to insert the use statement
        $lines = explode("\n", $content);
        $insertIndex = -1;
        $namespaceFound = false;

        foreach ($lines as $index => $line) {
            if (strpos($line, 'namespace ') === 0) {
                $namespaceFound = true;
                continue;
            }

            if ($namespaceFound && (strpos($line, 'use ') === 0 || trim($line) === '')) {
                continue;
            }

            if ($namespaceFound && strpos($line, 'class ') === 0) {
                $insertIndex = $index;
                break;
            }
        }

        if ($insertIndex > -1) {
            array_splice($lines, $insertIndex, 0, [$useStatement, '']);
            $this->integrationResults['use_statements_added']++;
        }

        return implode("\n", $lines);
    }

    private function findRequestNamespace($requestClass)
    {
        $possiblePaths = [
            '' => "{$this->requestsPath}/{$requestClass}.php",
            'Admin\\' => "{$this->requestsPath}/Admin/{$requestClass}.php",
            'Auth\\' => "{$this->requestsPath}/Auth/{$requestClass}.php",
            'Web\\' => "{$this->requestsPath}/Web/{$requestClass}.php",
            'Enhanced\\' => "{$this->requestsPath}/Enhanced/{$requestClass}.php",
        ];

        foreach ($possiblePaths as $namespace => $path) {
            if (file_exists($path)) {
                return $namespace;
            }
        }

        return '';
    }

    private function generateIntegrationReport()
    {
        echo "📊 **FORMREQUEST INTEGRATION REPORT**\n";
        echo "=" . str_repeat("=", 45) . "\n\n";

        echo "✅ **INTEGRATION RESULTS:**\n";
        echo "   • Controllers Updated: {$this->integrationResults['controllers_updated']}\n";
        echo "   • Methods Updated: {$this->integrationResults['methods_updated']}\n";
        echo "   • Use Statements Added: {$this->integrationResults['use_statements_added']}\n";

        if (!empty($this->integrationResults['errors'])) {
            echo "\n❌ **ERRORS ENCOUNTERED:**\n";
            foreach ($this->integrationResults['errors'] as $error) {
                echo "   • {$error}\n";
            }
        }

        echo "\n🎯 **NEXT STEPS:**\n";
        echo "   1. Run tests to verify integration: php artisan test\n";
        echo "   2. Check for any remaining generic Request usage\n";
        echo "   3. Update any custom validation logic in controllers\n";
        echo "   4. Verify all FormRequest files have proper validation rules\n";

        echo "\n📈 **EXPECTED IMPROVEMENTS:**\n";
        echo "   • Enhanced validation security\n";
        echo "   • Centralized validation logic\n";
        echo "   • Better error handling\n";
        echo "   • Improved code maintainability\n";
        echo "   • Context7 MCP compliance\n";

        echo "\n🔍 **VERIFICATION COMMANDS:**\n";
        echo "   # Check for remaining generic Request usage:\n";
        echo "   grep -r 'function.*Request \$request' app/Http/Controllers/\n";
        echo "\n   # Run validation tests:\n";
        echo "   php artisan test --filter=Validation\n";
    }
}

// Run the integrator
try {
    $integrator = new Context7FormRequestIntegrator();
    $integrator->integrateAll();
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "🔧 CONTEXT7 FORMREQUEST INTEGRATION COMPLETE!\n";
    echo str_repeat("=", 70) . "\n";
    
} catch (Exception $e) {
    echo "❌ Integration Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} 