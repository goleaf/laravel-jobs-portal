<?php

namespace Tests\Unit\Requests\MasterData;

use Tests\TestCase;
use App\Http\Requests\MasterData\MasterDataRequest;
use Illuminate\Support\Facades\Validator;

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
        $this->testRequest = new class extends MasterDataRequest {
            public function rules(): array
            {
                return $this->buildValidationRules();
            }
        };
    }

    /** @test */
    public function it_has_high_security_level()
    {
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('securityLevel');
        $property->setAccessible(true);
        
        $this->assertEquals('high', $property->getValue($this->testRequest));
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
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('validationModules');
        $property->setAccessible(true);
        
        $modules = $property->getValue($this->testRequest);
        
        $this->assertContains('location_validation', $modules);
        $this->assertContains('company_validation', $modules);
        $this->assertContains('job_classification', $modules);
        $this->assertContains('data_integrity', $modules);
    }

    /** @test */
    public function it_provides_location_validation_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getLocationRules');
        $method->setAccessible(true);
        
        $rules = $method->invoke($this->testRequest);
        
        $this->assertArrayHasKey('country_code', $rules);
        $this->assertArrayHasKey('city_name', $rules);
        $this->assertArrayHasKey('postal_code', $rules);
        $this->assertContains('required', $rules['country_code']);
        $this->assertContains('size:2', $rules['country_code']);
    }

    /** @test */
    public function it_provides_company_validation_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getCompanyRules');
        $method->setAccessible(true);
        
        $rules = $method->invoke($this->testRequest);
        
        $this->assertArrayHasKey('company_name', $rules);
        $this->assertArrayHasKey('company_size', $rules);
        $this->assertArrayHasKey('industry_id', $rules);
        $this->assertContains('required', $rules['company_name']);
        $this->assertContains('max:255', $rules['company_name']);
    }

    /** @test */
    public function it_provides_job_classification_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getJobClassificationRules');
        $method->setAccessible(true);
        
        $rules = $method->invoke($this->testRequest);
        
        $this->assertArrayHasKey('job_title', $rules);
        $this->assertArrayHasKey('job_category_id', $rules);
        $this->assertArrayHasKey('experience_level', $rules);
        $this->assertContains('required', $rules['job_title']);
    }

    /** @test */
    public function it_validates_location_data_successfully()
    {
        $data = [
            'country_code' => 'US',
            'city_name' => 'New York',
            'postal_code' => '10001'
        ];

        $method = new \ReflectionMethod($this->testRequest, 'getLocationRules');
        $method->setAccessible(true);
        $rules = $method->invoke($this->testRequest);

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_fails_validation_for_invalid_country_code()
    {
        $data = [
            'country_code' => 'USA', // Should be 2 characters
            'city_name' => 'New York',
            'postal_code' => '10001'
        ];

        $method = new \ReflectionMethod($this->testRequest, 'getLocationRules');
        $method->setAccessible(true);
        $rules = $method->invoke($this->testRequest);

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('country_code', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_company_data_successfully()
    {
        $data = [
            'company_name' => 'Test Company Ltd',
            'company_size' => 'medium',
            'industry_id' => 1
        ];

        $method = new \ReflectionMethod($this->testRequest, 'getCompanyRules');
        $method->setAccessible(true);
        $rules = $method->invoke($this->testRequest);

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_fails_validation_for_invalid_company_size()
    {
        $data = [
            'company_name' => 'Test Company',
            'company_size' => 'invalid_size',
            'industry_id' => 1
        ];

        $method = new \ReflectionMethod($this->testRequest, 'getCompanyRules');
        $method->setAccessible(true);
        $rules = $method->invoke($this->testRequest);

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('company_size', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_job_classification_successfully()
    {
        $data = [
            'job_title' => 'Software Engineer',
            'job_category_id' => 1,
            'experience_level' => 'mid'
        ];

        $method = new \ReflectionMethod($this->testRequest, 'getJobClassificationRules');
        $method->setAccessible(true);
        $rules = $method->invoke($this->testRequest);

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_applies_master_data_sanitization()
    {
        $data = [
            'company_name' => '  Test Company  ',
            'city_name' => 'new york',
            'country_code' => 'us'
        ];

        $method = new \ReflectionMethod($this->testRequest, 'applySanitization');
        $method->setAccessible(true);
        
        $sanitized = $method->invoke($this->testRequest, $data);
        
        $this->assertEquals('Test Company', $sanitized['company_name']);
        $this->assertEquals('New York', $sanitized['city_name']);
        $this->assertEquals('US', $sanitized['country_code']);
    }

    /** @test */
    public function it_has_domain_specific_error_messages()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getDomainMessages');
        $method->setAccessible(true);
        
        $messages = $method->invoke($this->testRequest);
        
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('country_code.required', $messages);
        $this->assertArrayHasKey('company_name.required', $messages);
        $this->assertArrayHasKey('job_title.required', $messages);
    }

    /** @test */
    public function it_has_domain_specific_attribute_names()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getDomainAttributes');
        $method->setAccessible(true);
        
        $attributes = $method->invoke($this->testRequest);
        
        $this->assertIsArray($attributes);
        $this->assertArrayHasKey('country_code', $attributes);
        $this->assertArrayHasKey('company_name', $attributes);
        $this->assertArrayHasKey('job_title', $attributes);
    }
} 