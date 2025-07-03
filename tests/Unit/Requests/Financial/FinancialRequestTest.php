<?php

namespace Tests\Unit\Requests\Financial;

use Tests\TestCase;
use App\Http\Requests\Financial\FinancialRequest;
use Illuminate\Support\Facades\Validator;

/**
 * FinancialRequest Test Suite
 * 
 * Tests the financial validation domain
 */
class FinancialRequestTest extends TestCase
{
    protected $testRequest;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create anonymous test request for testing
        $this->testRequest = new class extends FinancialRequest {
            public function rules(): array
            {
                return $this->buildValidationRules();
            }
        };
    }

    /** @test */
    public function it_has_critical_security_level()
    {
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('securityLevel');
        $property->setAccessible(true);
        
        $this->assertEquals('critical', $property->getValue($this->testRequest));
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
    public function it_has_audit_logging_enabled()
    {
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('auditLoggingEnabled');
        $property->setAccessible(true);
        
        $this->assertTrue($property->getValue($this->testRequest));
    }

    /** @test */
    public function it_includes_financial_validation_modules()
    {
        $reflection = new \ReflectionClass($this->testRequest);
        $property = $reflection->getProperty('validationModules');
        $property->setAccessible(true);
        
        $modules = $property->getValue($this->testRequest);
        
        $this->assertContains('payment_security', $modules);
        $this->assertContains('currency_validation', $modules);
        $this->assertContains('amount_verification', $modules);
        $this->assertContains('compliance_check', $modules);
    }

    /** @test */
    public function it_provides_payment_validation_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getPaymentRules');
        $method->setAccessible(true);
        
        $rules = $method->invoke($this->testRequest);
        
        $this->assertArrayHasKey('card_number', $rules);
        $this->assertArrayHasKey('card_holder_name', $rules);
        $this->assertArrayHasKey('expiry_month', $rules);
        $this->assertArrayHasKey('expiry_year', $rules);
        $this->assertArrayHasKey('cvv', $rules);
    }

    /** @test */
    public function it_provides_subscription_validation_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getSubscriptionRules');
        $method->setAccessible(true);
        
        $rules = $method->invoke($this->testRequest);
        
        $this->assertArrayHasKey('plan_id', $rules);
        $this->assertArrayHasKey('billing_cycle', $rules);
        $this->assertArrayHasKey('start_date', $rules);
        $this->assertArrayHasKey('auto_renewal', $rules);
    }

    /** @test */
    public function it_validates_valid_payment_amount()
    {
        $data = [
            'amount' => 99.99,
            'currency' => 'USD',
            'payment_method' => 'card',
            'transaction_type' => 'payment'
        ];

        $method = new \ReflectionMethod($this->testRequest, 'getDomainRules');
        $method->setAccessible(true);
        $rules = $method->invoke($this->testRequest);

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_fails_validation_for_negative_amount()
    {
        $data = [
            'amount' => -10.00,
            'currency' => 'USD',
            'payment_method' => 'card',
            'transaction_type' => 'payment'
        ];

        $method = new \ReflectionMethod($this->testRequest, 'getDomainRules');
        $method->setAccessible(true);
        $rules = $method->invoke($this->testRequest);

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('amount', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_card_number_format()
    {
        $rules = $this->testRequest->getPaymentRules();
        
        $validData = ['card_number' => '4111111111111111'];
        $invalidData = ['card_number' => '123'];
        
        $validValidator = Validator::make($validData, $rules);
        $invalidValidator = Validator::make($invalidData, $rules);
        
        $this->assertTrue($validValidator->passes());
        $this->assertTrue($invalidValidator->fails());
    }

    /** @test */
    public function it_validates_cvv_format()
    {
        $rules = $this->testRequest->getPaymentRules();
        
        $validData = ['cvv' => '123'];
        $invalidData = ['cvv' => 'abc'];
        
        $validValidator = Validator::make($validData, $rules);
        $invalidValidator = Validator::make($invalidData, $rules);
        
        $this->assertTrue($validValidator->passes());
        $this->assertTrue($invalidValidator->fails());
    }

    /** @test */
    public function it_validates_billing_cycle_options()
    {
        $rules = $this->testRequest->getSubscriptionRules();
        
        $validData = ['billing_cycle' => 'monthly'];
        $invalidData = ['billing_cycle' => 'invalid'];
        
        $validValidator = Validator::make($validData, $rules);
        $invalidValidator = Validator::make($invalidData, $rules);
        
        $this->assertTrue($validValidator->passes());
        $this->assertTrue($invalidValidator->fails());
    }

    /** @test */
    public function it_gets_correct_amount_limits_for_transaction_types()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getAmountLimits');
        $method->setAccessible(true);
        
        $paymentLimits = $method->invoke($this->testRequest, 'payment');
        $refundLimits = $method->invoke($this->testRequest, 'refund');
        $subscriptionLimits = $method->invoke($this->testRequest, 'subscription');
        
        $this->assertEquals(['min' => 0.01, 'max' => 100000.00], $paymentLimits);
        $this->assertEquals(['min' => 0.01, 'max' => 50000.00], $refundLimits);
        $this->assertEquals(['min' => 1.00, 'max' => 10000.00], $subscriptionLimits);
    }

    /** @test */
    public function it_gets_supported_currencies_for_payment_methods()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getSupportedCurrencies');
        $method->setAccessible(true);
        
        $paypalCurrencies = $method->invoke($this->testRequest, 'paypal');
        $stripeCurrencies = $method->invoke($this->testRequest, 'stripe');
        
        $this->assertContains('USD', $paypalCurrencies);
        $this->assertContains('EUR', $paypalCurrencies);
        $this->assertGreaterThan(count($paypalCurrencies), count($stripeCurrencies));
    }

    /** @test */
    public function it_applies_financial_sanitization()
    {
        $data = [
            'card_number' => '4111-1111-1111-1111',
            'amount' => '99.999',
            'currency' => 'usd'
        ];

        $method = new \ReflectionMethod($this->testRequest, 'applySanitization');
        $method->setAccessible(true);
        
        $sanitized = $method->invoke($this->testRequest, $data);
        
        $this->assertEquals('4111111111111111', $sanitized['card_number']);
        $this->assertEquals(100.00, $sanitized['amount']);
        $this->assertEquals('USD', $sanitized['currency']);
    }

    /** @test */
    public function it_has_domain_specific_error_messages()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getDomainMessages');
        $method->setAccessible(true);
        
        $messages = $method->invoke($this->testRequest);
        
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('amount.required', $messages);
        $this->assertArrayHasKey('currency.required', $messages);
        $this->assertArrayHasKey('payment_method.required', $messages);
    }

    /** @test */
    public function it_provides_billing_address_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getBillingAddressRules');
        $method->setAccessible(true);
        
        $rules = $method->invoke($this->testRequest);
        
        $this->assertArrayHasKey('billing_name', $rules);
        $this->assertArrayHasKey('billing_address', $rules);
        $this->assertArrayHasKey('billing_city', $rules);
        $this->assertArrayHasKey('billing_country', $rules);
        $this->assertContains('required', $rules['billing_name']);
        $this->assertContains('size:2', $rules['billing_country']);
    }

    /** @test */
    public function it_provides_currency_operation_rules()
    {
        $method = new \ReflectionMethod($this->testRequest, 'getCurrencyRules');
        $method->setAccessible(true);
        
        $rules = $method->invoke($this->testRequest);
        
        $this->assertArrayHasKey('from_currency', $rules);
        $this->assertArrayHasKey('to_currency', $rules);
        $this->assertArrayHasKey('exchange_rate', $rules);
        $this->assertContains('required', $rules['from_currency']);
        $this->assertContains('size:3', $rules['from_currency']);
    }
} 