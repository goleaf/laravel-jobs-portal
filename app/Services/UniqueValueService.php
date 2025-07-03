<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use JustBetter\UniqueValues\Support\UniqueValue;

/**
 * Job Portal Unique Value Service
 *
 * Handles generation of unique identifiers for various job portal entities
 * using the Laravel Unique Values package with concurrency support
 */
class UniqueValueService
{
    /**
     * Generate unique job reference number
     */
    public function generateJobReference(?string $companyPrefix = null): string
    {
        $prefix = $companyPrefix ? strtoupper(substr($companyPrefix, 0, 3)) : 'JOB';
        $year = Carbon::now()->format('Y');

        return UniqueValue::make()
            ->scope('job-references')
            ->attempts(10)
            ->generator(function (int $attempt) use ($prefix, $year): string {
                $baseNumber = str_pad((string) (1000 + $attempt), 4, '0', STR_PAD_LEFT);

                return "{$prefix}-{$year}-{$baseNumber}";
            })
            ->generate();
    }

    /**
     * Generate unique application reference
     */
    public function generateApplicationReference(int $jobId, int $candidateId): string
    {
        return UniqueValue::make()
            ->scope('application-references')
            ->subject("job-{$jobId}-candidate-{$candidateId}")
            ->attempts(5)
            ->generator(function (int $attempt) use ($jobId, $candidateId): string {
                $timestamp = Carbon::now()->format('ymd');
                $suffix = $attempt > 0 ? "-{$attempt}" : '';

                return "APP-{$timestamp}-{$jobId}-{$candidateId}{$suffix}";
            })
            ->generate();
    }

    /**
     * Generate unique company slug
     */
    public function generateCompanySlug(string $companyName, ?int $companyId = null): string
    {
        $baseSlug = Str::slug($companyName);
        $subject = $companyId ? "company-{$companyId}" : null;

        return UniqueValue::make()
            ->scope('company-slugs')
            ->when($subject, fn ($builder) => $builder->subject($subject))
            ->attempts(20)
            ->generator(function (int $attempt) use ($baseSlug): string {
                return $attempt === 0 ? $baseSlug : "{$baseSlug}-{$attempt}";
            })
            ->generate();
    }

    /**
     * Generate unique user reference code
     */
    public function generateUserReference(string $userType = 'candidate'): string
    {
        $prefix = match ($userType) {
            'employer' => 'EMP',
            'admin' => 'ADM',
            default => 'CAN',
        };

        return UniqueValue::make()
            ->scope("user-references-{$userType}")
            ->attempts(15)
            ->generator(function (int $attempt) use ($prefix): string {
                $timestamp = Carbon::now()->format('ymd');
                $random = strtoupper(Str::random(3));
                $counter = str_pad((string) $attempt, 3, '0', STR_PAD_LEFT);

                return "{$prefix}-{$timestamp}-{$random}-{$counter}";
            })
            ->generate();
    }

    /**
     * Generate unique invoice number
     */
    public function generateInvoiceNumber(int $companyId): string
    {
        return UniqueValue::make()
            ->scope('invoice-numbers')
            ->attempts(10)
            ->generator(function (int $attempt) use ($companyId): string {
                $year = Carbon::now()->format('Y');
                $month = Carbon::now()->format('m');
                $counter = str_pad((string) (1000 + $attempt), 4, '0', STR_PAD_LEFT);

                return "INV-{$year}{$month}-{$companyId}-{$counter}";
            })
            ->generate();
    }

    /**
     * Generate unique resume filename
     */
    public function generateResumeFilename(int $candidateId, string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $baseName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));

        return UniqueValue::make()
            ->scope('resume-filenames')
            ->subject("candidate-{$candidateId}")
            ->attempts(5)
            ->generator(function (int $attempt) use ($candidateId, $baseName, $extension): string {
                $timestamp = Carbon::now()->format('YmdHis');
                $suffix = $attempt > 0 ? "-{$attempt}" : '';

                return "resume-{$candidateId}-{$baseName}-{$timestamp}{$suffix}.{$extension}";
            })
            ->generate();
    }

    /**
     * Generate unique job posting slug
     */
    public function generateJobSlug(string $jobTitle, int $companyId): string
    {
        $baseSlug = Str::slug($jobTitle);

        return UniqueValue::make()
            ->scope('job-slugs')
            ->attempts(15)
            ->generator(function (int $attempt) use ($baseSlug, $companyId): string {
                if ($attempt === 0) {
                    return $baseSlug;
                }

                return "{$baseSlug}-{$companyId}-{$attempt}";
            })
            ->generate();
    }

    /**
     * Generate unique subscription code
     */
    public function generateSubscriptionCode(string $planType): string
    {
        $prefix = strtoupper(substr($planType, 0, 3));

        return UniqueValue::make()
            ->scope('subscription-codes')
            ->attempts(10)
            ->generator(function (int $attempt) use ($prefix): string {
                $year = Carbon::now()->format('y');
                $month = Carbon::now()->format('m');
                $random = strtoupper(Str::random(4));
                $counter = str_pad((string) $attempt, 3, '0', STR_PAD_LEFT);

                return "{$prefix}-{$year}{$month}-{$random}-{$counter}";
            })
            ->generate();
    }

    /**
     * Generate unique API key
     */
    public function generateApiKey(int $companyId): string
    {
        return UniqueValue::make()
            ->scope('api-keys')
            ->subject("company-{$companyId}")
            ->attempts(3)
            ->generator(function (int $attempt) use ($companyId): string {
                $prefix = 'jp'; // job portal
                $timestamp = Carbon::now()->format('ymdH');
                $random = Str::random(32);
                $suffix = $attempt > 0 ? $attempt : '';

                return "{$prefix}_{$timestamp}_{$companyId}_{$random}{$suffix}";
            })
            ->generate();
    }

    /**
     * Generate unique verification token
     */
    public function generateVerificationToken(string $type, int $userId): string
    {
        return UniqueValue::make()
            ->scope("verification-tokens-{$type}")
            ->subject("user-{$userId}")
            ->attempts(3)
            ->generator(function (int $attempt) use ($type, $userId): string {
                $prefix = strtoupper(substr($type, 0, 3));
                $timestamp = Carbon::now()->format('ymdHis');
                $random = Str::random(16);
                $suffix = $attempt > 0 ? "-{$attempt}" : '';

                return "{$prefix}-{$timestamp}-{$userId}-{$random}{$suffix}";
            })
            ->generate();
    }

    /**
     * Generate unique payment reference
     */
    public function generatePaymentReference(int $companyId, string $planType): string
    {
        return UniqueValue::make()
            ->scope('payment-references')
            ->attempts(8)
            ->generator(function (int $attempt) use ($companyId, $planType): string {
                $prefix = 'PAY';
                $planCode = strtoupper(substr($planType, 0, 3));
                $timestamp = Carbon::now()->format('ymdHi');
                $counter = str_pad((string) $attempt, 3, '0', STR_PAD_LEFT);

                return "{$prefix}-{$planCode}-{$timestamp}-{$companyId}-{$counter}";
            })
            ->generate();
    }

    /**
     * Generate unique interview code
     */
    public function generateInterviewCode(int $applicationId): string
    {
        return UniqueValue::make()
            ->scope('interview-codes')
            ->subject("application-{$applicationId}")
            ->attempts(5)
            ->generator(function (int $attempt) use ($applicationId): string {
                $date = Carbon::now()->format('md');
                $random = strtoupper(Str::random(6));
                $suffix = $attempt > 0 ? "-{$attempt}" : '';

                return "INT-{$date}-{$applicationId}-{$random}{$suffix}";
            })
            ->generate();
    }

    /**
     * Custom unique value generator
     */
    public function generateCustom(string $scope, callable $generator, int $attempts = 5, ?string $subject = null): string
    {
        $builder = UniqueValue::make()
            ->scope($scope)
            ->attempts($attempts)
            ->generator($generator);

        if ($subject) {
            $builder->subject($subject);
        }

        return $builder->generate();
    }
}
