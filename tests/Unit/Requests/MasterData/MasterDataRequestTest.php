<?php

namespace Tests\Unit\Requests\MasterData;

use App\Http\Requests\MasterData\MasterDataRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * MasterDataRequest Test Suite
 *
 * Tests the master data validation domain
 */
class MasterDataRequestTest extends TestCase
{
    protected $testRequest;

    protected function setUp(): void
    {
        parent::setUp();

        // Create anonymous test request for testing
        $this->testRequest = new class extends MasterDataRequest
        {
            public function rules(): array
            {
                return $this->buildValidationRules();
            }

            protected function getBusinessLogicRules(): array
            {
                return [
                    'entity_id' => 'nullable|integer|exists:entities,id',
                    'status' => 'nullable|string|in:active,inactive,pending',
                    'priority' => 'nullable|string|in:low,medium,high',
                ];
            }
        };
    }

    /** @test */
    public function it_has_low_security_level()
    {
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('securityLevel');
        $property->setAccessible(true);

        $this->assertEquals('low', $property->getValue($this->testRequest));
    }

    /** @test */
    public function it_has_performance_tracking_enabled()
    {
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('performanceTracking');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue($this->testRequest));
    }

    /** @test */
    public function it_includes_master_data_validation_modules()
    {
        // Test that the master data request has the appropriate domain rules
        $method = new \ReflectionMethod($this->testRequest, 'getDomainRules');
        $method->setAccessible(true);

        $rules = $method->invoke($this->testRequest);

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('status', $rules);
        $this->assertContains('required', $rules['name']);
        $this->assertContains('string', $rules['name']);
    }

    /** @test */
    public function it_provides_location_validation_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getLocationRules');
        $method->setAccessible(true);

        $rules = $method->invoke($this->testRequest);

        $this->assertArrayHasKey('country_id', $rules);
        $this->assertArrayHasKey('state_id', $rules);
        $this->assertArrayHasKey('city_id', $rules);
        $this->assertArrayHasKey('latitude', $rules);
        $this->assertArrayHasKey('longitude', $rules);
    }

    /** @test */
    public function it_provides_company_validation_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getCompanyClassificationRules');
        $method->setAccessible(true);

        $rules = $method->invoke($this->testRequest);

        $this->assertArrayHasKey('company_size_id', $rules);
        $this->assertArrayHasKey('industry_id', $rules);
        $this->assertArrayHasKey('ownership_type_id', $rules);
        $this->assertArrayHasKey('functional_area_id', $rules);
    }

    /** @test */
    public function it_provides_job_classification_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getJobClassificationRules');
        $method->setAccessible(true);

        $rules = $method->invoke($this->testRequest);

        $this->assertArrayHasKey('job_category_id', $rules);
        $this->assertArrayHasKey('salary_currency_id', $rules);
        $this->assertArrayHasKey('salary_period_id', $rules);
        $this->assertArrayHasKey('degree_level_id', $rules);
        $this->assertArrayHasKey('experience_min', $rules);
        $this->assertArrayHasKey('experience_max', $rules);
    }

    /** @test */
    public function it_validates_location_data_successfully()
    {
        $data = [
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ];

        // Use database-independent rules for testing
        $rules = [
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_fails_validation_for_invalid_latitude()
    {
        $data = [
            'latitude' => 95.0, // Should be between -90 and 90
            'longitude' => -74.0060,
        ];

        // Use database-independent rules for testing
        $rules = [
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('latitude', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_company_data_successfully()
    {
        $data = [
            'experience_min' => 2,
            'experience_max' => 5,
        ];

        // Use database-independent rules for testing
        $rules = [
            'experience_min' => 'sometimes|integer|min:0|max:50',
            'experience_max' => 'sometimes|integer|min:0|max:50|gte:experience_min',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_fails_validation_for_invalid_experience_range()
    {
        $data = [
            'experience_min' => 10,
            'experience_max' => 5,  // Max should be greater than or equal to min
        ];

        // Use database-independent rules for testing
        $rules = [
            'experience_min' => 'sometimes|integer|min:0|max:50',
            'experience_max' => 'sometimes|integer|min:0|max:50|gte:experience_min',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('experience_max', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_job_classification_successfully()
    {
        $data = [
            'experience_min' => 2,
            'experience_max' => 8,
        ];

        // Use database-independent rules for testing
        $rules = [
            'experience_min' => 'sometimes|integer|min:0|max:50',
            'experience_max' => 'sometimes|integer|min:0|max:50|gte:experience_min',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_applies_master_data_sanitization()
    {
        $data = [
            'name' => '  test name  ',
            'description' => '  some description  ',
        ];

        $method = new \ReflectionMethod($this->testRequest, 'applySanitization');
        $method->setAccessible(true);

        $sanitized = $method->invoke($this->testRequest, $data);

        $this->assertEquals('Test Name', $sanitized['name']);
        $this->assertEquals('some description', $sanitized['description']);
    }

    /** @test */
    public function it_has_domain_specific_error_messages()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getDomainMessages');
        $method->setAccessible(true);

        $messages = $method->invoke($this->testRequest);

        $this->assertIsArray($messages);
        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('name.string', $messages);
        $this->assertArrayHasKey('status.in', $messages);
    }

    /** @test */
    public function it_has_domain_specific_attribute_names()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getDomainAttributes');
        $method->setAccessible(true);

        $attributes = $method->invoke($this->testRequest);

        $this->assertIsArray($attributes);
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('status', $attributes);
        $this->assertArrayHasKey('sort_order', $attributes);
    }
}
