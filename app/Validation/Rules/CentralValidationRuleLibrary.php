<?php

namespace App\Validation\Rules;

/**
 * Central Validation Rule Library
 *
 * Provides centralized repository of reusable validation rules
 * across all business domains and request types
 *
 * @version 1.0.0
 *
 * @since 2024-12-28
 */
class CentralValidationRuleLibrary
{
    /**
     * Common string validation rules
     */
    public static function getStringRules(int $maxLength = 255): array
    {
        return [
            'required',
            'string',
            'max:'.$maxLength,
            'regex:/^[\p{L}\p{N}\s\p{P}]*$/u', // Unicode letters, numbers, spaces, punctuation
        ];
    }

    /**
     * Email validation rules
     */
    public static function getEmailRules(): array
    {
        return [
            'required',
            'string',
            'email:rfc,dns',
            'max:255',
        ];
    }

    /**
     * Phone validation rules
     */
    public static function getPhoneRules(): array
    {
        return [
            'required',
            'string',
            'regex:/^\+?[1-9]\d{1,14}$/', // International format
        ];
    }

    /**
     * URL validation rules
     */
    public static function getUrlRules(): array
    {
        return [
            'required',
            'url',
            'max:2000',
        ];
    }

    /**
     * Date validation rules
     */
    public static function getDateRules(): array
    {
        return [
            'required',
            'date',
            'date_format:Y-m-d',
        ];
    }

    /**
     * Integer validation rules
     */
    public static function getIntegerRules(int $min = 0, ?int $max = null): array
    {
        $rules = [
            'required',
            'integer',
            'min:'.$min,
        ];

        if ($max !== null) {
            $rules[] = 'max:'.$max;
        }

        return $rules;
    }

    /**
     * Numeric validation rules
     */
    public static function getNumericRules(float $min = 0, ?float $max = null): array
    {
        $rules = [
            'required',
            'numeric',
            'min:'.$min,
        ];

        if ($max !== null) {
            $rules[] = 'max:'.$max;
        }

        return $rules;
    }

    /**
     * Boolean validation rules
     */
    public static function getBooleanRules(): array
    {
        return [
            'required',
            'boolean',
        ];
    }

    /**
     * Status validation rules
     */
    public static function getStatusRules(): array
    {
        return [
            'required',
            'string',
            'in:active,inactive',
        ];
    }

    /**
     * ID validation rules (for foreign keys)
     */
    public static function getIdRules(?string $table = null): array
    {
        $rules = [
            'required',
            'integer',
            'min:1',
        ];

        if ($table) {
            $rules[] = 'exists:'.$table.',id';
        }

        return $rules;
    }

    /**
     * Optional ID validation rules
     */
    public static function getOptionalIdRules(?string $table = null): array
    {
        $rules = [
            'sometimes',
            'nullable',
            'integer',
            'min:1',
        ];

        if ($table) {
            $rules[] = 'exists:'.$table.',id';
        }

        return $rules;
    }

    /**
     * Job portal specific validation rules
     */
    public static function getJobTitleRules(): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:100',
            'regex:/^[a-zA-Z0-9\s\-\/\+\&\(\)\.]+$/', // Job title characters
        ];
    }

    public static function getSalaryRules(): array
    {
        return [
            'required',
            'numeric',
            'min:0',
            'max:9999999.99',
        ];
    }

    public static function getExperienceRules(): array
    {
        return [
            'required',
            'integer',
            'min:0',
            'max:50',
        ];
    }

    public static function getDescriptionRules(int $maxLength = 5000): array
    {
        return [
            'required',
            'string',
            'min:10',
            'max:'.$maxLength,
        ];
    }

    /**
     * File upload validation rules
     */
    public static function getImageRules(): array
    {
        return [
            'required',
            'image',
            'mimes:jpeg,png,jpg,gif,svg',
            'max:2048', // 2MB max
        ];
    }

    public static function getDocumentRules(): array
    {
        return [
            'required',
            'file',
            'mimes:pdf,doc,docx,txt',
            'max:5120', // 5MB max
        ];
    }

    /**
     * Password validation rules
     */
    public static function getPasswordRules(): array
    {
        return [
            'required',
            'string',
            'min:12',
            'max:255',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        ];
    }

    public static function getPasswordConfirmationRules(): array
    {
        return [
            'required',
            'string',
            'same:password',
        ];
    }

    /**
     * Array validation rules
     */
    public static function getArrayRules(int $min = 1, ?int $max = null): array
    {
        $rules = [
            'required',
            'array',
            'min:'.$min,
        ];

        if ($max !== null) {
            $rules[] = 'max:'.$max;
        }

        return $rules;
    }

    /**
     * Optional array validation rules
     */
    public static function getOptionalArrayRules(?int $max = null): array
    {
        $rules = [
            'sometimes',
            'array',
        ];

        if ($max !== null) {
            $rules[] = 'max:'.$max;
        }

        return $rules;
    }

    /**
     * Multilingual field validation rules
     */
    public static function getMultilingualRules(string $field, array $locales = ['en', 'lt']): array
    {
        $rules = [];

        foreach ($locales as $locale) {
            $fieldKey = $field.'_'.$locale;
            $rules[$fieldKey] = self::getStringRules();
        }

        return $rules;
    }

    /**
     * Get validation rule by name
     */
    public static function getRule(string $ruleName, ...$params): array
    {
        return match ($ruleName) {
            'string' => self::getStringRules($params[0] ?? 255),
            'email' => self::getEmailRules(),
            'phone' => self::getPhoneRules(),
            'url' => self::getUrlRules(),
            'date' => self::getDateRules(),
            'integer' => self::getIntegerRules($params[0] ?? 0, $params[1] ?? null),
            'numeric' => self::getNumericRules($params[0] ?? 0, $params[1] ?? null),
            'boolean' => self::getBooleanRules(),
            'status' => self::getStatusRules(),
            'id' => self::getIdRules($params[0] ?? null),
            'optional_id' => self::getOptionalIdRules($params[0] ?? null),
            'job_title' => self::getJobTitleRules(),
            'salary' => self::getSalaryRules(),
            'experience' => self::getExperienceRules(),
            'description' => self::getDescriptionRules($params[0] ?? 5000),
            'image' => self::getImageRules(),
            'document' => self::getDocumentRules(),
            'password' => self::getPasswordRules(),
            'password_confirmation' => self::getPasswordConfirmationRules(),
            'array' => self::getArrayRules($params[0] ?? 1, $params[1] ?? null),
            'optional_array' => self::getOptionalArrayRules($params[0] ?? null),
            default => []
        };
    }
}
