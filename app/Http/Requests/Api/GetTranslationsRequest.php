<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class GetTranslationsRequest
 * Enterprise-grade validation for API Get Translations operations
 */
class GetTranslationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
                Rule::exists('languages', 'iso_code')->where('is_active', 1),
            ],
            'namespaces' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'namespaces.*' => [
                'string',
                'regex:/^[a-zA-Z0-9_\-\/]+$/',
                'max:100',
            ],
            'keys' => [
                'sometimes',
                'array',
                'max:100',
            ],
            'keys.*' => [
                'string',
                'regex:/^[a-zA-Z0-9_\-\.\/]+$/',
                'max:200',
            ],
            'search' => [
                'sometimes',
                'string',
                'min:1',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_\.]+$/u',
            ],
            'include_fallback' => [
                'sometimes',
                'boolean',
            ],
            'fallback_locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'format' => [
                'sometimes',
                'string',
                'in:json,flat,nested,array',
            ],
            'include_meta' => [
                'sometimes',
                'boolean',
            ],
            'meta_fields' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'meta_fields.*' => [
                'string',
                'in:last_updated,translator,status,pluralization,context',
            ],
            'only_missing' => [
                'sometimes',
                'boolean',
            ],
            'include_pluralization' => [
                'sometimes',
                'boolean',
            ],
            'context' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\-]+$/',
            ],
            'cache_key' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_\-\.]+$/',
            ],
            'cache_ttl' => [
                'sometimes',
                'integer',
                'min:60',
                'max:86400',
            ],
            'compression' => [
                'sometimes',
                'string',
                'in:none,gzip,deflate',
            ],
            'version' => [
                'sometimes',
                'string',
                'regex:/^[0-9]+\.[0-9]+(\.[0-9]+)?$/',
            ],
            'api_version' => [
                'sometimes',
                'string',
                'in:v1,v2,latest',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'locale' => app()->getLocale(),
            'include_fallback' => true,
            'fallback_locale' => 'en',
            'format' => 'nested',
            'include_meta' => false,
            'only_missing' => false,
            'include_pluralization' => true,
            'cache_ttl' => 3600,
            'compression' => 'none',
            'api_version' => 'v1',
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'locale.regex' => __('validation.custom.translations.locale_format'),
            'locale.exists' => __('validation.custom.translations.locale_not_supported'),
            'namespaces.max' => __('validation.custom.translations.namespaces_limit'),
            'namespaces.*.regex' => __('validation.custom.translations.namespace_format'),
            'keys.max' => __('validation.custom.translations.keys_limit'),
            'keys.*.regex' => __('validation.custom.translations.key_format'),
            'search.regex' => __('validation.custom.translations.search_format'),
            'fallback_locale.regex' => __('validation.custom.translations.fallback_format'),
            'format.in' => __('validation.custom.translations.format_invalid'),
            'meta_fields.max' => __('validation.custom.translations.meta_fields_limit'),
            'meta_fields.*.in' => __('validation.custom.translations.meta_field_invalid'),
            'context.regex' => __('validation.custom.translations.context_format'),
            'cache_key.regex' => __('validation.custom.translations.cache_key_format'),
            'cache_ttl.max' => __('validation.custom.translations.cache_ttl_limit'),
            'compression.in' => __('validation.custom.translations.compression_invalid'),
            'version.regex' => __('validation.custom.translations.version_format'),
            'api_version.in' => __('validation.custom.translations.api_version_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('locale')) {
            $this->merge(['locale' => strtolower(trim($this->locale))]);
        }

        if ($this->has('fallback_locale')) {
            $this->merge(['fallback_locale' => strtolower(trim($this->fallback_locale))]);
        }

        foreach (['namespaces', 'keys', 'meta_fields'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }

        if ($this->has('search')) {
            $this->merge(['search' => trim($this->search)]);
        }

        if ($this->has('context')) {
            $this->merge(['context' => trim($this->context)]);
        }
    }
}
