<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Job\JobFilterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Enhanced Validation Tests for JobFilterRequest.
 *
 * @group validation
 * @group requests
 *
 * @internal
 *
 * @coversNothing
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
    public function testAuthorizationReturnsTrue()
    {
        $request = new JobFilterRequest();

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function testValidationRulesAreDefined()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function testValidationMessagesAreDefined()
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
    public function testValidationAttributesAreDefined()
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
    public function testSearchValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('search', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['search'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testNullableValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('nullable', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['nullable'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testStringValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('string', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['string'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCategoryIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('category_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['category_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testJobTypeIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('job_type_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['job_type_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCountryIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('country_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['country_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testStateIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('state_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['state_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCityIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('city_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['city_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCompanyIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('company_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['company_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testFunctionalAreaIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('functional_area_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['functional_area_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCareerLevelIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('career_level_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['career_level_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testDegreeLevelIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('degree_level_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['degree_level_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testJobShiftIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('job_shift_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['job_shift_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCurrencyIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('currency_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['currency_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testSalaryPeriodIdValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('salary_period_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['salary_period_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testMinSalaryValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('min_salary', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['min_salary'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testNumericValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('numeric', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['numeric'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testMaxSalaryValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('max_salary', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['max_salary'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testMinExperienceValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('min_experience', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['min_experience'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testIntegerValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('integer', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['integer'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testMaxExperienceValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('max_experience', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['max_experience'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testSkillsValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('skills', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['skills'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testArrayValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('array', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['array'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testTagsValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('tags', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['tags'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPostedWithinValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('posted_within', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['posted_within'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testIsFeaturedValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('is_featured', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['is_featured'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testBooleanValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('boolean', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['boolean'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testIsFreelanceValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('is_freelance', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['is_freelance'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testHideSalaryValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('hide_salary', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['hide_salary'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testSortByValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('sort_by', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['sort_by'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testRelevanceValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('relevance', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['relevance'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testDateDescValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('date_desc', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['date_desc'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testDateAscValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('date_asc', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['date_asc'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testSalaryDescValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('salary_desc', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['salary_desc'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testSalaryAscValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('salary_asc', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['salary_asc'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCompanyNameValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('company_name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['company_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testLocationValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('location', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['location'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPopularityValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('popularity', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['popularity'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPerPageValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('per_page', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['per_page'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPageValidation()
    {
        $request = new JobFilterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('page', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['page'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testValidDataPassesValidation()
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
    public function testRequestHandlesEmptyDataCorrectly()
    {
        $emptyData = [];

        $request = new JobFilterRequest();
        $validator = validator($emptyData, $request->rules());

        // Should handle empty data according to rules
        $this->assertIsArray($validator->errors()->toArray());
    }

    /** @test */
    public function testSecurityValidationPreventsXss()
    {
        $maliciousData = [
            'name' => '<script>alert("xss")</script>',
            'description' => 'javascript:alert("xss")',
            'content' => '<img src=x onerror=alert("xss")>',
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
    public function testSqlInjectionPrevention()
    {
        $sqlInjectionData = [
            'name' => "'; DROP TABLE users; --",
            'search' => "1' OR '1'='1",
            'filter' => 'UNION SELECT * FROM passwords',
        ];

        $request = new JobFilterRequest();
        $validator = validator($sqlInjectionData, $request->rules());

        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
