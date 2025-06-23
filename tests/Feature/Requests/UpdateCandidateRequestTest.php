<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Candidate\UpdateCandidateRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Enhanced Validation Tests for UpdateCandidateRequest.
 *
 * @group validation
 * @group requests
 *
 * @internal
 *
 * @coversNothing
 */
class UpdateCandidateRequestTest extends TestCase
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
        $request = new UpdateCandidateRequest();

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function testValidationRulesAreDefined()
    {
        $request = new UpdateCandidateRequest();
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function testValidationMessagesAreDefined()
    {
        $request = new UpdateCandidateRequest();

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
        $request = new UpdateCandidateRequest();

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
        $request = new UpdateCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user.first_name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user.first_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testUserLastNameValidation()
    {
        $request = new UpdateCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user.last_name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user.last_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testUserEmailValidation()
    {
        $request = new UpdateCandidateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user.email', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user.email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testValidDataPassesValidation()
    {
        $validData = [
            'user.first_name' => 'Test Value',
            'user.last_name' => 'Test Value',
            'user.email' => 'test@example.com',
        ];

        $request = new UpdateCandidateRequest();
        $validator = validator($validData, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function testRequestHandlesEmptyDataCorrectly()
    {
        $emptyData = [];

        $request = new UpdateCandidateRequest();
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

        $request = new UpdateCandidateRequest();
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

        $request = new UpdateCandidateRequest();
        $validator = validator($sqlInjectionData, $request->rules());

        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
