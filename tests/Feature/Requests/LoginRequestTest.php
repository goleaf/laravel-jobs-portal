<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/**
 * Universal Enhanced Validation Tests for LoginRequest
 * 
 * @group validation
 * @group requests
 */
class LoginRequestTest extends TestCase
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
        $request = new LoginRequest();
        
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function test_validation_rules_are_defined()
    {
        $request = new LoginRequest();
        $rules = $request->rules();
        
        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function test_validation_messages_are_defined()
    {
        $request = new LoginRequest();
        
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
        $request = new LoginRequest();
        
        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function test_email_validation()
    {
        $request = new LoginRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('email', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_password_validation()
    {
        $request = new LoginRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('password', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['password'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_remember_validation()
    {
        $request = new LoginRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('remember', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['remember'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_boolean_validation()
    {
        $request = new LoginRequest();
        $rules = $request->rules();
        
        // The remember field should have boolean validation
        $this->assertArrayHasKey('remember', $rules);
        
        // Test field-specific validation rules for remember field
        $fieldRules = $rules['remember'];
        $this->assertEquals('boolean', $fieldRules);
    }

    /** @test */
    public function test_valid_data_passes_validation()
    {
        $validData = [
            'email' => 'test@example.com',
            'password' => 'SecureP@ssw0rd123!',
            'remember' => true, // Boolean value for remember field
        ];
        
        $request = new LoginRequest();
        $validator = validator($validData, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_request_handles_empty_data_correctly()
    {
        $emptyData = [];
        
        $request = new LoginRequest();
        $validator = validator($emptyData, $request->rules());
        
        // Should handle empty data according to rules - email and password are required
        $this->assertTrue($validator->fails());
        $this->assertIsArray($validator->errors()->toArray());
    }

    /** @test */
    public function test_security_validation_prevents_xss()
    {
        $maliciousData = [
            'email' => 'test<script>alert("xss")</script>@example.com',
            'password' => 'password<script>alert("xss")</script>',
        ];
        
        $request = new LoginRequest();
        $validator = validator($maliciousData, $request->rules());
        
        // Email validation should fail for malicious content
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** @test */
    public function test_sql_injection_prevention()
    {
        $sqlInjectionData = [
            'email' => "test'; DROP TABLE users; --@example.com",
            'password' => "1' OR '1'='1",
        ];
        
        $request = new LoginRequest();
        $validator = validator($sqlInjectionData, $request->rules());
        
        // SQL injection patterns should be handled safely - email should fail validation
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }
}
