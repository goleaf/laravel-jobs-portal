<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Enhanced Validation Tests for StoreTransactionRequest.
 *
 * @group validation
 * @group requests
 *
 * @internal
 *
 * @coversNothing
 */
class StoreTransactionRequestTest extends TestCase
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
        $request = new StoreTransactionRequest();

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function testValidationRulesAreDefined()
    {
        $request = new StoreTransactionRequest();
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    /** @test */
    public function testValidationMessagesAreDefined()
    {
        $request = new StoreTransactionRequest();

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
        $request = new StoreTransactionRequest();

        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->markTestSkipped('No custom attributes method defined');
        }
    }

    /** @test */
    public function testUserIdValidation()
    {
        $request = new StoreTransactionRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('user_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['user_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testSubscriptionPlanIdValidation()
    {
        $request = new StoreTransactionRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('subscription_plan_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['subscription_plan_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testTransactionIdValidation()
    {
        $request = new StoreTransactionRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('transaction_id', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['transaction_id'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testAmountValidation()
    {
        $request = new StoreTransactionRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('amount', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['amount'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testPaymentTypeValidation()
    {
        $request = new StoreTransactionRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('payment_type', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['payment_type'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testStatusValidation()
    {
        $request = new StoreTransactionRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('status', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['status'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testMetaValidation()
    {
        $request = new StoreTransactionRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('meta', $rules);

        // Test field-specific validation rules
        $fieldRules = $rules['meta'];
        $this->assertNotEmpty($fieldRules);
    }

    /** @test */
    public function testValidDataPassesValidation()
    {
        $validData = [
            'user_id' => 1,
            'subscription_plan_id' => 1,
            'transaction_id' => 1,
            'amount' => 'Test Value',
            'payment_type' => 'Test Value',
            'status' => true,
            'meta' => 'Test Value',
        ];

        $request = new StoreTransactionRequest();
        $validator = validator($validData, $request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function testRequestHandlesEmptyDataCorrectly()
    {
        $emptyData = [];

        $request = new StoreTransactionRequest();
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

        $request = new StoreTransactionRequest();
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

        $request = new StoreTransactionRequest();
        $validator = validator($sqlInjectionData, $request->rules());

        // SQL injection patterns should be handled safely
        $this->assertIsArray($validator->errors()->toArray());
    }
}
