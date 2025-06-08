<?php

require_once 'vendor/autoload.php';

class RequestCoverageAnalyzer
{
    private $controllersPath = 'app/Http/Controllers';
    private $requestsPath = 'app/Http/Requests';
    private $missingRequests = [];
    private $existingRequests = [];

    public function __construct()
    {
        $this->loadExistingRequests();
    }

    private function loadExistingRequests()
    {
        $requestFiles = $this->getAllPhpFiles($this->requestsPath);
        foreach ($requestFiles as $file) {
            $requestName = basename($file, '.php');
            $this->existingRequests[] = $requestName;
        }
    }

    private function getAllPhpFiles($directory)
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }

    public function analyzeControllers()
    {
        $controllerFiles = $this->getAllPhpFiles($this->controllersPath);
        
        foreach ($controllerFiles as $file) {
            $this->analyzeController($file);
        }
    }

    private function analyzeController($filePath)
    {
        $content = file_get_contents($filePath);
        $className = $this->getClassName($content);
        
        if (!$className) {
            return;
        }

        // Find all public methods with Request parameters
        preg_match_all('/public\s+function\s+(\w+)\s*\([^)]*(\w*Request\s+\$\w+|Request\s+\$\w+)[^)]*\)/i', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $index => $methodName) {
                $this->analyzeMethod($className, $methodName, $matches[2][$index], $filePath);
            }
        }
    }

    private function getClassName($content)
    {
        preg_match('/class\s+(\w+)/', $content, $matches);
        return $matches[1] ?? null;
    }

    private function analyzeMethod($className, $methodName, $requestParam, $filePath)
    {
        // Skip index, show, edit, create methods (typically don't need validation)
        if (in_array($methodName, ['index', 'show', 'edit', 'create'])) {
            return;
        }

        // Check if using generic Request instead of FormRequest
        if (preg_match('/\bRequest\s+\$\w+/', $requestParam)) {
            $expectedRequestName = $this->generateExpectedRequestName($className, $methodName);
            
            $this->missingRequests[] = [
                'controller' => $className,
                'method' => $methodName,
                'file' => $filePath,
                'expected_request' => $expectedRequestName,
                'current_request' => 'Request (generic)',
                'status' => 'MISSING_FORM_REQUEST'
            ];
        } else {
            // Extract the specific request class name
            preg_match('/(\w+Request)/', $requestParam, $matches);
            $currentRequest = $matches[1] ?? null;
            
            if ($currentRequest && !in_array($currentRequest, $this->existingRequests)) {
                $this->missingRequests[] = [
                    'controller' => $className,
                    'method' => $methodName,
                    'file' => $filePath,
                    'expected_request' => $currentRequest,
                    'current_request' => $currentRequest,
                    'status' => 'REQUEST_FILE_MISSING'
                ];
            }
        }
    }

    private function generateExpectedRequestName($className, $methodName)
    {
        $actionMap = [
            'store' => 'Create',
            'update' => 'Update',
            'destroy' => 'Delete',
            'delete' => 'Delete'
        ];

        $action = $actionMap[$methodName] ?? ucfirst($methodName);
        $entity = str_replace('Controller', '', $className);
        
        return $action . $entity . 'Request';
    }

    public function generateReport()
    {
        echo "=== CONTROLLER REQUEST FILE COVERAGE ANALYSIS ===\n\n";
        
        if (empty($this->missingRequests)) {
            echo "✅ All controller methods have appropriate request files!\n";
            return;
        }

        echo "❌ Found " . count($this->missingRequests) . " methods that need request files:\n\n";

        $groupedByStatus = [];
        foreach ($this->missingRequests as $missing) {
            $groupedByStatus[$missing['status']][] = $missing;
        }

        foreach ($groupedByStatus as $status => $items) {
            echo "--- " . str_replace('_', ' ', $status) . " ---\n";
            
            foreach ($items as $item) {
                echo "• {$item['controller']}::{$item['method']}()\n";
                echo "  Expected: {$item['expected_request']}\n";
                echo "  Current: {$item['current_request']}\n";
                echo "  File: {$item['file']}\n\n";
            }
        }
    }

    public function generateCreateCommands()
    {
        echo "\n=== ARTISAN COMMANDS TO CREATE MISSING REQUEST FILES ===\n\n";
        
        $commands = [];
        foreach ($this->missingRequests as $missing) {
            if ($missing['status'] === 'MISSING_FORM_REQUEST' || $missing['status'] === 'REQUEST_FILE_MISSING') {
                $commands[] = "php artisan make:request {$missing['expected_request']}";
            }
        }
        
        $commands = array_unique($commands);
        foreach ($commands as $command) {
            echo $command . "\n";
        }
    }

    public function generateRequestTemplate($requestName)
    {
        $template = "<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {$requestName} extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Add your validation rules here
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Add custom error messages here
        ];
    }
}
";
        return $template;
    }
}

// Run the analysis
$analyzer = new RequestCoverageAnalyzer();
$analyzer->analyzeControllers();
$analyzer->generateReport();
$analyzer->generateCreateCommands();

echo "\n=== SUMMARY ===\n";
echo "Analysis complete! Check the output above for missing request files.\n";
echo "Use the artisan commands provided to create the missing request files.\n";
echo "Remember to add appropriate validation rules to each request file.\n"; 