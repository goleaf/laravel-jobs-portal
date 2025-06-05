<?php

require_once 'vendor/autoload.php';

class ControllerRequestUpdater
{
    private $controllersPath = 'app/Http/Controllers';
    private $requestsPath = 'app/Http/Requests';
    private $updates = [];
    private $errors = [];

    public function updateAllControllers()
    {
        echo "=== UPDATING CONTROLLERS TO USE SPECIFIC REQUEST FILES ===\n\n";
        
        $controllerFiles = $this->getAllControllerFiles();
        $requestFiles = $this->getAvailableRequests();
        
        foreach ($controllerFiles as $file) {
            $this->updateControllerFile($file, $requestFiles);
        }
        
        $this->generateReport();
    }

    private function getAllControllerFiles()
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->controllersPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }

    private function getAvailableRequests()
    {
        $requests = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->requestsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $requestName = basename($file->getPathname(), '.php');
                $requests[] = $requestName;
            }
        }
        
        return $requests;
    }

    private function updateControllerFile($filePath, $availableRequests)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fileName = basename($filePath);
        
        echo "Processing: $fileName\n";
        
        // Find methods that use generic Request
        $pattern = '/public function (\w+)\([^)]*Request \$request[^)]*\)/';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $methodName = $match[1];
            $fullMatch = $match[0];
            
            // Determine appropriate request class
            $requestClass = $this->determineRequestClass($fileName, $methodName, $availableRequests);
            
            if ($requestClass && $requestClass !== 'Request') {
                // Update the method signature
                $newSignature = str_replace('Request $request', $requestClass . ' $request', $fullMatch);
                $content = str_replace($fullMatch, $newSignature, $content);
                
                // Add use statement
                $content = $this->addUseStatement($content, $requestClass);
                
                $this->updates[] = [
                    'file' => $fileName,
                    'method' => $methodName,
                    'from' => 'Request',
                    'to' => $requestClass
                ];
                
                echo "  ✓ Updated $methodName: Request -> $requestClass\n";
            }
        }
        
        // Save the updated file if changes were made
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            echo "  📝 File updated with " . count(array_filter($this->updates, function($u) use ($fileName) {
                return $u['file'] === $fileName;
            })) . " changes\n";
        } else {
            echo "  ➖ No changes needed\n";
        }
        
        echo "\n";
    }

    private function determineRequestClass($fileName, $methodName, $availableRequests)
    {
        // Controller-specific mapping
        $controllerMappings = [
            'AdminController.php' => [
                'store' => 'CreateAdminRequest',
                'update' => 'UpdateAdminRequest',
            ],
            'JobController.php' => [
                'store' => 'CreateJobRequest',
                'update' => 'UpdateJobRequest',
            ],
            'CompanyController.php' => [
                'store' => 'CreateCompanyRequest',
                'update' => 'UpdateCompanyRequest',
            ],
            'CandidateController.php' => [
                'store' => 'CreateCandidateRequest',
                'update' => 'UpdateCandidateRequest',
            ],
        ];

        // Check specific mappings first
        if (isset($controllerMappings[$fileName][$methodName])) {
            $requestClass = $controllerMappings[$fileName][$methodName];
            if (in_array($requestClass, $availableRequests)) {
                return $requestClass;
            }
        }

        // Generate request class name based on patterns
        $controllerBase = str_replace('Controller.php', '', $fileName);
        $possibleRequests = [
            ucfirst($methodName) . $controllerBase . 'Request',
            $controllerBase . ucfirst($methodName) . 'Request',
            ucfirst($methodName) . 'Request',
        ];

        foreach ($possibleRequests as $requestClass) {
            if (in_array($requestClass, $availableRequests)) {
                return $requestClass;
            }
        }

        return null;
    }

    private function addUseStatement($content, $requestClass)
    {
        $useStatement = "use App\\Http\\Requests\\$requestClass;";
        
        // Check if use statement already exists
        if (strpos($content, $useStatement) !== false) {
            return $content;
        }
        
        // Find the namespace line and add use statement after it
        $lines = explode("\n", $content);
        $newLines = [];
        $useAdded = false;
        
        foreach ($lines as $line) {
            $newLines[] = $line;
            
            if (!$useAdded && (strpos($line, 'namespace ') === 0 || strpos($line, 'use ') === 0)) {
                // Look for the end of use statements
                continue;
            }
            
            if (!$useAdded && strpos($line, 'class ') === 0) {
                // Add use statement before class declaration
                array_splice($newLines, -1, 0, [$useStatement, '']);
                $useAdded = true;
            }
        }
        
        return implode("\n", $newLines);
    }

    private function generateReport()
    {
        echo "=== UPDATE SUMMARY ===\n";
        echo "Total controllers processed: " . count(array_unique(array_column($this->updates, 'file'))) . "\n";
        echo "Total methods updated: " . count($this->updates) . "\n\n";
        
        if (!empty($this->updates)) {
            echo "SUCCESSFUL UPDATES:\n";
            foreach ($this->updates as $update) {
                echo "  {$update['file']}::{$update['method']} -> {$update['to']}\n";
            }
        }
        
        if (!empty($this->errors)) {
            echo "\nERRORS:\n";
            foreach ($this->errors as $error) {
                echo "  $error\n";
            }
        }
        
        echo "\n=== COMPLETED ===\n";
    }
}

// Run the updater
$updater = new ControllerRequestUpdater();
$updater->updateAllControllers(); 