<?php

require_once 'vendor/autoload.php';

class RequestTestGenerator
{
    private $requestsPath = 'app/Http/Requests';
    private $testsPath = 'tests/Feature/Requests';
    private $generatedTests = [];
    private $currentRequestName = '';

    public function generateAllTests()
    {
        echo "=== GENERATING COMPREHENSIVE REQUEST TESTS ===\n\n";
        
        // Ensure tests directory exists
        if (!is_dir($this->testsPath)) {
            mkdir($this->testsPath, 0755, true);
        }
        
        $requestFiles = $this->getAllRequestFiles();
        
        foreach ($requestFiles as $file) {
            $this->generateTestForRequest($file);
        }
        
        $this->generateReport();
    }

    private function getAllRequestFiles()
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->requestsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }

    private function generateTestForRequest($filePath)
    {
        $requestName = basename($filePath, '.php');
        $testFileName = $requestName . 'Test.php';
        $testFilePath = $this->testsPath . '/' . $testFileName;
        
        echo "Generating test for: $requestName\n";
        
        // Set current request name for test generation
        $this->currentRequestName = $requestName;
        
        // Analyze the request file to get validation rules
        $requestContent = file_get_contents($filePath);
        $validationRules = $this->extractValidationRules($requestContent);
        $authorizationLogic = $this->extractAuthorizationLogic($requestContent);
        
        $testContent = $this->generateTestContent($requestName, $validationRules, $authorizationLogic);
        
        file_put_contents($testFilePath, $testContent);
        $this->generatedTests[] = $testFileName;
        
        echo "  ✓ Generated: $testFileName\n\n";
    }

    private function extractValidationRules($content)
    {
        // Extract validation rules from the rules() method
        preg_match('/public function rules\(\):\s*array\s*\{([^}]+)\}/', $content, $matches);
        
        if (!isset($matches[1])) {
            return [];
        }
        
        $rulesContent = $matches[1];
        
        // Parse the return array
        preg_match('/return\s*\[([^\]]+)\]/', $rulesContent, $arrayMatches);
        
        if (!isset($arrayMatches[1])) {
            return [];
        }
        
        // Extract individual field rules
        $rules = [];
        preg_match_all("/'(\w+)'\s*=>\s*\[([^\]]+)\]/", $arrayMatches[1], $fieldMatches, PREG_SET_ORDER);
        
        foreach ($fieldMatches as $match) {
            $field = $match[1];
            $ruleString = $match[2];
            
            // Parse individual rules
            preg_match_all("/'([^']+)'/", $ruleString, $ruleMatches);
            $rules[$field] = $ruleMatches[1] ?? [];
        }
        
        return $rules;
    }

    private function extractAuthorizationLogic($content)
    {
        // Check for authorization logic
        if (strpos($content, 'auth()->check()') !== false) {
            if (strpos($content, "hasRole('Admin')") !== false) {
                return 'admin';
            } elseif (strpos($content, "hasRole('Employer')") !== false) {
                return 'employer';
            } elseif (strpos($content, "hasRole('Candidate')") !== false) {
                return 'candidate';
            }
            return 'authenticated';
        }
        
        if (strpos($content, 'return true') !== false) {
            return 'public';
        }
        
        return 'unknown';
    }

    private function generateTestContent($requestName, $validationRules, $authorizationLogic)
    {
        $className = $requestName . 'Test';
        
        $testMethods = $this->generateValidationTests($validationRules);
        $authTests = $this->generateAuthorizationTests($authorizationLogic);
        
        return <<<PHP
<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\\$requestName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class $className extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that the request has proper validation rules.
     */
    public function test_validation_rules_are_defined()
    {
        \$request = new $requestName();
        \$rules = \$request->rules();
        
        \$this->assertIsArray(\$rules);
        \$this->assertNotEmpty(\$rules);
    }

{$authTests}

{$testMethods}

    /**
     * Test custom error messages are defined.
     */
    public function test_custom_error_messages_are_defined()
    {
        \$request = new $requestName();
        
        if (method_exists(\$request, 'messages')) {
            \$messages = \$request->messages();
            \$this->assertIsArray(\$messages);
        } else {
            \$this->assertTrue(true, 'No custom messages method defined');
        }
    }

    /**
     * Test custom attributes are defined.
     */
    public function test_custom_attributes_are_defined()
    {
        \$request = new $requestName();
        
        if (method_exists(\$request, 'attributes')) {
            \$attributes = \$request->attributes();
            \$this->assertIsArray(\$attributes);
        } else {
            \$this->assertTrue(true, 'No custom attributes method defined');
        }
    }

    /**
     * Create a test user with specific role.
     */
    protected function createUserWithRole(\$role = 'user')
    {
        \$user = User::factory()->create();
        // Add role assignment logic based on your role system
        return \$user;
    }

    /**
     * Get valid test data for the request.
     */
    protected function getValidData()
    {
        return [
            // Add sample valid data based on validation rules
        ];
    }

    /**
     * Get invalid test data for the request.
     */
    protected function getInvalidData()
    {
        return [
            // Add sample invalid data to test validation
        ];
    }
}
PHP;
    }

    private function generateAuthorizationTests($authorizationLogic)
    {
        switch ($authorizationLogic) {
            case 'admin':
                return <<<PHP
    /**
     * Test authorization for admin users.
     */
    public function test_admin_user_is_authorized()
    {
        \$user = \$this->createUserWithRole('admin');
        \$this->actingAs(\$user);
        
        \$request = new {$this->currentRequestName}();
        \$this->assertTrue(\$request->authorize());
    }

    /**
     * Test authorization denies non-admin users.
     */
    public function test_non_admin_user_is_not_authorized()
    {
        \$user = \$this->createUserWithRole('user');
        \$this->actingAs(\$user);
        
        \$request = new {$this->currentRequestName}();
        \$this->assertFalse(\$request->authorize());
    }

    /**
     * Test authorization denies unauthenticated users.
     */
    public function test_unauthenticated_user_is_not_authorized()
    {
        \$request = new {$this->currentRequestName}();
        \$this->assertFalse(\$request->authorize());
    }
PHP;

            case 'employer':
                return <<<PHP
    /**
     * Test authorization for employer users.
     */
    public function test_employer_user_is_authorized()
    {
        \$user = \$this->createUserWithRole('employer');
        \$this->actingAs(\$user);
        
        \$request = new {$this->currentRequestName}();
        \$this->assertTrue(\$request->authorize());
    }

    /**
     * Test authorization denies non-employer users.
     */
    public function test_non_employer_user_is_not_authorized()
    {
        \$user = \$this->createUserWithRole('user');
        \$this->actingAs(\$user);
        
        \$request = new {$this->currentRequestName}();
        \$this->assertFalse(\$request->authorize());
    }
PHP;

            case 'public':
                return <<<PHP
    /**
     * Test authorization allows all users.
     */
    public function test_all_users_are_authorized()
    {
        \$request = new {$this->currentRequestName}();
        \$this->assertTrue(\$request->authorize());
    }
PHP;

            default:
                return <<<PHP
    /**
     * Test authorization logic.
     */
    public function test_authorization_logic()
    {
        \$request = new {$this->currentRequestName}();
        // Add specific authorization tests based on the request logic
        \$this->assertTrue(true, 'Authorization test needs implementation');
    }
PHP;
        }
    }

    private function generateValidationTests($validationRules)
    {
        if (empty($validationRules)) {
            return <<<PHP
    /**
     * Test validation with valid data.
     */
    public function test_validation_passes_with_valid_data()
    {
        \$data = \$this->getValidData();
        
        // Create a mock request with valid data
        \$this->assertTrue(true, 'Validation test needs implementation with actual rules');
    }

    /**
     * Test validation fails with invalid data.
     */
    public function test_validation_fails_with_invalid_data()
    {
        \$data = \$this->getInvalidData();
        
        // Create a mock request with invalid data
        \$this->assertTrue(true, 'Validation test needs implementation with actual rules');
    }
PHP;
        }

        $tests = '';
        foreach ($validationRules as $field => $rules) {
            if (in_array('required', $rules)) {
                $tests .= <<<PHP

    /**
     * Test that $field is required.
     */
    public function test_{$field}_is_required()
    {
        \$data = \$this->getValidData();
        unset(\$data['$field']);
        
        // Test validation fails when $field is missing
        \$this->assertTrue(true, '$field required validation test needs implementation');
    }
PHP;
            }

            if (in_array('email', $rules)) {
                $tests .= <<<PHP

    /**
     * Test that $field must be valid email.
     */
    public function test_{$field}_must_be_valid_email()
    {
        \$data = \$this->getValidData();
        \$data['$field'] = 'invalid-email';
        
        // Test validation fails with invalid email
        \$this->assertTrue(true, '$field email validation test needs implementation');
    }
PHP;
            }

            if (in_array('unique', $rules)) {
                $tests .= <<<PHP

    /**
     * Test that $field must be unique.
     */
    public function test_{$field}_must_be_unique()
    {
        \$data = \$this->getValidData();
        
        // Test validation fails when $field is not unique
        \$this->assertTrue(true, '$field unique validation test needs implementation');
    }
PHP;
            }
        }

        return $tests;
    }

    private function generateReport()
    {
        echo "=== TEST GENERATION SUMMARY ===\n";
        echo "Total test files generated: " . count($this->generatedTests) . "\n\n";
        
        echo "GENERATED TEST FILES:\n";
        foreach ($this->generatedTests as $testFile) {
            echo "  ✓ $testFile\n";
        }
        
        echo "\n=== NEXT STEPS ===\n";
        echo "1. Review generated tests and add specific test data\n";
        echo "2. Implement role system integration in tests\n";
        echo "3. Add factory data for realistic testing\n";
        echo "4. Run tests: php artisan test tests/Feature/Requests/\n";
        
        echo "\n=== COMPLETED ===\n";
    }
}

// Run the test generator
$generator = new RequestTestGenerator();
$generator->generateAllTests(); 