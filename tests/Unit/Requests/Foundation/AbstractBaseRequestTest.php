<?php

namespace Tests\Unit\Requests\Foundation;

use Tests\TestCase;
use App\Http\Requests\Foundation\AbstractBaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

/**
 * AbstractBaseRequest Test Suite
 * 
 * Tests the foundation validation architecture
 */
class AbstractBaseRequestTest extends TestCase
{
    protected $testRequest;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create anonymous test request for testing
        $this->testRequest = new class extends AbstractBaseRequest {
            protected function getDomainRules(): array
            {
                return [
                    'test_field' => ['required', 'string', 'max:255'],
                    'email_field' => ['required', 'email'],
                ];
            }

            protected function getDomainMessages(): array
            {
                return [
                    'test_field.required' => 'Test field is required',
                    'email_field.email' => 'Email field must be valid email',
                ];
            }

            protected function getDomainAttributes(): array
            {
                return [
                    'test_field' => 'Test Field',
                    'email_field' => 'Email Address',
                ];
            }

            protected function getBusinessLogicRules(): array
            {
                return [];
            }
        };
    }

    /** @test */
    public function it_extends_laravel_form_request()
    {
        $this->assertInstanceOf(FormRequest::class, $this->testRequest);
    }

    /** @test */
    public function it_has_default_security_level()
    {
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('securityLevel');
        $property->setAccessible(true);
        
        $this->assertEquals('medium', $property->getValue($this->testRequest));
    }

    /** @test */
    public function it_combines_common_and_domain_rules()
    {
        $this->testRequest->replace([
            'test_field' => 'valid_value',
            'email_field' => 'test@example.com'
        ]);

        $rules = $this->testRequest->rules();
        
        $this->assertArrayHasKey('test_field', $rules);
        $this->assertArrayHasKey('email_field', $rules);
        $this->assertContains('required', $rules['test_field']);
        $this->assertContains('email', $rules['email_field']);
    }

    /** @test */
    public function it_validates_successfully_with_valid_data()
    {
        $data = [
            'test_field' => 'Valid Test Value',
            'email_field' => 'test@example.com'
        ];

        $validator = Validator::make($data, $this->testRequest->rules());
        
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_fails_validation_with_invalid_data()
    {
        $data = [
            'test_field' => '', // Required field empty
            'email_field' => 'invalid-email' // Invalid email format
        ];

        $validator = Validator::make($data, $this->testRequest->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('test_field', $validator->errors()->toArray());
        $this->assertArrayHasKey('email_field', $validator->errors()->toArray());
    }

    /** @test */
    public function it_applies_custom_messages()
    {
        $data = [
            'test_field' => '',
            'email_field' => 'invalid-email'
        ];

        $validator = Validator::make(
            $data, 
            $this->testRequest->rules(),
            $this->testRequest->messages()
        );
        
        $this->assertTrue($validator->fails());
        $this->assertContains('Test field is required', $validator->errors()->get('test_field'));
        $this->assertContains('Email field must be valid email', $validator->errors()->get('email_field'));
    }

    /** @test */
    public function it_applies_custom_attributes()
    {
        $attributes = $this->testRequest->attributes();
        
        $this->assertArrayHasKey('test_field', $attributes);
        $this->assertArrayHasKey('email_field', $attributes);
        $this->assertEquals('Test Field', $attributes['test_field']);
        $this->assertEquals('Email Address', $attributes['email_field']);
    }

    /** @test */
    public function it_has_performance_tracking_enabled_by_default()
    {
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('performanceTracking');
        $property->setAccessible(true);
        
        $this->assertTrue($property->getValue($this->testRequest));
    }

    /** @test */
    public function it_can_check_performance_tracking_status()
    {
        $this->assertTrue($this->testRequest->isPerformanceTrackingEnabled());
    }

    /** @test */
    public function it_generates_unique_request_id()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getRequestId');
        $method->setAccessible(true);
        
        $requestId1 = $method->invoke($this->testRequest);
        $requestId2 = $method->invoke($this->testRequest);
        
        $this->assertNotEquals($requestId1, $requestId2);
        $this->assertIsString($requestId1);
        $this->assertTrue(strlen($requestId1) > 10);
    }

    /** @test */
    public function it_can_get_security_level()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getSecurityLevel');
        $method->setAccessible(true);
        
        $securityLevel = $method->invoke($this->testRequest);
        
        $this->assertEquals('medium', $securityLevel);
    }

    /** @test */
    public function it_applies_sanitization_to_data()
    {
        $data = [
            'test_field' => '  Trim This  ',
            'email_field' => '<script>Test@EXAMPLE.COM</script>'
        ];

        $method = new \ReflectionMethod($this->testRequest, 'applySanitization');
        $method->setAccessible(true);
        
        $sanitized = $method->invoke($this->testRequest, $data);
        
        $this->assertEquals('Trim This', $sanitized['test_field']);
        $this->assertEquals('Test@EXAMPLE.COM', $sanitized['email_field']);
    }

    /** @test */
    public function it_has_validation_modules_array()
    {
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('validationModules');
        $property->setAccessible(true);
        
        $modules = $property->getValue($this->testRequest);
        
        $this->assertIsArray($modules);
    }
} 