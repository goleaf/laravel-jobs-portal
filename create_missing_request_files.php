<?php

/**
 * Missing Request Files Generator
 * 
 * Analyzes all controllers and creates missing request validation files
 * with multilingual error messages
 */

require_once __DIR__ . '/vendor/autoload.php';

class RequestFileGenerator
{
    private $controllersPath;
    private $requestsPath;
    private $missingRequests = [];
    private $existingRequests = [];
    private $analysisResults = [];

    public function __construct()
    {
        $this->controllersPath = __DIR__ . '/app/Http/Controllers';
        $this->requestsPath = __DIR__ . '/app/Http/Requests';
    }

    /**
     * Analyze controllers and generate missing request files
     */
    public function generateMissingRequests()
    {
        echo "=== REQUEST FILE GENERATOR ===\n";
        echo "Analyzing controllers for missing request files...\n\n";

        $this->analyzeExistingRequests();
        $this->analyzeControllers();
        $this->generateMissingRequestFiles();
        $this->generateReport();
    }

    /**
     * Analyze existing request files
     */
    private function analyzeExistingRequests()
    {
        $existingFiles = $this->findRequestFiles($this->requestsPath);
        
        foreach ($existingFiles as $file) {
            $className = $this->getClassNameFromPath($file);
            $this->existingRequests[] = $className;
        }

        echo "Found " . count($this->existingRequests) . " existing request files\n";
    }

    /**
     * Find all request files
     */
    private function findRequestFiles($directory)
    {
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Analyze all controllers
     */
    private function analyzeControllers()
    {
        $controllerFiles = $this->findControllerFiles($this->controllersPath);
        
        foreach ($controllerFiles as $file) {
            $this->analyzeController($file);
        }
    }

    /**
     * Find all controller files
     */
    private function findControllerFiles($directory)
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && 
                pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'php' &&
                strpos($file->getFilename(), 'Controller.php') !== false) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Analyze a single controller
     */
    private function analyzeController($filePath)
    {
        $content = file_get_contents($filePath);
        $className = $this->getControllerClassName($filePath);
        
        echo "Analyzing controller: $className\n";

        // Find all public methods that should have requests
        preg_match_all('/public\s+function\s+(\w+)\s*\([^)]*\)/', $content, $methods);
        
        $controllerMethods = [];
        foreach ($methods[1] as $method) {
            // Skip special methods
            if (in_array($method, ['__construct', '__invoke', 'middleware'])) {
                continue;
            }
            
            $controllerMethods[] = $method;
            $this->analyzeControllerMethod($className, $method, $content);
        }

        $this->analysisResults[$className] = $controllerMethods;
    }

    /**
     * Analyze a controller method for request validation needs
     */
    private function analyzeControllerMethod($controller, $method, $content)
    {
        // Methods that typically need validation
        $validationMethods = ['store', 'update', 'create', 'edit'];
        
        // Check if method has validation needs
        $needsValidation = false;
        
        if (in_array($method, $validationMethods)) {
            $needsValidation = true;
        }
        
        // Check if method already uses a request class
        $pattern = '/function\s+' . preg_quote($method) . '\s*\([^)]*(\w+Request)[^)]*\)/';
        if (preg_match($pattern, $content, $matches)) {
            // Already has a request class
            return;
        }
        
        // Check for $request->validate() calls
        if (strpos($content, '$request->validate(') !== false) {
            $needsValidation = true;
        }

        if ($needsValidation) {
            $requestClassName = $this->generateRequestClassName($controller, $method);
            
            if (!in_array($requestClassName, $this->existingRequests)) {
                $this->missingRequests[] = [
                    'controller' => $controller,
                    'method' => $method,
                    'request_class' => $requestClassName,
                    'type' => $this->getRequestType($method)
                ];
            }
        }
    }

    /**
     * Generate request class name
     */
    private function generateRequestClassName($controller, $method)
    {
        $controllerName = str_replace('Controller', '', $controller);
        
        $methodMappings = [
            'store' => 'Store',
            'update' => 'Update',
            'create' => 'Create',
            'edit' => 'Update',
            'destroy' => 'Delete',
            'index' => 'Index',
            'show' => 'Show'
        ];
        
        $prefix = $methodMappings[$method] ?? ucfirst($method);
        
        return $prefix . $controllerName . 'Request';
    }

    /**
     * Get request type for validation rules
     */
    private function getRequestType($method)
    {
        $types = [
            'store' => 'create',
            'update' => 'update',
            'create' => 'create',
            'edit' => 'update',
            'destroy' => 'delete'
        ];
        
        return $types[$method] ?? 'general';
    }

    /**
     * Generate missing request files
     */
    private function generateMissingRequestFiles()
    {
        echo "\nGenerating missing request files...\n";
        
        foreach ($this->missingRequests as $request) {
            $this->createRequestFile($request);
        }
    }

    /**
     * Create a request file
     */
    private function createRequestFile($requestData)
    {
        $className = $requestData['request_class'];
        $controller = $requestData['controller'];
        $method = $requestData['method'];
        $type = $requestData['type'];
        
        echo "Creating: $className\n";

        $template = $this->getRequestTemplate($className, $controller, $method, $type);
        
        $filePath = $this->requestsPath . '/' . $className . '.php';
        file_put_contents($filePath, $template);
    }

    /**
     * Get request file template
     */
    private function getRequestTemplate($className, $controller, $method, $type)
    {
        $rules = $this->generateValidationRules($controller, $method, $type);
        $messages = $this->generateValidationMessages();
        
        return <<<PHP
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request validation for $controller::$method
 * 
 * @generated by RequestFileGenerator
 */
class $className extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Implement proper authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
$rules
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
$messages
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            // Add custom attribute names for better error messages
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Add any data preparation logic here
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  \$validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator \$validator): void
    {
        // Custom validation failure handling if needed
        parent::failedValidation(\$validator);
    }
}
PHP;
    }

    /**
     * Generate validation rules based on controller and method
     */
    private function generateValidationRules($controller, $method, $type)
    {
        // Basic rules - fewer to start with
        $basicRules = [
            'name' => "'required|string|max:255'",
            'title' => "'required|string|max:255'"
        ];

        // Controller-specific rules
        $controllerRules = $this->getControllerSpecificRules($controller, $method);
        
        $rules = array_merge($basicRules, $controllerRules);
        
        // Format rules for the template
        $formattedRules = [];
        foreach ($rules as $field => $rule) {
            $formattedRules[] = "            '$field' => $rule,";
        }

        return implode("\n", $formattedRules);
    }

    /**
     * Get controller-specific validation rules
     */
    private function getControllerSpecificRules($controller, $method)
    {
        $rules = [];

        // Add specific rules based on controller name
        if (strpos($controller, 'Job') !== false) {
            $rules['job_category_id'] = "'nullable|exists:job_categories,id'";
            $rules['description'] = "'nullable|string'";
        }

        if (strpos($controller, 'Company') !== false) {
            $rules['industry_id'] = "'nullable|exists:industries,id'";
            $rules['company_size_id'] = "'nullable|exists:company_sizes,id'";
        }

        if (strpos($controller, 'User') !== false) {
            $rules['email'] = "'required|email'";
            $rules['password'] = "'nullable|string|min:8'";
        }

        return $rules;
    }

    /**
     * Generate validation messages using JSON translations
     */
    private function generateValidationMessages()
    {
        $messages = [
            "'required' => __('validation.required')",
            "'email' => __('validation.email')",
            "'string' => __('validation.string')",
            "'max' => __('validation.max')",
            "'min' => __('validation.min')"
        ];

        $formattedMessages = [];
        foreach ($messages as $message) {
            $formattedMessages[] = "            $message,";
        }

        return implode("\n", $formattedMessages);
    }

    /**
     * Get class name from file path
     */
    private function getClassNameFromPath($filePath)
    {
        return basename($filePath, '.php');
    }

    /**
     * Get controller class name
     */
    private function getControllerClassName($filePath)
    {
        return basename($filePath, '.php');
    }

    /**
     * Generate comprehensive report
     */
    private function generateReport()
    {
        echo "\n=== REPORT ===\n";
        echo "Existing request files: " . count($this->existingRequests) . "\n";
        echo "Missing request files: " . count($this->missingRequests) . "\n";
        echo "Controllers analyzed: " . count($this->analysisResults) . "\n\n";

        if (!empty($this->missingRequests)) {
            echo "Generated request files:\n";
            foreach ($this->missingRequests as $request) {
                echo "  - {$request['request_class']} (for {$request['controller']}::{$request['method']})\n";
            }
        }

        $this->createImplementationGuide();
    }

    /**
     * Create implementation guide
     */
    private function createImplementationGuide()
    {
        $guide = <<<'GUIDE'
# Request Validation Implementation Guide

## Overview
Generated request validation files for all controller methods that need validation.

## Implementation Steps

### 1. Update Controller Methods
Replace existing validation with the new request classes.

### 2. Import Request Classes
Add import statements to controllers.

### 3. Customize Validation Rules
Review and customize the generated validation rules.

### 4. Test Validation
Test each request validation thoroughly.

GUIDE;

        file_put_contents(__DIR__ . '/REQUEST_VALIDATION_GUIDE.md', $guide);
        echo "\nImplementation guide created: REQUEST_VALIDATION_GUIDE.md\n";
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    $generator = new RequestFileGenerator();
    $generator->generateMissingRequests();
    
    echo "\n=== COMPLETED ===\n";
    echo "Request file generation complete!\n";
} 