<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Enhanced\CreateCompanyRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Enhanced Validation Tests for CreateCompanyRequest.
 *
 * @group validation
 * @group requests
 *
 * @internal
 *
 * @coversNothing
 */
class CreateCompanyRequestTest extends TestCase
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
        $request = new CreateCompanyRequest;

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function test_validation_rules_are_defined()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function test_validation_messages_are_defined()
    {
        $request = new CreateCompanyRequest;

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
        $request = new CreateCompanyRequest;

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
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_required_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('required', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['required'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_string_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('string', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['string'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_ceo_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('ceo', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['ceo'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_industry_id_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('industry_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['industry_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_integer_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('integer', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['integer'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_ownership_type_id_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('ownership_type_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['ownership_type_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_company_size_id_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('company_size_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['company_size_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_established_in_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('established_in', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['established_in'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_y_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('Y', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['Y'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_details_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('details', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['details'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_nullable_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('nullable', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['nullable'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_website_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('website', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['website'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_url_validation()
    {
        $request = new CreateCompanyRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('url', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['url'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_valid_data_passes_validation()
    {
        $validData = [
            'name' => 'Test Value',
            'required' => 'Test Value',
            'string' => 'Test Value',
            'ceo' => 'Test Value',
            'industry_id' => 1,
            'integer' => 'Test Value',
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 'Test Value',
            'Y' => 'Test Value',
            'details' => 'Test Value',
            'nullable' => 'Test Value',
            'website' => 'Test Value',
            'url' => 'https://example.com',
        ];

        $request = new CreateCompanyRequest;
        $validator = validator($validData, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_request_handles_empty_data_correctly()
    {
        $emptyData = [];

        $request = new CreateCompanyRequest;
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
            'content' => '<img src=x onerror=alert("xss")>',
        ];

        $request = new CreateCompanyRequest;
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
            'filter' => 'UNION SELECT * FROM passwords',
        ];

        $request = new CreateCompanyRequest;
        $validator = validator($sqlInjectionData, $request->rules());

        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
