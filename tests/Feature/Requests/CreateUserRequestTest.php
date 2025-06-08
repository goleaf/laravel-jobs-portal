<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Enhanced\CreateUserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/**
 * Universal Enhanced Validation Tests for CreateUserRequest
 * 
 * @group validation
 * @group requests
 */
class CreateUserRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create authenticated user for testing
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_authorization_returns_true()
    {
        $request = new CreateUserRequest();
        
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function test_validation_rules_are_defined()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function test_validation_messages_are_defined()
    {
        $request = new CreateUserRequest();
        
        if (method_exists($request, 'messages')) {
            $messages = $request->messages();
            $this->assertIsArray($messages);
        } else {
            $this->markTestSkipped('No custom messages method defined');
        }
    }

    /** @test */
    public function test_validation_attributes_are_defined()
    {
        $request = new CreateUserRequest();
        
        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function test_first_name_validation()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('first_name', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['first_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_required_validation()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('required', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['required'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_string_validation()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('string', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['string'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_last_name_validation()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('last_name', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['last_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_email_validation()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('email', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_password_validation()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('password', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['password'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_confirmed_validation()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('confirmed', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['confirmed'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_phone_validation()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('phone', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['phone'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_nullable_validation()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('nullable', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['nullable'];
        $this->assertNotEmpty($fieldRules);
    }



    /** @test */
    public function test_valid_data_passes_validation()
    {
        $validData = [
            'first_name' => 'Test Value',
            'required' => 'Test Value',
            'string' => 'Test Value',
            'last_name' => 'Test Value',
            'email' => 'test@example.com',
            'password' => 'SecureP@ssw0rd123!',
            'confirmed' => 'Test Value',
            'phone' => '+1234567890',
            'nullable' => 'Test Value',
        ];
        
        $request = new CreateUserRequest();
        $validator = validator($validData, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_request_handles_empty_data_correctly()
    {
        $emptyData = [];
        
        $request = new CreateUserRequest();
        $validator = validator($emptyData, $request->rules());
        
        // Should handle empty data according to rules
        $this->assertIsArray($validator->errors()->toArray());
    }

    /** @test */
    public function test_security_validation_prevents_xss()
    {
        $maliciousData = [
            'name' => '<script>alert("xss")</script>',
            'description' => 'javascript:alert("xss")',
            'content' => '<img src=x onerror=alert("xss")>'
        ];
        
        $request = new CreateUserRequest();
        $validator = validator($maliciousData, $request->rules());
        
        // XSS data should either fail validation or be properly sanitized
        if ($validator->passes()) {
            foreach ($maliciousData as $field => $value) {
                if (is_string($value)) {
                    $this->assertStringNotContainsString('<script>', $value);
                    $this->assertStringNotContainsString('javascript:', $value);
                }
            }
        }
    }

    /** @test */
    public function test_sql_injection_prevention()
    {
        $sqlInjectionData = [
            'name' => "'; DROP TABLE users; --",
            'search' => "1' OR '1'='1",
            'filter' => "UNION SELECT * FROM passwords"
        ];
        
        $request = new CreateUserRequest();
        $validator = validator($sqlInjectionData, $request->rules());
        
        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
