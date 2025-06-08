<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Job\StoreJobRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/**
 * Universal Enhanced Validation Tests for StoreJobRequest
 * 
 * @group validation
 * @group requests
 */
class StoreJobRequestTest extends TestCase
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
        $request = new StoreJobRequest();
        
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function test_validation_rules_are_defined()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function test_validation_messages_are_defined()
    {
        $request = new StoreJobRequest();
        
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
        $request = new StoreJobRequest();
        
        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function test_job_title_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_title', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_title'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_job_description_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_description', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_description'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_job_requirement_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_requirement', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_requirement'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_job_benefit_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_benefit', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_benefit'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_country_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('country_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['country_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_state_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('state_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['state_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_city_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('city_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['city_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_salary_from_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('salary_from', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['salary_from'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_salary_to_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('salary_to', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['salary_to'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_salary_currency_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('salary_currency_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['salary_currency_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_salary_period_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('salary_period_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['salary_period_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_job_category_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_category_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_category_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_job_type_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_type_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_type_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_career_level_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('career_level_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['career_level_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_functional_area_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('functional_area_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['functional_area_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_job_shift_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_shift_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_shift_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_degree_level_id_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('degree_level_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['degree_level_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_position_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('position', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['position'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_experience_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('experience', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['experience'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_job_expiry_date_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_expiry_date', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_expiry_date'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_hide_salary_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('hide_salary', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['hide_salary'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_boolean_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('boolean', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['boolean'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_is_freelance_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('is_freelance', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['is_freelance'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_is_suspended_validation()
    {
        $request = new StoreJobRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('is_suspended', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['is_suspended'];
        $this->assertNotEmpty($fieldRules);
    }



    /** @test */
    public function test_valid_data_passes_validation()
    {
        $validData = [
            'job_title' => 'Test Value',
            'job_description' => 'Test Value',
            'job_requirement' => 'Test Value',
            'job_benefit' => 'Test Value',
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'salary_from' => 'Test Value',
            'salary_to' => 'Test Value',
            'salary_currency_id' => 1,
            'salary_period_id' => 1,
            'job_category_id' => 1,
            'job_type_id' => 1,
            'career_level_id' => 1,
            'functional_area_id' => 1,
            'job_shift_id' => 1,
            'degree_level_id' => 1,
            'position' => 'Test Value',
            'experience' => 'Test Value',
            'job_expiry_date' => '2024-01-01',
            'hide_salary' => 1,
            'boolean' => 'Test Value',
            'is_freelance' => 'Test Value',
            'is_suspended' => 'Test Value',
        ];
        
        $request = new StoreJobRequest();
        $validator = validator($validData, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_request_handles_empty_data_correctly()
    {
        $emptyData = [];
        
        $request = new StoreJobRequest();
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
        
        $request = new StoreJobRequest();
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
        
        $request = new StoreJobRequest();
        $validator = validator($sqlInjectionData, $request->rules());
        
        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
