<?php

namespace App\Http\Requests\Foundation\Traits;

use Illuminate\Support\Facades\App;

/**
 * Multilingual Validation Trait
 *
 * Provides multilingual validation support across all request types:
 * - Dynamic error message translation
 * - Locale-specific validation rules
 * - Fallback language support
 * - Translation key generation
 * - Smart contextual error messages
 *
 * @version 1.0.0
 *
 * @since 2024-12-28
 */
trait MultilingualValidationTrait
{
    /**
     * Supported languages for validation messages
     */
    protected array $supportedLocales = [
        'en' => 'English',
        'lt' => 'Lithuanian',
        'de' => 'German',
        'fr' => 'French',
        'es' => 'Spanish',
        'it' => 'Italian',
        'pl' => 'Polish',
        'lv' => 'Latvian',
        'et' => 'Estonian',
        'ru' => 'Russian',
        'pt' => 'Portuguese',
        'nl' => 'Dutch',
    ];

    /**
     * Default fallback locale
     */
    protected string $fallbackLocale = 'en';

    /**
     * Get multilingual validation rules
     */
    protected function getMultilingualValidationRules(): array
    {
        $locale = app()->getLocale();
        $rules = [];

        // Add multilingual field validation
        $multilingualFields = ['name', 'title', 'description', 'content'];
        $supportedLocales = array_keys($this->supportedLocales);

        foreach ($multilingualFields as $field) {
            foreach ($supportedLocales as $localeCode) {
                $fieldKey = $field.'_'.$localeCode;
                if (array_key_exists($fieldKey, $this->all())) {
                    $rules[$fieldKey] = $this->getFieldValidationRules($field, $localeCode);
                }
            }
        }

        return $rules;
    }

    /**
     * Get multilingual error messages
     */
    protected function getMultilingualValidationMessages(): array
    {
        $locale = app()->getLocale();

        return [
            'required' => __('validation.required', [], $locale),
            'string' => __('validation.string', [], $locale),
            'email' => __('validation.email', [], $locale),
            'max.string' => __('validation.max.string', [], $locale),
            'min.string' => __('validation.min.string', [], $locale),
            'exists' => __('validation.exists', [], $locale),
            'unique' => __('validation.unique', [], $locale),
        ];
    }

    /**
     * Get multilingual attribute names
     */
    protected function getMultilingualValidationAttributes(): array
    {
        $locale = app()->getLocale();

        return [
            'name' => __('validation.attributes.name', [], $locale),
            'title' => __('validation.attributes.title', [], $locale),
            'description' => __('validation.attributes.description', [], $locale),
            'email' => __('validation.attributes.email', [], $locale),
            'phone' => __('validation.attributes.phone', [], $locale),
            'status' => __('validation.attributes.status', [], $locale),
        ];
    }

    /**
     * Get current locale with fallback
     */
    protected function getCurrentLocale(): string
    {
        $locale = App::getLocale();

        // Check if locale is supported
        if (! array_key_exists($locale, $this->supportedLocales)) {
            return $this->fallbackLocale;
        }

        return $locale;
    }

    /**
     * Get locale-specific validation rules
     */
    protected function getLocaleSpecificRules(string $locale): array
    {
        $rules = [];

        // Phone number validation by locale
        if (isset($this->getValidatedData()['phone'])) {
            $rules['phone'] = $this->getPhoneValidationRules($locale);
        }

        // Date format validation by locale
        if (isset($this->getValidatedData()['date'])) {
            $rules['date'] = $this->getDateValidationRules($locale);
        }

        // Currency validation by locale
        if (isset($this->getValidatedData()['salary']) || isset($this->getValidatedData()['amount'])) {
            $rules = array_merge($rules, $this->getCurrencyValidationRules($locale));
        }

        return $rules;
    }

    /**
     * Get multilingual field validation rules
     */
    protected function getMultilingualFieldRules(): array
    {
        $rules = [];
        $supportedLocales = array_keys($this->supportedLocales);

        // For fields that should have multilingual versions
        $multilingualFields = ['name', 'title', 'description', 'content'];

        foreach ($multilingualFields as $field) {
            foreach ($supportedLocales as $locale) {
                $fieldKey = $field.'_'.$locale;
                if (array_key_exists($fieldKey, $this->all())) {
                    $rules[$fieldKey] = $this->getFieldValidationRules($field, $locale);
                }
            }
        }

        return $rules;
    }

    /**
     * Get phone validation rules by locale
     */
    protected function getPhoneValidationRules(string $locale): array
    {
        $patterns = [
            'en' => '/^\+?[1-9]\d{1,14}$/', // International format
            'lt' => '/^(\+370|370|8)?[6-9]\d{7}$/', // Lithuanian format
            'de' => '/^(\+49|49|0)[1-9]\d{8,11}$/', // German format
            'fr' => '/^(\+33|33|0)[1-9]\d{8}$/', // French format
            'es' => '/^(\+34|34)?[6-9]\d{8}$/', // Spanish format
        ];

        $pattern = $patterns[$locale] ?? $patterns['en'];

        return [
            'required',
            'string',
            'regex:'.$pattern,
        ];
    }

    /**
     * Get date validation rules by locale
     */
    protected function getDateValidationRules(string $locale): array
    {
        $formats = [
            'en' => 'Y-m-d', // US/UK format
            'lt' => 'Y-m-d', // Lithuanian format
            'de' => 'd.m.Y', // German format
            'fr' => 'd/m/Y', // French format
            'es' => 'd/m/Y', // Spanish format
        ];

        $format = $formats[$locale] ?? $formats['en'];

        return [
            'required',
            'date',
            'date_format:'.$format,
        ];
    }

    /**
     * Get currency validation rules by locale
     */
    protected function getCurrencyValidationRules(string $locale): array
    {
        $currencies = [
            'en' => ['USD', 'GBP', 'EUR'],
            'lt' => ['EUR', 'USD'],
            'de' => ['EUR', 'USD'],
            'fr' => ['EUR', 'USD'],
            'es' => ['EUR', 'USD'],
        ];

        $allowedCurrencies = $currencies[$locale] ?? $currencies['en'];

        $rules = [];
        if (isset($this->all()['currency'])) {
            $rules['currency'] = ['required', 'string', 'in:'.implode(',', $allowedCurrencies)];
        }

        return $rules;
    }

    /**
     * Get field validation rules for specific locale
     */
    protected function getFieldValidationRules(string $field, string $locale): array
    {
        $baseRules = ['string'];

        // Add locale-specific character requirements
        switch ($locale) {
            case 'lt':
                $baseRules[] = 'regex:/^[\p{L}\p{N}\s\p{P}]*$/u'; // Lithuanian characters
                break;
            case 'de':
                $baseRules[] = 'regex:/^[\p{L}\p{N}\s\p{P}äöüßÄÖÜ]*$/u'; // German characters
                break;
            case 'fr':
                $baseRules[] = 'regex:/^[\p{L}\p{N}\s\p{P}àâäéèêëïîôöùûüÿçÀÂÄÉÈÊËÏÎÔÖÙÛÜŸÇ]*$/u'; // French characters
                break;
            default:
                $baseRules[] = 'regex:/^[\p{L}\p{N}\s\p{P}]*$/u'; // Unicode letters, numbers, spaces, punctuation
        }

        // Field-specific rules
        switch ($field) {
            case 'name':
            case 'title':
                $baseRules[] = 'max:255';
                break;
            case 'description':
            case 'content':
                $baseRules[] = 'max:5000';
                break;
        }

        return $baseRules;
    }

    /**
     * Get base validation messages for locale
     */
    protected function getBaseValidationMessages(string $locale): array
    {
        return [
            'required' => __('validation.required', [], $locale),
            'string' => __('validation.string', [], $locale),
            'email' => __('validation.email', [], $locale),
            'max.string' => __('validation.max.string', [], $locale),
            'min.string' => __('validation.min.string', [], $locale),
            'exists' => __('validation.exists', [], $locale),
            'unique' => __('validation.unique', [], $locale),
            'integer' => __('validation.integer', [], $locale),
            'numeric' => __('validation.numeric', [], $locale),
            'date' => __('validation.date', [], $locale),
            'date_format' => __('validation.date_format', [], $locale),
            'in' => __('validation.in', [], $locale),
            'regex' => __('validation.regex', [], $locale),
        ];
    }

    /**
     * Get domain-specific messages for locale
     */
    protected function getDomainSpecificMessages(string $locale): array
    {
        $domain = $this->getValidationDomain();

        return [
            'job_title.required' => __("validation.{$domain}.job_title_required", [], $locale),
            'company_name.required' => __("validation.{$domain}.company_name_required", [], $locale),
            'salary.numeric' => __("validation.{$domain}.salary_numeric", [], $locale),
            'experience.integer' => __("validation.{$domain}.experience_integer", [], $locale),
            'location.required' => __("validation.{$domain}.location_required", [], $locale),
        ];
    }

    /**
     * Get contextual messages for locale
     */
    protected function getContextualMessages(string $locale): array
    {
        $requestContext = $this->getRequestContext();

        return [
            'name.required' => __("validation.contextual.{$requestContext}.name_required", [], $locale),
            'description.max' => __("validation.contextual.{$requestContext}.description_max", [], $locale),
        ];
    }

    /**
     * Get validation domain (master_data, business_logic, etc.)
     */
    protected function getValidationDomain(): string
    {
        $class = get_class($this);

        if (str_contains($class, 'MasterData')) {
            return 'master_data';
        } elseif (str_contains($class, 'BusinessLogic')) {
            return 'business_logic';
        } elseif (str_contains($class, 'Financial')) {
            return 'financial';
        } elseif (str_contains($class, 'Communication')) {
            return 'communication';
        } elseif (str_contains($class, 'Api')) {
            return 'api';
        }

        return 'general';
    }

    /**
     * Get request context (create, update, delete, etc.)
     */
    protected function getRequestContext(): string
    {
        $class = get_class($this);

        if (str_contains($class, 'Store') || str_contains($class, 'Create')) {
            return 'create';
        } elseif (str_contains($class, 'Update')) {
            return 'update';
        } elseif (str_contains($class, 'Delete') || str_contains($class, 'Destroy')) {
            return 'delete';
        }

        return 'general';
    }

    /**
     * Generate smart translation key
     */
    protected function generateTranslationKey(string $field, string $rule): string
    {
        $domain = $this->getValidationDomain();
        $context = $this->getRequestContext();

        return "validation.{$domain}.{$context}.{$field}.{$rule}";
    }

    /**
     * Get translation with fallback
     */
    protected function getTranslationWithFallback(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? $this->getCurrentLocale();

        // Try specific locale
        $translation = __($key, $replace, $locale);

        // Fallback to default locale if translation not found
        if ($translation === $key && $locale !== $this->fallbackLocale) {
            $translation = __($key, $replace, $this->fallbackLocale);
        }

        // Final fallback to generic message
        if ($translation === $key) {
            return __('validation.general_error', $replace, $this->fallbackLocale);
        }

        return $translation;
    }
}
