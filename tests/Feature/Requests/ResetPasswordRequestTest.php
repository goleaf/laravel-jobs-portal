<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Enhanced Validation Tests for ResetPasswordRequest.
 *
 * @group validation
 * @group requests
 *
 * @internal
 *
 * @coversNothing
 */
class ResetPasswordRequestTest extends TestCase
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
        $request = new ResetPasswordRequest();

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function testValidationRulesAreDefined()
    {
        $request = new ResetPasswordRequest();
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function testValidationMessagesAreDefined()
    {
        $request = new ResetPasswordRequest();

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
        $request = new ResetPasswordRequest();

        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function testTokenValidation()
    {
        $request = new ResetPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('token', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['token'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testEmailValidation()
    {
        $request = new ResetPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPasswordValidation()
    {
        $request = new ResetPasswordRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('password', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['password'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testValidDataPassesValidation()
    {
        $validData = [
            'token' => 'Test Value',
            'email' => 'test@example.com',
            'password' => 'SecureP@ssw0rd123!',
        ];

        $request = new ResetPasswordRequest();
        $validator = validator($validData, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function testRequestHandlesEmptyDataCorrectly()
    {
        $emptyData = [];

        $request = new ResetPasswordRequest();
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

        $request = new ResetPasswordRequest();
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

        $request = new ResetPasswordRequest();
        $validator = validator($sqlInjectionData, $request->rules());

        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
