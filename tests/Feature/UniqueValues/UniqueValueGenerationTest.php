<?php

namespace Tests\Feature\UniqueValues;

use App\Services\Universal\UniversalUniqueValueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use JustBetter\UniqueValues\Support\UniqueValue;
use Tests\TestCase;

class UniqueValueGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected UniversalUniqueValueService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UniversalUniqueValueService::class);
        Cache::flush();
    }

    /** @test */
    public function it_can_generate_unique_job_reference()
    {
        $reference1 = $this->service->generateJobReference();
        $reference2 = $this->service->generateJobReference();

        $this->assertStringStartsWith('JOB-', $reference1);
        $this->assertStringStartsWith('JOB-', $reference2);
        $this->assertNotEquals($reference1, $reference2);
        $this->assertMatchesRegularExpression('/^JOB-\d{4}-\d{6}$/', $reference1);
    }

    /** @test */
    public function it_can_generate_unique_application_code()
    {
        $code1 = $this->service->generateApplicationCode();
        $code2 = $this->service->generateApplicationCode();

        $this->assertStringStartsWith('APP-', $code1);
        $this->assertStringStartsWith('APP-', $code2);
        $this->assertNotEquals($code1, $code2);
        $this->assertMatchesRegularExpression('/^APP-\d{8}-\d{5}$/', $code1);
    }

    /** @test */
    public function it_can_generate_unique_candidate_code()
    {
        $code1 = $this->service->generateCandidateCode();
        $code2 = $this->service->generateCandidateCode();

        $this->assertStringStartsWith('CAN-', $code1);
        $this->assertStringStartsWith('CAN-', $code2);
        $this->assertNotEquals($code1, $code2);
        $this->assertMatchesRegularExpression('/^CAN-\d{6}$/', $code1);
    }

    /** @test */
    public function it_can_generate_unique_company_code()
    {
        $code1 = $this->service->generateCompanyCode();
        $code2 = $this->service->generateCompanyCode();

        $this->assertStringStartsWith('COM-', $code1);
        $this->assertStringStartsWith('COM-', $code2);
        $this->assertNotEquals($code1, $code2);
        $this->assertMatchesRegularExpression('/^COM-\d{4}-\d{5}$/', $code1);
    }

    /** @test */
    public function it_can_generate_unique_slugs()
    {
        $slug1 = $this->service->generateUniqueSlug('Software Developer', 'job-slug');
        $slug2 = $this->service->generateUniqueSlug('Software Developer', 'job-slug');

        $this->assertEquals('software-developer', $slug1);
        $this->assertEquals('software-developer-1', $slug2);
    }

    /** @test */
    public function it_can_generate_unique_invoice_number()
    {
        $invoice1 = $this->service->generateInvoiceNumber();
        $invoice2 = $this->service->generateInvoiceNumber();

        $this->assertStringStartsWith('INV-', $invoice1);
        $this->assertStringStartsWith('INV-', $invoice2);
        $this->assertNotEquals($invoice1, $invoice2);
        $this->assertMatchesRegularExpression('/^INV-\d{8}-\d{5}$/', $invoice1);
    }

    /** @test */
    public function it_can_generate_unique_api_key()
    {
        $key1 = $this->service->generateApiKey();
        $key2 = $this->service->generateApiKey();

        $this->assertNotEquals($key1, $key2);
        $this->assertEquals(32, strlen($key1));
        $this->assertEquals(32, strlen($key2));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $key1);
    }

    /** @test */
    public function it_can_generate_unique_order_reference()
    {
        $order1 = $this->service->generateOrderReference();
        $order2 = $this->service->generateOrderReference();

        $this->assertStringStartsWith('ORD-', $order1);
        $this->assertStringStartsWith('ORD-', $order2);
        $this->assertNotEquals($order1, $order2);
        $this->assertMatchesRegularExpression('/^ORD-\d{8}-\d{6}-\d{3}$/', $order1);
    }

    /** @test */
    public function it_handles_concurrency_with_subjects()
    {
        $jobId = 123;

        // First generation should be the same for the same subject
        $reference1 = $this->service->generateJobReference($jobId);
        $reference2 = $this->service->generateJobReference($jobId);

        $this->assertEquals($reference1, $reference2);

        // Different subject should generate different value
        $reference3 = $this->service->generateJobReference(456);
        $this->assertNotEquals($reference1, $reference3);
    }

    /** @test */
    public function it_can_generate_custom_unique_values()
    {
        $custom = $this->service->generateCustomUnique(
            'test-scope',
            function (int $attempt): string {
                return 'TEST-'.str_pad((string) $attempt, 3, '0', STR_PAD_LEFT);
            },
            null,
            5
        );

        $this->assertEquals('TEST-000', $custom);

        // Second generation should increment
        $custom2 = $this->service->generateCustomUnique(
            'test-scope',
            function (int $attempt): string {
                return 'TEST-'.str_pad((string) $attempt, 3, '0', STR_PAD_LEFT);
            },
            null,
            5
        );

        $this->assertEquals('TEST-001', $custom2);
    }

    /** @test */
    public function it_can_generate_batch_values()
    {
        $subjectIds = [1, 2, 3];
        $results = $this->service->generateBatch('job-reference', $subjectIds);

        $this->assertCount(3, $results);
        $this->assertArrayHasKey(1, $results);
        $this->assertArrayHasKey(2, $results);
        $this->assertArrayHasKey(3, $results);

        foreach ($results as $result) {
            $this->assertStringStartsWith('JOB-', $result);
        }

        // All values should be unique
        $uniqueValues = array_unique(array_values($results));
        $this->assertCount(3, $uniqueValues);
    }

    /** @test */
    public function it_provides_generation_statistics()
    {
        $stats = $this->service->getGenerationStats();

        $this->assertArrayHasKey('scopes', $stats);
        $this->assertArrayHasKey('configuration', $stats);
        $this->assertArrayHasKey('patterns', $stats);

        $expectedScopes = [
            'job-reference',
            'application-code',
            'candidate-code',
            'company-code',
            'invoice-number',
            'order-reference',
            'api-key',
            'general-slug',
        ];

        foreach ($expectedScopes as $scope) {
            $this->assertContains($scope, $stats['scopes']);
        }
    }

    /** @test */
    public function it_handles_failed_generation_gracefully()
    {
        $this->expectException(\Exception::class);

        $this->service->generateBatch('invalid-type', [1, 2, 3]);
    }

    /** @test */
    public function it_respects_maximum_attempts()
    {
        // Create a scenario where generation will always conflict
        $conflictingGenerator = function (int $attempt): string {
            return 'ALWAYS-SAME-VALUE';
        };

        // First generation should succeed
        $value1 = UniqueValue::make()
            ->scope('conflict-test')
            ->attempts(2)
            ->generator($conflictingGenerator)
            ->generate();

        $this->assertEquals('ALWAYS-SAME-VALUE', $value1);

        // Second generation should fail after max attempts
        $this->expectException(\Exception::class);

        UniqueValue::make()
            ->scope('conflict-test')
            ->attempts(2)
            ->generator($conflictingGenerator)
            ->generate();
    }

    /** @test */
    public function it_works_with_different_scopes()
    {
        $value1 = UniqueValue::make()
            ->scope('scope-1')
            ->generator(fn () => 'TEST-VALUE')
            ->generate();

        $value2 = UniqueValue::make()
            ->scope('scope-2')
            ->generator(fn () => 'TEST-VALUE')
            ->generate();

        // Same value should be allowed in different scopes
        $this->assertEquals('TEST-VALUE', $value1);
        $this->assertEquals('TEST-VALUE', $value2);
    }
}
