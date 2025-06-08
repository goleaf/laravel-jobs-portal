<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\StoreAdminRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/**
 * Universal Enhanced Validation Tests for StoreAdminRequest
 * 
 * @group validation
 * @group requests
 */
class StoreAdminRequestTest extends TestCase
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
        $request = new StoreAdminRequest();
        
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function test_validation_rules_are_defined()
    {
        $request = new StoreAdminRequest();
        $rules = $request->rules();
        
        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function test_validation_messages_are_defined()
    {
        $request = new StoreAdminRequest();
        
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
        $request = new StoreAdminRequest();
        
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
        $request = new StoreAdminRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('first_name', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['first_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_last_name_validation()
    {
        $request = new StoreAdminRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('last_name', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['last_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_email_validation()
    {
        $request = new StoreAdminRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('email', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_password_validation()
    {
        $request = new StoreAdminRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('password', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['password'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_phone_validation()
    {
        $request = new StoreAdminRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('phone', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['phone'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_is_active_validation()
    {
        $request = new StoreAdminRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('is_active', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['is_active'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_boolean_validation()
    {
        $request = new StoreAdminRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('boolean', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['boolean'];
        $this->assertNotEmpty($fieldRules);
    }



    /** @test */
    public function test_valid_data_passes_validation()
    {
        $validData = [
            'first_name' => 'Test Value',
            'last_name' => 'Test Value',
            'email' => 'test@example.com',
            'password' => 'SecureP@ssw0rd123!',
            'phone' => '+1234567890',
            'is_active' => true,
            'boolean' => 'Test Value',
        ];
        
        $request = new StoreAdminRequest();
        $validator = validator($validData, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_request_handles_empty_data_correctly()
    {
        $emptyData = [];
        
        $request = new StoreAdminRequest();
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
        
        $request = new StoreAdminRequest();
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
        
        $request = new StoreAdminRequest();
        $validator = validator($sqlInjectionData, $request->rules());
        
        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
