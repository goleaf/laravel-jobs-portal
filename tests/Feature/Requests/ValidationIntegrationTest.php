<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\Api\ApiRequest;
use App\Http\Requests\Communication\CommunicationRequest;
use App\Http\Requests\Financial\FinancialRequest;
use App\Http\Requests\Foundation\AbstractBaseRequest;
use App\Http\Requests\MasterData\MasterDataRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Validation Integration Test Suite
 *
 * Tests the integration between different validation components
 */
class ValidationIntegrationTest extends TestCase
{
    /** @test */
    public function it_integrates_all_domain_validation_classes()
    {
        // Test that all domain classes extend AbstractBaseRequest
        $this->assertInstanceOf(AbstractBaseRequest::class, $this->createMasterDataRequest());
        $this->assertInstanceOf(AbstractBaseRequest::class, $this->createFinancialRequest());
        $this->assertInstanceOf(AbstractBaseRequest::class, $this->createApiRequest());
        $this->assertInstanceOf(AbstractBaseRequest::class, $this->createCommunicationRequest());
    }

    /** @test */
    public function it_combines_multiple_domain_validations()
    {
        // Create a combined request that uses multiple domains
        $combinedRequest = new class extends AbstractBaseRequest
        {
            protected function getDomainRules(): array
            {
                $masterDataRequest = $this->createMasterDataInstance();
                $financialRequest = $this->createFinancialInstance();

                return array_merge(
                    $masterDataRequest->getLocationRules(),
                    $financialRequest->getPaymentRules()
                );
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

            private function createMasterDataInstance()
            {
                return new class extends MasterDataRequest
                {
                    public function getLocationRules(): array
                    {
                        return parent::getLocationRules();
                    }

                    protected function getBusinessLogicRules(): array
                    {
                        return [];
                    }
                };
            }

            private function createFinancialInstance()
            {
                return new class extends FinancialRequest
                {
                    public function getPaymentRules(): array
                    {
                        return parent::getPaymentRules();
                    }

                    protected function getBusinessLogicRules(): array
                    {
                        return [];
                    }
                };
            }
        };

        $data = [
            'country_code' => 'US',
            'city_name' => 'New York',
            'postal_code' => '10001',
            'card_number' => '4111111111111111',
            'card_holder_name' => 'John Doe',
            'expiry_month' => 12,
            'expiry_year' => 2025,
            'cvv' => '123',
        ];

        $validator = Validator::make($data, $combinedRequest->rules());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_validates_api_requests_with_domain_data()
    {
        $apiRequest = new class extends ApiRequest
        {
            public function rules(): array
            {
                return array_merge(
                    $this->getDomainRules(),
                    $this->getPaginationRules(),
                    $this->getSortingRules(['name', 'created_at'])
                );
            }

            protected function getBusinessLogicRules(): array
            {
                return [];
            }
        };

        $data = [
            'page' => 1,
            'per_page' => 10,
            'sort' => 'name',
            'order' => 'asc',
            'api_version' => 'v1',
        ];

        $validator = Validator::make($data, $apiRequest->rules());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_handles_validation_errors_consistently()
    {
        $requests = [
            $this->createMasterDataRequest(),
            $this->createFinancialRequest(),
            $this->createApiRequest(),
            $this->createCommunicationRequest(),
        ];

        foreach ($requests as $request) {
            $reflection = new \ReflectionClass($request);

            // All requests should have error handling methods
            $this->assertTrue($reflection->hasMethod('getDomainMessages'));
            $this->assertTrue($reflection->hasMethod('getDomainAttributes'));

            $messages = $request->messages();
            $attributes = $request->attributes();

            $this->assertIsArray($messages);
            $this->assertIsArray($attributes);
        }
    }

    /** @test */
    public function it_applies_sanitization_across_domains()
    {
        $testData = [
            'master_data' => [
                'company_name' => '  Test Company  ',
                'country_code' => 'us',
            ],
            'financial' => [
                'amount' => '99.999',
                'currency' => 'usd',
            ],
            'api' => [
                'sort' => 'NAME',
                'order' => 'ASC',
            ],
        ];

        // Test MasterData sanitization
        $masterDataRequest = $this->createMasterDataRequest();
        $method = new \ReflectionMethod($masterDataRequest, 'applySanitization');
        $method->setAccessible(true);
        $sanitized = $method->invoke($masterDataRequest, $testData['master_data']);

        $this->assertEquals('Test Company', $sanitized['company_name']);
        $this->assertEquals('US', $sanitized['country_code']);

        // Test Financial sanitization
        $financialRequest = $this->createFinancialRequest();
        $method = new \ReflectionMethod($financialRequest, 'applySanitization');
        $method->setAccessible(true);
        $sanitized = $method->invoke($financialRequest, $testData['financial']);

        $this->assertEquals(100.00, $sanitized['amount']);
        $this->assertEquals('USD', $sanitized['currency']);

        // Test API sanitization
        $apiRequest = $this->createApiRequest();
        $method = new \ReflectionMethod($apiRequest, 'applySanitization');
        $method->setAccessible(true);
        $sanitized = $method->invoke($apiRequest, $testData['api']);

        $this->assertEquals('name', $sanitized['sort']);
        $this->assertEquals('asc', $sanitized['order']);
    }

    /** @test */
    public function it_maintains_security_levels_across_domains()
    {
        $securityLevels = [
            'MasterData' => 'high',
            'Financial' => 'critical',
            'Api' => 'high',
            'Communication' => 'medium',
        ];

        foreach ($securityLevels as $domain => $expectedLevel) {
            $request = $this->{"create{$domain}Request"}();
            $reflection = new \ReflectionClass($request);
            $property = $reflection->getProperty('securityLevel');
            $property->setAccessible(true);

            $actualLevel = $property->getValue($request);
            $this->assertEquals(
                $expectedLevel,
                $actualLevel,
                "{$domain}Request should have {$expectedLevel} security level"
            );
        }
    }

    /** @test */
    public function it_validates_multilingual_content()
    {
        $communicationRequest = $this->createCommunicationRequest();

        $testData = [
            'subject' => 'Test Subject',
            'content' => 'This is test content with some text',
            'message_type' => 'email',
        ];

        $validator = Validator::make($testData, $communicationRequest->rules());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_handles_cross_domain_validation_failures()
    {
        $combinedRequest = new class extends AbstractBaseRequest
        {
            protected function getDomainRules(): array
            {
                return [
                    'email' => ['required', 'email'],
                    'amount' => ['required', 'numeric', 'min:0'],
                    'country_code' => ['required', 'size:2'],
                    'api_version' => ['required', 'in:v1,v2,v3'],
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

        $invalidData = [
            'email' => 'invalid-email',
            'amount' => -10,
            'country_code' => 'USA',
            'api_version' => 'v99',
        ];

        $validator = Validator::make($invalidData, $combinedRequest->rules());
        $this->assertTrue($validator->fails());

        $errors = $validator->errors()->toArray();
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('amount', $errors);
        $this->assertArrayHasKey('country_code', $errors);
        $this->assertArrayHasKey('api_version', $errors);
    }

    // Helper methods to create domain request instances
    private function createMasterDataRequest()
    {
        return new class extends MasterDataRequest
        {
            public function rules(): array
            {
                return $this->getDomainRules();
            }

            protected function getBusinessLogicRules(): array
            {
                return [];
            }
        };
    }

    private function createFinancialRequest()
    {
        return new class extends FinancialRequest
        {
            public function rules(): array
            {
                return $this->getDomainRules();
            }

            protected function getBusinessLogicRules(): array
            {
                return [];
            }
        };
    }

    private function createApiRequest()
    {
        return new class extends ApiRequest
        {
            public function rules(): array
            {
                return $this->getDomainRules();
            }

            protected function getBusinessLogicRules(): array
            {
                return [];
            }
        };
    }

    private function createCommunicationRequest()
    {
        return new class extends CommunicationRequest
        {
            public function rules(): array
            {
                return $this->getDomainRules();
            }

            protected function getBusinessLogicRules(): array
            {
                return [];
            }
        };
    }
}
