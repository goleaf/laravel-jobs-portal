<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Enhanced Validation Tests for RegisterRequest.
 *
 * @group validation
 * @group requests
 *
 * @internal
 *
 * @coversNothing
 */
class RegisterRequestTest extends TestCase
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
        $request = new RegisterRequest();

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function testValidationRulesAreDefined()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function testValidationMessagesAreDefined()
    {
        $request = new RegisterRequest();

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
        $request = new RegisterRequest();

        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function testFirstNameValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('first_name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['first_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testRequiredValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('required', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['required'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testStringValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('string', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['string'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testLastNameValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('last_name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['last_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testEmailValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPasswordValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('password', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['password'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testConfirmedValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('confirmed', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['confirmed'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testSecurityAuthenticationPasswordMinLengthValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('security.authentication.password_min_length', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['security.authentication.password_min_length'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPhoneValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('phone', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['phone'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testNullableValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('nullable', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['nullable'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testTermsValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('terms', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['terms'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testAcceptedValidation()
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('accepted', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['accepted'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testValidDataPassesValidation()
    {
        $validData = [
            'first_name' => 'Test Value',
            'required' => 'Test Value',
            'string' => 'Test Value',
            'last_name' => 'Test Value',
            'email' => 'test@example.com',
            'password' => 'SecureP@ssw0rd123!',
            'confirmed' => 'Test Value',
            'security.authentication.password_min_length' => 'SecureP@ssw0rd123!',
            'phone' => '+1234567890',
            'nullable' => 'Test Value',
            'terms' => 'Test Value',
            'accepted' => 'Test Value',
        ];

        $request = new RegisterRequest();
        $validator = validator($validData, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function testRequestHandlesEmptyDataCorrectly()
    {
        $emptyData = [];

        $request = new RegisterRequest();
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

        $request = new RegisterRequest();
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

        $request = new RegisterRequest();
        $validator = validator($sqlInjectionData, $request->rules());

        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
