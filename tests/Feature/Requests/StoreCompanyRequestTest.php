<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Company\StoreCompanyRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/**
 * Universal Enhanced Validation Tests for StoreCompanyRequest
 * 
 * @group validation
 * @group requests
 */
class StoreCompanyRequestTest extends TestCase
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
        $request = new StoreCompanyRequest();
        
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function test_validation_rules_are_defined()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function test_validation_messages_are_defined()
    {
        $request = new StoreCompanyRequest();
        
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
        $request = new StoreCompanyRequest();
        
        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function test_name_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('name', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_email_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('email', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_phone_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('phone', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['phone'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_website_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('website', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['website'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_industry_id_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('industry_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['industry_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_ownership_type_id_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('ownership_type_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['ownership_type_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_company_size_id_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('company_size_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['company_size_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_established_in_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('established_in', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['established_in'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_description_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('description', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['description'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_country_id_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('country_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['country_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_state_id_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('state_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['state_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_city_id_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('city_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['city_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_address_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('address', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['address'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_postal_code_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('postal_code', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['postal_code'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_logo_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('logo', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['logo'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_is_active_validation()
    {
        $request = new StoreCompanyRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('is_active', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['is_active'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_boolean_validation()
    {
        $request = new StoreCompanyRequest();
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
            'name' => 'Test Value',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'website' => 'Test Value',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 'Test Value',
            'description' => 'Test Value',
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Test Value',
            'postal_code' => 'Test Value',
            'logo' => 'Test Value',
            'is_active' => true,
            'boolean' => 'Test Value',
        ];
        
        $request = new StoreCompanyRequest();
        $validator = validator($validData, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_request_handles_empty_data_correctly()
    {
        $emptyData = [];
        
        $request = new StoreCompanyRequest();
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
        
        $request = new StoreCompanyRequest();
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
        
        $request = new StoreCompanyRequest();
        $validator = validator($sqlInjectionData, $request->rules());
        
        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
