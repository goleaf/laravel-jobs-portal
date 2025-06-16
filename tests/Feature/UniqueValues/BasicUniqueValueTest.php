<?php

namespace Tests\Feature\UniqueValues;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JustBetter\UniqueValues\Support\UniqueValue;

class BasicUniqueValueTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_generate_basic_unique_values()
    {
        $value1 = UniqueValue::make()
            ->scope('test-scope')
            ->generator(function (int $attempt): string {
                return $attempt === 0 ? 'unique-value' : 'unique-value-' . $attempt;
            })
            ->generate();

        $value2 = UniqueValue::make()
            ->scope('test-scope')
            ->generator(function (int $attempt): string {
                return $attempt === 0 ? 'unique-value' : 'unique-value-' . $attempt;
            })
            ->generate();

        $this->assertEquals('unique-value', $value1);
        $this->assertEquals('unique-value-1', $value2);
    }

    /** @test */
    public function it_works_with_subject_persistence()
    {
        $subjectId = 123;
        
        $value1 = UniqueValue::make()
            ->scope('subject-test')
            ->subject($subjectId)
            ->generator(function (int $attempt): string {
                return 'value-for-subject-' . $attempt;
            })
            ->generate();

        $value2 = UniqueValue::make()
            ->scope('subject-test')
            ->subject($subjectId)
            ->generator(function (int $attempt): string {
                return 'value-for-subject-' . $attempt;
            })
            ->generate();

        // Same subject should return same value
        $this->assertEquals($value1, $value2);
        $this->assertEquals('value-for-subject-0', $value1);
    }

    /** @test */
    public function it_can_generate_job_reference_style_values()
    {
        $jobReference = UniqueValue::make()
            ->scope('job-reference')
            ->generator(function (int $attempt): string {
                $year = date('Y');
                $baseNumber = str_pad((string) ($attempt + 1), 6, '0', STR_PAD_LEFT);
                return "JOB-{$year}-{$baseNumber}";
            })
            ->generate();

        $this->assertStringStartsWith('JOB-', $jobReference);
        $this->assertMatchesRegularExpression('/^JOB-\d{4}-\d{6}$/', $jobReference);
    }
} 