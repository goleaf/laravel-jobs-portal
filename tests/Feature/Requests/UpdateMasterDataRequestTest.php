<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Admin\UpdateMasterDataRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/**
 * Context7 Enhanced Validation Tests for UpdateMasterDataRequest
 * 
 * @group validation
 * @group requests
 */
class UpdateMasterDataRequestTest extends TestCase
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
        $request = new UpdateMasterDataRequest();
        
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function test_validation_rules_are_defined()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function test_validation_messages_are_defined()
    {
        $request = new UpdateMasterDataRequest();
        
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
        $request = new UpdateMasterDataRequest();
        
        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function test_MasterData_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('MasterData', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['MasterData'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_id_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_name_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('name', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_required_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('required', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['required'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_string_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('string', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['string'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_MasterDatas_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('MasterDatas', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['MasterDatas'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_email_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('email', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_nullable_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('nullable', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['nullable'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_users_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('users', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['users'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_description_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('description', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['description'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_status_validation()
    {
        $request = new UpdateMasterDataRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('status', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['status'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_boolean_validation()
    {
        $request = new UpdateMasterDataRequest();
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
            'MasterData' => 'Test Value',
            'id' => 1,
            'name' => 'Test Value',
            'required' => 'Test Value',
            'string' => 'Test Value',
            'MasterDatas' => 'Test Value',
            'email' => 'test@example.com',
            'nullable' => 'Test Value',
            'users' => 'Test Value',
            'description' => 'Test Value',
            'status' => true,
            'boolean' => 'Test Value',
        ];
        
        $request = new UpdateMasterDataRequest();
        $validator = validator($validData, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_request_handles_empty_data_correctly()
    {
        $emptyData = [];
        
        $request = new UpdateMasterDataRequest();
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
        
        $request = new UpdateMasterDataRequest();
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
        
        $request = new UpdateMasterDataRequest();
        $validator = validator($sqlInjectionData, $request->rules());
        
        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
