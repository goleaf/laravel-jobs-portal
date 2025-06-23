<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Universal Form Request for storing Language
 * Implements Laravel 12 best practices with Universal MCP patterns.
 */
class StoreLanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: Authorization check.
     */
    public function authorize(): bool
    {
        // Check if user has admin or manager permissions
        return Auth::check() && (
            Auth::user()->hasRole('admin') || 
            Auth::user()->hasRole('manager') ||
            Auth::user()->can('create-master-data')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: Comprehensive validation rules.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Language name in English
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Z\s\-\'\.]+$/',
                Rule::unique('languages', 'name')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) {
                    if (!$this->validateLanguageName($value)) {
                        $fail(__('validation.invalid_language_name'));
                    }
                },
            ],

            // Language code (ISO 639-1 or 639-2)
            'code' => [
                'required',
                'string',
                'min:2',
                'max:3',
                'alpha',
                'lowercase',
                Rule::unique('languages', 'code')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) {
                    if (!$this->validateLanguageCode($value)) {
                        $fail(__('validation.invalid_language_code'));
                    }
                },
            ],

            // Native name of the language
            'native_name' => [
                'sometimes',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-\'\.]+$/u', // Unicode support for native scripts
            ],

            // Language direction (LTR/RTL)
            'direction' => [
                'sometimes',
                'string',
                Rule::in(['ltr', 'rtl']),
            ],

            // Language family/group
            'family' => [
                'sometimes',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z\s\-]+$/',
            ],

            // ISO 639-3 code for extended language identification
            'iso_639_3' => [
                'sometimes',
                'string',
                'size:3',
                'alpha',
                'lowercase',
                Rule::unique('languages', 'iso_639_3')->whereNull('deleted_at'),
            ],

            // Flag indicating if language is active
            'is_active' => [
                'sometimes',
                'boolean',
            ],

            // Flag indicating if language is default
            'is_default' => [
                'sometimes',
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && $this->hasDefaultLanguage()) {
                        $fail(__('validation.default_language_exists'));
                    }
                },
            ],

            // Locale code for localization
            'locale' => [
                'sometimes',
                'string',
                'min:2',
                'max:10',
                'regex:/^[a-z]{2}(_[A-Z]{2})?$/', // en, en_US, fr_CA format
                Rule::unique('languages', 'locale')->whereNull('deleted_at'),
            ],

            // Language priority/order
            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999',
            ],

            // Additional metadata
            'description' => [
                'sometimes',
                'string',
                'max:500',
            ],

            // Flag image URL or path
            'flag_icon' => [
                'sometimes',
                'string',
                'max:255',
                'url',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: Multilingual error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required_field', ['field' => __('validation.attributes.language_name')]),
            'name.unique' => __('validation.unique_field', ['field' => __('validation.attributes.language_name')]),
            'name.regex' => __('validation.language_name_format'),
            
            'code.required' => __('validation.required_field', ['field' => __('validation.attributes.language_code')]),
            'code.unique' => __('validation.unique_field', ['field' => __('validation.attributes.language_code')]),
            'code.alpha' => __('validation.alpha_only', ['attribute' => __('validation.attributes.language_code')]),
            'code.lowercase' => __('validation.lowercase_only', ['attribute' => __('validation.attributes.language_code')]),
            
            'native_name.regex' => __('validation.native_name_format'),
            'direction.in' => __('validation.in_list', ['attribute' => __('validation.attributes.direction'), 'values' => 'ltr, rtl']),
            
            'iso_639_3.size' => __('validation.exact_length', ['attribute' => __('validation.attributes.iso_639_3'), 'length' => 3]),
            'iso_639_3.unique' => __('validation.unique_field', ['field' => __('validation.attributes.iso_639_3')]),
            
            'locale.regex' => __('validation.locale_format'),
            'locale.unique' => __('validation.unique_field', ['field' => __('validation.attributes.locale')]),
            
            'flag_icon.url' => __('validation.valid_url', ['attribute' => __('validation.attributes.flag_icon')]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.language_name'),
            'code' => __('validation.attributes.language_code'),
            'native_name' => __('validation.attributes.native_name'),
            'direction' => __('validation.attributes.direction'),
            'family' => __('validation.attributes.language_family'),
            'iso_639_3' => __('validation.attributes.iso_639_3'),
            'is_active' => __('validation.attributes.is_active'),
            'is_default' => __('validation.attributes.is_default'),
            'locale' => __('validation.attributes.locale'),
            'sort_order' => __('validation.attributes.sort_order'),
            'description' => __('validation.attributes.description'),
            'flag_icon' => __('validation.attributes.flag_icon'),
        ];
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: Enhanced validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Universal Pattern: Additional business logic validation
            if ($this->hasConflictingData()) {
                $validator->errors()->add('name', __('validation.conflicting_data'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Universal Pattern: Data normalization.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'is_default' => $this->boolean('is_default', false),
            'direction' => $this->direction ?? 'ltr',
            'sort_order' => $this->sort_order ?? 0,
        ]);

        // Normalize language code to lowercase
        if ($this->has('code')) {
            $this->merge([
                'code' => strtolower(trim($this->code)),
            ]);
        }

        // Normalize ISO 639-3 code
        if ($this->has('iso_639_3')) {
            $this->merge([
                'iso_639_3' => strtolower(trim($this->iso_639_3)),
            ]);
        }

        // Normalize locale
        if ($this->has('locale')) {
            $this->merge([
                'locale' => trim($this->locale),
            ]);
        }

        // Log the language creation attempt
        Log::info('Language creation attempt', [
            'name' => $this->name ?? null,
            'code' => $this->code ?? null,
            'user_id' => Auth::id(),
            'ip' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: Enhanced error handling.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->info('Store validation failed for StoreLanguageRequest', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->safe()->toArray(),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Universal Pattern: Custom business logic check.
     */
    private function hasConflictingData(): bool
    {
        // Add specific business logic here
        return false;
    }

    /**
     * Custom validation for language name.
     */
    private function validateLanguageName(string $name): bool
    {
        // Check against common language names
        $validLanguages = [
            'English', 'Spanish', 'French', 'German', 'Italian', 'Portuguese',
            'Russian', 'Chinese', 'Japanese', 'Korean', 'Arabic', 'Hindi',
            'Bengali', 'Turkish', 'Dutch', 'Swedish', 'Norwegian', 'Danish',
            // Add more as needed
        ];
        
        return in_array($name, $validLanguages) || 
               strlen($name) >= 2; // Basic fallback
    }

    /**
     * Custom validation for language code.
     */
    private function validateLanguageCode(string $code): bool
    {
        // ISO 639-1 codes (2 letters) or some common 3-letter codes
        $validCodes = [
            'en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'zh', 'ja', 'ko',
            'ar', 'hi', 'bn', 'tr', 'nl', 'sv', 'no', 'da', 'fi', 'pl',
            'cs', 'sk', 'hu', 'ro', 'bg', 'hr', 'sr', 'sl', 'et', 'lv',
            'lt', 'mt', 'ga', 'eu', 'ca', 'gl', 'cy', 'br', 'gd', 'kw',
        ];
        
        return in_array($code, $validCodes) || 
               (strlen($code) >= 2 && strlen($code) <= 3);
    }

    /**
     * Check if a default language already exists.
     */
    private function hasDefaultLanguage(): bool
    {
        return \DB::table('languages')
            ->where('is_default', true)
            ->whereNull('deleted_at')
            ->exists();
    }
}
