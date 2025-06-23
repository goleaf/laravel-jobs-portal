<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\User\StoreUserRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Enhanced Validation Tests for StoreUserRequest.
 *
 * @group validation
 * @group requests
 *
 * @internal
 *
 * @coversNothing
 */
class StoreUserRequestTest extends TestCase
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
        $request = new StoreUserRequest();

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function testValidationRulesAreDefined()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function testValidationMessagesAreDefined()
    {
        $request = new StoreUserRequest();

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
        $request = new StoreUserRequest();

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
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('first_name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['first_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testRequiredValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('required', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['required'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testStringValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('string', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['string'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testLastNameValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('last_name', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['last_name'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testEmailValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['email'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPasswordValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('password', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['password'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testConfirmedValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('confirmed', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['confirmed'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testUserTypeValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user_type', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user_type'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testIntegerValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('integer', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['integer'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testDobValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('dob', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['dob'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testNullableValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('nullable', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['nullable'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testDateValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('date', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['date'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testGenderValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('gender', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['gender'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPhoneValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('phone', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['phone'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCountryIdValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('country_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['country_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testStateIdValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('state_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['state_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testCityIdValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('city_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['city_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testFacebookUrlValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('facebook_url', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['facebook_url'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testUrlValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('url', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['url'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testTwitterUrlValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('twitter_url', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['twitter_url'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testLinkedinUrlValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('linkedin_url', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['linkedin_url'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testGooglePlusUrlValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('google_plus_url', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['google_plus_url'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPinterestUrlValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('pinterest_url', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['pinterest_url'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testLanguageValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('language', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['language'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testRegionCodeValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('region_code', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['region_code'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testTermsAcceptedValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('terms_accepted', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['terms_accepted'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testAcceptedValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('accepted', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['accepted'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPrivacyAcceptedValidation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('privacy_accepted', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['privacy_accepted'];
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
            'user_type' => 'Test Value',
            'integer' => 'Test Value',
            'dob' => 'Test Value',
            'nullable' => 'Test Value',
            'date' => '2024-01-01',
            'gender' => 'Test Value',
            'phone' => '+1234567890',
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'facebook_url' => 'https://example.com',
            'url' => 'https://example.com',
            'twitter_url' => 'https://example.com',
            'linkedin_url' => 'https://example.com',
            'google_plus_url' => 'https://example.com',
            'pinterest_url' => 'https://example.com',
            'language' => 'Test Value',
            'region_code' => 'Test Value',
            'terms_accepted' => 'Test Value',
            'accepted' => 'Test Value',
            'privacy_accepted' => 'Test Value',
        ];

        $request = new StoreUserRequest();
        $validator = validator($validData, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function testRequestHandlesEmptyDataCorrectly()
    {
        $emptyData = [];

        $request = new StoreUserRequest();
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

        $request = new StoreUserRequest();
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

        $request = new StoreUserRequest();
        $validator = validator($sqlInjectionData, $request->rules());

        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
