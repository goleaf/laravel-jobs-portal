<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * ValidationHelper - Modern Laravel validation utilities
 *
 * Integrates Laravel 12.16+ features including Arr::hasAll()
 * for enhanced data validation and integrity checks
 */
class ValidationHelper
{
    /**
     * Validate job application data using Arr::hasAll()
     */
    public static function validateJobApplicationData(array $data): bool
    {
        $requiredFields = [
            'user_id',
            'job_id',
            'resume',
            'cover_letter',
        ];

        return Arr::hasAll($data, $requiredFields);
    }

    /**
     * Validate nested user profile data with dot notation
     */
    public static function validateUserProfileData(array $data): bool
    {
        $nestedRequiredFields = [
            'personal.first_name',
            'personal.last_name',
            'personal.email',
            'contact.phone',
            'contact.address',
        ];

        return Arr::hasAll($data, ['personal', 'contact']) &&
               Arr::hasAll($data, $nestedRequiredFields);
    }

    /**
     * Validate company registration data
     */
    public static function validateCompanyData(array $data): bool
    {
        $requiredFields = [
            'name',
            'email',
            'industry_id',
            'company_size_id',
            'location',
        ];

        return Arr::hasAll($data, $requiredFields);
    }

    /**
     * Validate job posting data with comprehensive checks
     */
    public static function validateJobPostingData(array $data): bool
    {
        $coreFields = ['title', 'description', 'company_id', 'job_category_id'];
        $detailFields = ['requirements', 'benefits', 'salary_range'];

        // Check core fields exist
        if (! Arr::hasAll($data, $coreFields)) {
            return false;
        }

        // Check at least one detail field exists
        return Arr::hasAny($data, $detailFields);
    }

    /**
     * Enhanced email template validation
     */
    public static function validateEmailTemplateData(array $data): array
    {
        $requiredFields = ['template_name', 'subject', 'body', 'variables'];

        if (! Arr::hasAll($data, $requiredFields)) {
            throw new \InvalidArgumentException('Email template missing required fields: '.implode(', ', $requiredFields));
        }

        // Validate variables is valid JSON
        if (isset($data['variables']) && ! is_array(json_decode($data['variables'], true))) {
            throw new \InvalidArgumentException('Variables field must contain valid JSON');
        }

        return $data;
    }

    /**
     * Validate media upload data using modern techniques
     */
    public static function validateMediaUploadData(array $data): bool
    {
        $requiredFields = ['name', 'file_name', 'mime_type', 'size'];
        $optionalFields = ['alt_text', 'caption', 'description'];

        // Must have all required fields
        if (! Arr::hasAll($data, $requiredFields)) {
            return false;
        }

        // If custom_properties exists, validate its structure
        if (isset($data['custom_properties'])) {
            $customProps = is_string($data['custom_properties'])
                ? json_decode($data['custom_properties'], true)
                : $data['custom_properties'];

            return is_array($customProps);
        }

        return true;
    }

    /**
     * Group validation results for batch processing
     * Inspired by Laravel collection groupBy techniques
     */
    public static function groupValidationResults(array $items, callable $validator): Collection
    {
        return collect($items)->groupBy(function ($item) use ($validator) {
            return $validator($item) ? 'valid' : 'invalid';
        });
    }

    /**
     * Validate API request data with comprehensive checks
     */
    public static function validateApiRequestData(array $data, array $requiredFields, array $optionalFields = []): array
    {
        // Check all required fields exist
        if (! Arr::hasAll($data, $requiredFields)) {
            $missing = array_diff($requiredFields, array_keys($data));
            throw new \InvalidArgumentException('Missing required fields: '.implode(', ', $missing));
        }

        // Filter to only allowed fields
        $allowedFields = array_merge($requiredFields, $optionalFields);

        return Arr::only($data, $allowedFields);
    }

    /**
     * Validate search filters with modern Laravel techniques
     */
    public static function validateSearchFilters(array $filters): bool
    {
        $allowedFilters = [
            'keyword',
            'location',
            'job_category_id',
            'company_size_id',
            'salary_min',
            'salary_max',
            'experience_level',
            'job_type',
        ];

        // At least one filter must be provided
        if (empty($filters)) {
            return false;
        }

        // All provided filters must be in allowed list
        return empty(array_diff(array_keys($filters), $allowedFilters));
    }
}
