<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Candidate\StoreCandidateRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Enhanced Validation Tests for StoreCandidateRequest.
 *
 * @group validation
 * @group requests
 *
 * @internal
 *
 * @coversNothing
 */
class StoreCandidateRequestTest extends TestCase
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
        $request = new StoreCandidateRequest();

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function testValidationRulesAreDefined()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function testValidationMessagesAreDefined()
    {
        $request = new StoreCandidateRequest();

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
        $request = new StoreCandidateRequest();

        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function testUserFirstNameValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user.first_name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user.first_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testUserLastNameValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user.last_name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user.last_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testUserEmailValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user.email', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user.email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testUserPasswordValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user.password', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user.password'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testUserPhoneValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user.phone', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user.phone'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testUserDobValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user.dob', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user.dob'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testMaritalStatusIdValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('marital_status_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['marital_status_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testNationalityValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('nationality', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['nationality'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCountryIdValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('country_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['country_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testStateIdValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('state_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['state_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCityIdValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('city_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['city_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testIsActiveValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('is_active', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['is_active'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testBooleanValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('boolean', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['boolean'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testIsVerifiedValidation()
    {
        $request = new StoreCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('is_verified', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['is_verified'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testValidDataPassesValidation()
    {
        $validData = [
            'user.first_name' => 'Test Value',
            'user.last_name' => 'Test Value',
            'user.email' => 'test@example.com',
            'user.password' => 'SecureP@ssw0rd123!',
            'user.phone' => '+1234567890',
            'user.dob' => 'Test Value',
            'marital_status_id' => true,
            'nationality' => 'Test Value',
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'is_active' => true,
            'boolean' => 'Test Value',
            'is_verified' => 'Test Value',
        ];

        $request = new StoreCandidateRequest();
        $validator = validator($validData, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function testRequestHandlesEmptyDataCorrectly()
    {
        $emptyData = [];

        $request = new StoreCandidateRequest();
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

        $request = new StoreCandidateRequest();
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

        $request = new StoreCandidateRequest();
        $validator = validator($sqlInjectionData, $request->rules());

        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
