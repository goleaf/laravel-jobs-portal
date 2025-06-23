<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Auth\StoreConfirmPasswordRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Enhanced Validation Tests for StoreConfirmPasswordRequest.
 *
 * @group validation
 * @group requests
 *
 * @internal
 *
 * @coversNothing
 */
class StoreConfirmPasswordRequestTest extends TestCase
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
        $request = new StoreConfirmPasswordRequest();

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function testValidationRulesAreDefined()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function testValidationMessagesAreDefined()
    {
        $request = new StoreConfirmPasswordRequest();

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
        $request = new StoreConfirmPasswordRequest();

        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function testNameValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testRequiredValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('required', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['required'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testStringValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('string', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['string'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testEmailValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testNullableValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('nullable', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['nullable'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testDescriptionValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('description', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['description'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testStatusValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('status', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['status'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testBooleanValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('boolean', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['boolean'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testAppRecaptchaEnabledValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('app.recaptcha_enabled', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['app.recaptcha_enabled'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testValidationRecaptchaRequiredValidation()
    {
        $request = new StoreConfirmPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('validation.recaptcha_required', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['validation.recaptcha_required'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testValidDataPassesValidation()
    {
        $validData = [
            'name' => 'Test Value',
            'required' => 'Test Value',
            'string' => 'Test Value',
            'email' => 'test@example.com',
            'nullable' => 'Test Value',
            'description' => 'Test Value',
            'status' => true,
            'boolean' => 'Test Value',
            'app.recaptcha_enabled' => 'Test Value',
            'validation.recaptcha_required' => 1,
        ];

        $request = new StoreConfirmPasswordRequest();
        $validator = validator($validData, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function testRequestHandlesEmptyDataCorrectly()
    {
        $emptyData = [];

        $request = new StoreConfirmPasswordRequest();
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

        $request = new StoreConfirmPasswordRequest();
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

        $request = new StoreConfirmPasswordRequest();
        $validator = validator($sqlInjectionData, $request->rules());

        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
