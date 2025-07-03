<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Financial\FinancialRequest;
use App\Http\Requests\Foundation\AbstractBaseRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Validation Performance Test Suite
 *
 * Tests the performance of validation systems
 */
class ValidationPerformanceTest extends TestCase
{
    /** @test */
    public function it_validates_large_datasets_within_performance_limits()
    {
        $startTime = microtime(true);

        // Test with 100 validation requests
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'test_field' => 'Valid Test Value '.$i,
                'email_field' => "test{$i}@example.com",
            ];

            $testRequest = new class extends AbstractBaseRequest
            {
                protected function getDomainRules(): array
                {
                    return [
                        'test_field' => ['required', 'string', 'max:255'],
                        'email_field' => ['required', 'email'],
                    ];
                }

                protected function getDomainMessages(): array
                {
                    return [];
                }

                protected function getDomainAttributes(): array
                {
                    return [];
                }

                protected function getBusinessLogicRules(): array
                {
                    return [];
                }
            };

            $validator = Validator::make($data, $testRequest->rules());
            $this->assertTrue($validator->passes());
        }

        $executionTime = microtime(true) - $startTime;

        // Should complete 100 validations in under 1 second
        $this->assertLessThan(
            1.0,
            $executionTime,
            "Validation performance test failed. Took {$executionTime} seconds for 100 validations"
        );
    }

    /** @test */
    public function it_handles_complex_validation_efficiently()
    {
        $startTime = microtime(true);

        $data = [
            'amount' => 99.99,
            'currency' => 'USD',
            'payment_method' => 'card',
            'transaction_type' => 'payment',
            'card_number' => '4111111111111111',
            'card_holder_name' => 'John Doe',
            'expiry_month' => 12,
            'expiry_year' => 2025,
            'cvv' => '123',
        ];

        $testRequest = new class extends FinancialRequest
        {
            public function rules(): array
            {
                // Use simplified rules without database validation for performance testing
                return [
                    'amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
                    'currency' => ['required', 'string', 'size:3'],
                    'payment_method' => ['required', 'string', 'in:card,bank_transfer,paypal,stripe'],
                    'transaction_type' => ['required', 'string', 'in:payment,refund,subscription,cancellation'],
                    'card_number' => ['sometimes', 'required', 'string', 'regex:/^\d{13,19}$/'],
                    'card_holder_name' => ['sometimes', 'required', 'string', 'max:255'],
                    'expiry_month' => ['sometimes', 'required', 'integer', 'between:1,12'],
                    'expiry_year' => ['sometimes', 'required', 'integer', 'min:'.date('Y')],
                    'cvv' => ['sometimes', 'required', 'string', 'regex:/^\d{3,4}$/'],
                ];
            }

            protected function getBusinessLogicRules(): array
            {
                return [];
            }
        };

        // Run complex validation 50 times
        for ($i = 0; $i < 50; $i++) {
            $validator = Validator::make($data, $testRequest->rules());
            $this->assertTrue($validator->passes());
        }

        $executionTime = microtime(true) - $startTime;

        // Should complete 50 complex validations in under 0.5 seconds
        $this->assertLessThan(
            0.5,
            $executionTime,
            "Complex validation performance test failed. Took {$executionTime} seconds for 50 validations"
        );
    }

    /** @test */
    public function it_handles_sanitization_efficiently()
    {
        $startTime = microtime(true);

        $testRequest = new class extends AbstractBaseRequest
        {
            protected function getDomainRules(): array
            {
                return [];
            }

            protected function getDomainMessages(): array
            {
                return [];
            }

            protected function getDomainAttributes(): array
            {
                return [];
            }

            protected function getBusinessLogicRules(): array
            {
                return [];
            }
        };

        $method = new \ReflectionMethod($testRequest, 'applySanitization');
        $method->setAccessible(true);

        // Test sanitization with 1000 data sets
        for ($i = 0; $i < 1000; $i++) {
            $data = [
                'field1' => "  Trim This {$i}  ",
                'field2' => "Test@EXAMPLE{$i}.COM",
                'field3' => "  Another Field {$i}  ",
            ];

            $sanitized = $method->invoke($testRequest, $data);
            $this->assertNotEmpty($sanitized);
        }

        $executionTime = microtime(true) - $startTime;

        // Should complete 1000 sanitizations in under 0.1 seconds
        $this->assertLessThan(
            0.1,
            $executionTime,
            "Sanitization performance test failed. Took {$executionTime} seconds for 1000 sanitizations"
        );
    }

    /** @test */
    public function it_maintains_memory_efficiency()
    {
        $initialMemory = memory_get_usage();

        // Create many validation instances
        $validators = [];
        for ($i = 0; $i < 100; $i++) {
            $validators[] = new class extends AbstractBaseRequest
            {
                protected function getDomainRules(): array
                {
                    return ['field' => ['required', 'string']];
                }

                protected function getDomainMessages(): array
                {
                    return [];
                }

                protected function getDomainAttributes(): array
                {
                    return [];
                }

                protected function getBusinessLogicRules(): array
                {
                    return [];
                }
            };
        }

        $peakMemory = memory_get_peak_usage();
        $memoryUsed = $peakMemory - $initialMemory;

        // Should use less than 5MB for 100 validation instances
        $this->assertLessThan(
            5 * 1024 * 1024,
            $memoryUsed,
            'Memory efficiency test failed. Used '.($memoryUsed / 1024 / 1024).'MB for 100 validators'
        );

        unset($validators);
    }
}
