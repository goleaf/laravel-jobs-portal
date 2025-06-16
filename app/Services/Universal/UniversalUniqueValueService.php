<?php

namespace App\Services\Universal;

use JustBetter\UniqueValues\Support\UniqueValue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Universal Unique Value Service
 * 
 * Integrates Laravel Unique Values package with concurrency support
 * for generating unique identifiers across the job portal application.
 * 
 * Features:
 * - Job reference numbers
 * - Application tracking codes
 * - Candidate identification codes
 * - Company registration codes
 * - SEO-friendly slugs
 * - Invoice/transaction numbers
 */
class UniversalUniqueValueService
{
    /**
     * Generate unique job reference number.
     * Format: JOB-YYYY-XXXXXX (e.g., JOB-2025-000001)
     */
    public function generateJobReference(int $jobId = null): string
    {
        return UniqueValue::make()
            ->scope('job-reference')
            ->subject($jobId)
            ->attempts(5)
            ->generator(function (int $attempt): string {
                $year = date('Y');
                $baseNumber = str_pad((string) ($attempt + 1), 6, '0', STR_PAD_LEFT);
                return "JOB-{$year}-{$baseNumber}";
            })
            ->generate();
    }

    /**
     * Generate unique application tracking code.
     * Format: APP-YYYYMMDD-XXXXX (e.g., APP-20250615-00001)
     */
    public function generateApplicationCode(int $applicationId = null): string
    {
        return UniqueValue::make()
            ->scope('application-code')
            ->subject($applicationId)
            ->attempts(5)
            ->generator(function (int $attempt): string {
                $date = date('Ymd');
                $baseNumber = str_pad((string) ($attempt + 1), 5, '0', STR_PAD_LEFT);
                return "APP-{$date}-{$baseNumber}";
            })
            ->generate();
    }

    /**
     * Generate unique candidate identification code.
     * Format: CAN-XXXXXX (e.g., CAN-000001)
     */
    public function generateCandidateCode(int $candidateId = null): string
    {
        return UniqueValue::make()
            ->scope('candidate-code')
            ->subject($candidateId)
            ->attempts(5)
            ->generator(function (int $attempt): string {
                $baseNumber = str_pad((string) ($attempt + 1), 6, '0', STR_PAD_LEFT);
                return "CAN-{$baseNumber}";
            })
            ->generate();
    }

    /**
     * Generate unique company registration code.
     * Format: COM-YYYY-XXXXX (e.g., COM-2025-00001)
     */
    public function generateCompanyCode(int $companyId = null): string
    {
        return UniqueValue::make()
            ->scope('company-code')
            ->subject($companyId)
            ->attempts(5)
            ->generator(function (int $attempt): string {
                $year = date('Y');
                $baseNumber = str_pad((string) ($attempt + 1), 5, '0', STR_PAD_LEFT);
                return "COM-{$year}-{$baseNumber}";
            })
            ->generate();
    }

    /**
     * Generate unique SEO-friendly slug.
     * Handles concurrency for duplicate titles/names.
     */
    public function generateUniqueSlug(string $title, string $scope = 'general-slug', int $subjectId = null): string
    {
        $baseSlug = Str::slug($title);
        
        return UniqueValue::make()
            ->scope($scope)
            ->subject($subjectId)
            ->attempts(10)
            ->generator(function (int $attempt) use ($baseSlug): string {
                return $attempt === 0 ? $baseSlug : "{$baseSlug}-{$attempt}";
            })
            ->generate();
    }

    /**
     * Generate unique invoice/transaction number.
     * Format: INV-YYYYMMDD-XXXXX (e.g., INV-20250615-00001)
     */
    public function generateInvoiceNumber(int $transactionId = null): string
    {
        return UniqueValue::make()
            ->scope('invoice-number')
            ->subject($transactionId)
            ->attempts(5)
            ->generator(function (int $attempt): string {
                $date = date('Ymd');
                $baseNumber = str_pad((string) ($attempt + 1), 5, '0', STR_PAD_LEFT);
                return "INV-{$date}-{$baseNumber}";
            })
            ->generate();
    }

    /**
     * Generate unique API key.
     * Format: 32-character alphanumeric string
     */
    public function generateApiKey(int $userId = null): string
    {
        return UniqueValue::make()
            ->scope('api-key')
            ->subject($userId)
            ->attempts(3)
            ->generator(function (int $attempt): string {
                return Str::random(32);
            })
            ->generate();
    }

    /**
     * Generate unique order/booking reference.
     * Format: ORD-YYYYMMDD-HHMMSS-XXX (e.g., ORD-20250615-143022-001)
     */
    public function generateOrderReference(int $orderId = null): string
    {
        return UniqueValue::make()
            ->scope('order-reference')
            ->subject($orderId)
            ->attempts(5)
            ->generator(function (int $attempt): string {
                $timestamp = date('Ymd-His');
                $suffix = str_pad((string) ($attempt + 1), 3, '0', STR_PAD_LEFT);
                return "ORD-{$timestamp}-{$suffix}";
            })
            ->generate();
    }

    /**
     * Generate custom unique value with flexible pattern.
     */
    public function generateCustomUnique(
        string $scope,
        callable $generator,
        int $subjectId = null,
        int $maxAttempts = 3
    ): string {
        try {
            return UniqueValue::make()
                ->scope($scope)
                ->subject($subjectId)
                ->attempts($maxAttempts)
                ->generator($generator)
                ->generate();
        } catch (Exception $e) {
            Log::error('Failed to generate unique value', [
                'scope' => $scope,
                'subject_id' => $subjectId,
                'max_attempts' => $maxAttempts,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate batch of unique values for bulk operations.
     */
    public function generateBatch(string $type, array $subjectIds): array
    {
        $results = [];
        
        // Validate the type parameter
        $validTypes = [
            'job-reference',
            'application-code', 
            'candidate-code',
            'company-code',
            'invoice-number',
            'order-reference',
            'api-key'
        ];
        
        if (!in_array($type, $validTypes)) {
            throw new Exception("Invalid batch generation type: {$type}. Valid types are: " . implode(', ', $validTypes));
        }
        
        foreach ($subjectIds as $subjectId) {
            try {
                $value = match ($type) {
                    'job-reference' => $this->generateJobReference($subjectId),
                    'application-code' => $this->generateApplicationCode($subjectId),
                    'candidate-code' => $this->generateCandidateCode($subjectId),
                    'company-code' => $this->generateCompanyCode($subjectId),
                    'invoice-number' => $this->generateInvoiceNumber($subjectId),
                    'order-reference' => $this->generateOrderReference($subjectId),
                    'api-key' => $this->generateApiKey($subjectId),
                    default => throw new Exception("Unsupported type: {$type}")
                };
                
                $results[$subjectId] = $value;
            } catch (Exception $e) {
                Log::error('Failed to generate value in batch', [
                    'type' => $type,
                    'subject_id' => $subjectId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
        
        return $results;
    }

    /**
     * Get statistics about unique value generation.
     */
    public function getGenerationStats(): array
    {
        return [
            'scopes' => [
                'job-reference',
                'application-code', 
                'candidate-code',
                'company-code',
                'invoice-number',
                'order-reference',
                'api-key',
                'general-slug',
            ],
            'configuration' => [
                'block_for_seconds' => config('unique-values.block_for', 10),
                'default_attempts' => 3,
                'max_attempts' => 10,
            ],
            'patterns' => [
                'job-reference' => 'JOB-YYYY-XXXXXX',
                'application-code' => 'APP-YYYYMMDD-XXXXX',
                'candidate-code' => 'CAN-XXXXXX',
                'company-code' => 'COM-YYYY-XXXXX',
                'invoice-number' => 'INV-YYYYMMDD-XXXXX',
                'order-reference' => 'ORD-YYYYMMDD-HHMMSS-XXX',
                'api-key' => '32-character alphanumeric',
                'slug' => 'seo-friendly-slug[-N]',
            ],
        ];
    }
} 