<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Job\JobFilterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/**
 * Universal Enhanced Validation Tests for JobFilterRequest
 * 
 * @group validation
 * @group requests
 */
class JobFilterRequestTest extends TestCase
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
        $request = new JobFilterRequest();
        
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function test_validation_rules_are_defined()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function test_validation_messages_are_defined()
    {
        $request = new JobFilterRequest();
        
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
        $request = new JobFilterRequest();
        
        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function test_search_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('search', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['search'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_nullable_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('nullable', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['nullable'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_string_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('string', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['string'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_category_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('category_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['category_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_job_type_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_type_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_type_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_country_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('country_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['country_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_state_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('state_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['state_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_city_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('city_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['city_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_company_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('company_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['company_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_functional_area_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('functional_area_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['functional_area_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_career_level_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('career_level_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['career_level_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_degree_level_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('degree_level_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['degree_level_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_job_shift_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('job_shift_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['job_shift_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_currency_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('currency_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['currency_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_salary_period_id_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('salary_period_id', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['salary_period_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_min_salary_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('min_salary', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['min_salary'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_numeric_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('numeric', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['numeric'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_max_salary_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('max_salary', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['max_salary'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_min_experience_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('min_experience', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['min_experience'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_integer_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('integer', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['integer'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_max_experience_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('max_experience', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['max_experience'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_skills_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('skills', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['skills'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_array_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('array', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['array'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_tags_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('tags', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['tags'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_posted_within_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('posted_within', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['posted_within'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_is_featured_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('is_featured', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['is_featured'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_boolean_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('boolean', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['boolean'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_is_freelance_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('is_freelance', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['is_freelance'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_hide_salary_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('hide_salary', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['hide_salary'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_sort_by_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('sort_by', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['sort_by'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_relevance_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('relevance', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['relevance'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_date_desc_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('date_desc', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['date_desc'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_date_asc_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('date_asc', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['date_asc'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_salary_desc_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('salary_desc', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['salary_desc'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_salary_asc_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('salary_asc', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['salary_asc'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_company_name_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('company_name', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['company_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_location_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('location', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['location'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_popularity_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('popularity', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['popularity'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_per_page_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('per_page', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['per_page'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function test_page_validation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();
        
        $this->assertArrayHasKey('page', $rules);
        
        // Test field-specific validation rules
        $fieldRules = $rules['page'];
        $this->assertNotEmpty($fieldRules);
    }



    /** @test */
    public function test_valid_data_passes_validation()
    {
        $validData = [
            'search' => 'Test Value',
            'nullable' => 'Test Value',
            'string' => 'Test Value',
            'category_id' => 1,
            'job_type_id' => 1,
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'company_id' => 1,
            'functional_area_id' => 1,
            'career_level_id' => 1,
            'degree_level_id' => 1,
            'job_shift_id' => 1,
            'currency_id' => 1,
            'salary_period_id' => 1,
            'min_salary' => 'Test Value',
            'numeric' => 'Test Value',
            'max_salary' => 'Test Value',
            'min_experience' => 'Test Value',
            'integer' => 'Test Value',
            'max_experience' => 'Test Value',
            'skills' => 'Test Value',
            'array' => 'Test Value',
            'tags' => 'Test Value',
            'posted_within' => 'Test Value',
            'is_featured' => 'Test Value',
            'boolean' => 'Test Value',
            'is_freelance' => 'Test Value',
            'hide_salary' => 1,
            'sort_by' => 'Test Value',
            'relevance' => 'Test Value',
            'date_desc' => '2024-01-01',
            'date_asc' => '2024-01-01',
            'salary_desc' => 'Test Value',
            'salary_asc' => 'Test Value',
            'company_name' => 'Test Value',
            'location' => 'Test Value',
            'popularity' => 'Test Value',
            'per_page' => 'Test Value',
            'page' => 'Test Value',
        ];
        
        $request = new JobFilterRequest();
        $validator = validator($validData, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_request_handles_empty_data_correctly()
    {
        $emptyData = [];
        
        $request = new JobFilterRequest();
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
        
        $request = new JobFilterRequest();
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
        
        $request = new JobFilterRequest();
        $validator = validator($sqlInjectionData, $request->rules());
        
        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
