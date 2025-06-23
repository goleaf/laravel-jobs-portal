<?php

namespace App\Http\Requests\Financial;

use App\Models\SalaryCurrency;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Comprehensive Form Request for storing Salary Currencies
 * Implements Laravel 12 best practices with financial currency validation.
 */
class StoreSalaryCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only financial administrators can manage currencies.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Only admin, financial managers, or users with currency management permissions
        return $user->hasRole('Admin') || 
               $user->hasRole('Financial Manager') || 
               $user->hasRole('System Administrator') ||
               $user->can('manage-currencies');
    }

    /**
     * Get the validation rules that apply to the request.
     * Comprehensive currency data validation.
     */
    public function rules(): array
    {
        return [
            // Basic currency information
            'currency_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'unique:salary_currencies,currency_name',
                'regex:/^[a-zA-Z\s\(\)\-]+$/', // Only letters, spaces, parentheses, hyphens
            ],
            
            'iso_code' => [
                'required',
                'string',
                'size:3',
                'uppercase',
                'unique:salary_currencies,iso_code',
                'regex:/^[A-Z]{3}$/', // ISO 4217 standard
            ],
            
            'numeric_code' => [
                'nullable',
                'string',
                'size:3',
                'unique:salary_currencies,numeric_code',
                'regex:/^[0-9]{3}$/', // ISO 4217 numeric code
            ],

            // Currency symbol and display
            'currency_symbol' => [
                'required',
                'string',
                'min:1',
                'max:10',
            ],
            
            'symbol_position' => [
                'required',
                'string',
                Rule::in(['before', 'after']),
            ],
            
            'decimal_separator' => [
                'required',
                'string',
                'size:1',
                Rule::in(['.', ',']),
            ],
            
            'thousands_separator' => [
                'nullable',
                'string',
                'size:1',
                Rule::in([',', '.', ' ', "'"]),
            ],
            
            'decimal_places' => [
                'required',
                'integer',
                'min:0',
                'max:8',
            ],

            // Exchange rate and financial data
            'exchange_rate_to_usd' => [
                'required',
                'numeric',
                'min:0.000001',
                'max:999999999',
                'regex:/^\d+(\.\d{1,8})?$/', // Up to 8 decimal places
            ],
            
            'last_updated_rate' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
            
            'rate_source' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(['manual', 'xe', 'fixer', 'openexchangerates', 'currencylayer', 'bank_api']),
            ],

            // Geographic and usage information
            'country_code' => [
                'nullable',
                'string',
                'size:2',
                'exists:countries,iso_code_2',
            ],
            
            'countries' => [
                'nullable',
                'array',
                'max:50',
            ],
            'countries.*' => [
                'string',
                'size:2',
                'exists:countries,iso_code_2',
                'distinct',
            ],
            
            'usage_type' => [
                'required',
                'string',
                Rule::in(['official', 'unofficial', 'historical', 'cryptocurrency', 'commodity']),
            ],

            // Currency classification
            'currency_type' => [
                'required',
                'string',
                Rule::in(['fiat', 'cryptocurrency', 'commodity', 'points', 'voucher']),
            ],
            
            'is_major_currency' => [
                'nullable',
                'boolean',
            ],
            
            'is_crypto' => [
                'nullable',
                'boolean',
            ],
            
            'is_stable_coin' => [
                'nullable',
                'boolean',
            ],

            // Display and ordering
            'display_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            
            'is_featured' => [
                'nullable',
                'boolean',
            ],
            
            'color_code' => [
                'nullable',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', // Valid hex color
            ],

            // Status and availability
            'is_active' => [
                'nullable',
                'boolean',
            ],
            
            'is_visible' => [
                'nullable',
                'boolean',
            ],
            
            'is_tradeable' => [
                'nullable',
                'boolean',
            ],
            
            'is_deprecated' => [
                'nullable',
                'boolean',
            ],

            // Additional metadata
            'central_bank' => [
                'nullable',
                'string',
                'max:200',
            ],
            
            'subunit_name' => [
                'nullable',
                'string',
                'max:50',
            ],
            
            'subunit_ratio' => [
                'nullable',
                'integer',
                'min:1',
                'max:10000',
            ],

            // External integration
            'external_id' => [
                'nullable',
                'string',
                'max:100',
                'unique:salary_currencies,external_id',
            ],
            
            'api_identifiers' => [
                'nullable',
                'array',
                'max:10',
            ],
            'api_identifiers.*' => [
                'string',
                'max:50',
                'distinct',
            ],

            // Historical and validation data
            'introduced_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            
            'discontinued_date' => [
                'nullable',
                'date',
                'after:introduced_date',
            ],
            
            'inflation_rate' => [
                'nullable',
                'numeric',
                'between:-100,1000',
            ],

            // Additional data
            'metadata' => [
                'nullable',
                'array',
            ],
            
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Multilingual currency error messages.
     */
    public function messages(): array
    {
        return [
            // Basic information
            'currency_name.required' => __('currency.validation.name_required'),
            'currency_name.unique' => __('currency.validation.name_unique'),
            'currency_name.regex' => __('currency.validation.name_format'),
            
            // ISO codes
            'iso_code.required' => __('currency.validation.iso_code_required'),
            'iso_code.size' => __('currency.validation.iso_code_size'),
            'iso_code.unique' => __('currency.validation.iso_code_unique'),
            'iso_code.regex' => __('currency.validation.iso_code_format'),
            'numeric_code.size' => __('currency.validation.numeric_code_size'),
            'numeric_code.unique' => __('currency.validation.numeric_code_unique'),
            'numeric_code.regex' => __('currency.validation.numeric_code_format'),
            
            // Symbol and display
            'currency_symbol.required' => __('currency.validation.symbol_required'),
            'symbol_position.in' => __('currency.validation.symbol_position_invalid'),
            'decimal_separator.required' => __('currency.validation.decimal_separator_required'),
            'decimal_separator.in' => __('currency.validation.decimal_separator_invalid'),
            'thousands_separator.in' => __('currency.validation.thousands_separator_invalid'),
            'decimal_places.min' => __('currency.validation.decimal_places_min'),
            'decimal_places.max' => __('currency.validation.decimal_places_max'),
            
            // Exchange rate
            'exchange_rate_to_usd.required' => __('currency.validation.exchange_rate_required'),
            'exchange_rate_to_usd.numeric' => __('currency.validation.exchange_rate_numeric'),
            'exchange_rate_to_usd.min' => __('currency.validation.exchange_rate_min'),
            'exchange_rate_to_usd.regex' => __('currency.validation.exchange_rate_format'),
            'last_updated_rate.before_or_equal' => __('currency.validation.rate_date_future'),
            'rate_source.in' => __('currency.validation.rate_source_invalid'),
            
            // Geographic
            'country_code.exists' => __('currency.validation.country_invalid'),
            'countries.*.exists' => __('currency.validation.country_not_found'),
            'countries.*.distinct' => __('currency.validation.countries_unique'),
            'usage_type.in' => __('currency.validation.usage_type_invalid'),
            
            // Classification
            'currency_type.in' => __('currency.validation.currency_type_invalid'),
            'color_code.regex' => __('currency.validation.color_format'),
            
            // Historical data
            'introduced_date.before_or_equal' => __('currency.validation.introduced_date_future'),
            'discontinued_date.after' => __('currency.validation.discontinued_before_introduced'),
            'inflation_rate.between' => __('currency.validation.inflation_rate_range'),
            
            // External
            'external_id.unique' => __('currency.validation.external_id_unique'),
            'api_identifiers.*.distinct' => __('currency.validation.api_identifiers_unique'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'currency_name' => __('currency.fields.currency_name'),
            'iso_code' => __('currency.fields.iso_code'),
            'numeric_code' => __('currency.fields.numeric_code'),
            'currency_symbol' => __('currency.fields.currency_symbol'),
            'symbol_position' => __('currency.fields.symbol_position'),
            'decimal_separator' => __('currency.fields.decimal_separator'),
            'thousands_separator' => __('currency.fields.thousands_separator'),
            'decimal_places' => __('currency.fields.decimal_places'),
            'exchange_rate_to_usd' => __('currency.fields.exchange_rate'),
            'last_updated_rate' => __('currency.fields.last_updated'),
            'rate_source' => __('currency.fields.rate_source'),
            'country_code' => __('currency.fields.primary_country'),
            'countries' => __('currency.fields.countries'),
            'usage_type' => __('currency.fields.usage_type'),
            'currency_type' => __('currency.fields.currency_type'),
            'is_major_currency' => __('currency.fields.is_major'),
            'display_order' => __('currency.fields.display_order'),
            'central_bank' => __('currency.fields.central_bank'),
            'subunit_name' => __('currency.fields.subunit_name'),
            'subunit_ratio' => __('currency.fields.subunit_ratio'),
            'external_id' => __('currency.fields.external_id'),
            'api_identifiers' => __('currency.fields.api_identifiers'),
            'introduced_date' => __('currency.fields.introduced_date'),
            'discontinued_date' => __('currency.fields.discontinued_date'),
            'inflation_rate' => __('currency.fields.inflation_rate'),
        ];
    }

    /**
     * Configure the validator instance.
     * Enhanced currency validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check for currency code conflicts
            if ($this->hasCurrencyCodeConflicts()) {
                $validator->errors()->add('iso_code', __('currency.validation.code_conflicts'));
            }
            
            // Validate exchange rate reasonableness
            if ($this->hasUnreasonableExchangeRate()) {
                $validator->errors()->add('exchange_rate_to_usd', __('currency.validation.unreasonable_rate'));
            }
            
            // Check cryptocurrency specific rules
            if ($this->violatesCryptocurrencyRules()) {
                $validator->errors()->add('currency_type', __('currency.validation.crypto_rules_violation'));
            }
            
            // Validate historical date consistency
            if ($this->hasInvalidHistoricalDates()) {
                $validator->errors()->add('discontinued_date', __('currency.validation.invalid_historical_dates'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Currency data normalization.
     */
    protected function prepareForValidation(): void
    {
        // Normalize ISO code to uppercase
        if ($this->filled('iso_code')) {
            $this->merge(['iso_code' => strtoupper($this->iso_code)]);
        }
        
        // Normalize country code to uppercase
        if ($this->filled('country_code')) {
            $this->merge(['country_code' => strtoupper($this->country_code)]);
        }
        
        // Normalize text fields
        $this->merge([
            'currency_name' => trim($this->currency_name ?? ''),
            'central_bank' => trim($this->central_bank ?? '') ?: null,
            'subunit_name' => trim($this->subunit_name ?? '') ?: null,
            'notes' => trim($this->notes ?? '') ?: null,
        ]);
        
        // Set defaults
        $this->merge([
            'is_active' => filter_var($this->is_active ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_visible' => filter_var($this->is_visible ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_tradeable' => filter_var($this->is_tradeable ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_major_currency' => filter_var($this->is_major_currency ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_crypto' => filter_var($this->is_crypto ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_stable_coin' => filter_var($this->is_stable_coin ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_featured' => filter_var($this->is_featured ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_deprecated' => filter_var($this->is_deprecated ?? false, FILTER_VALIDATE_BOOLEAN),
            'display_order' => (int) ($this->display_order ?? 0),
            'decimal_places' => (int) ($this->decimal_places ?? 2),
            'subunit_ratio' => (int) ($this->subunit_ratio ?? 100),
        ]);
        
        // Clean arrays
        if ($this->filled('countries')) {
            $this->merge([
                'countries' => array_filter(array_unique(array_map('strtoupper', (array) $this->countries))),
            ]);
        }
        
        if ($this->filled('api_identifiers')) {
            $this->merge([
                'api_identifiers' => array_filter(array_unique((array) $this->api_identifiers)),
            ]);
        }
        
        // Auto-detect cryptocurrency
        if ($this->currency_type === 'cryptocurrency') {
            $this->merge(['is_crypto' => true]);
        }
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced currency logging.
     */
    protected function failedValidation(Validator $validator): void
    {
        \Log::warning('Currency creation validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input_data' => $this->safe()->toArray(),
            'user_id' => Auth::id(),
            'user_role' => Auth::user()?->getRoleNames(),
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Get processed data for currency creation.
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();
        
        // Add creator information
        $data['created_by'] = Auth::id();
        
        // Set timestamps
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $data['last_updated_rate'] = $data['last_updated_rate'] ?? now();
        
        // Process arrays as JSON
        if (isset($data['countries'])) {
            $data['countries'] = json_encode($data['countries']);
        }
        
        if (isset($data['api_identifiers'])) {
            $data['api_identifiers'] = json_encode($data['api_identifiers']);
        }
        
        if (isset($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
        }
        
        return $data;
    }

    /**
     * Check for currency code conflicts.
     */
    private function hasCurrencyCodeConflicts(): bool
    {
        $isoCode = $this->iso_code;
        $numericCode = $this->numeric_code;
        
        if (!$isoCode) {
            return false;
        }
        
        // Check if ISO code conflicts with existing major currencies
        $conflictingCurrencies = [
            'USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF', 'CNY', 'SEK', 'NZD'
        ];
        
        return in_array($isoCode, $conflictingCurrencies) && 
               SalaryCurrency::where('iso_code', $isoCode)->exists();
    }

    /**
     * Validate exchange rate reasonableness.
     */
    private function hasUnreasonableExchangeRate(): bool
    {
        $rate = $this->exchange_rate_to_usd;
        
        if (!$rate) {
            return false;
        }
        
        // Check for extremely high or low rates that might be errors
        if ($rate > 1000000 || $rate < 0.000001) {
            return true;
        }
        
        // For major currencies, rates should be within reasonable ranges
        $isoCode = $this->iso_code;
        $majorCurrencyRanges = [
            'EUR' => ['min' => 0.8, 'max' => 1.3],
            'GBP' => ['min' => 1.1, 'max' => 1.6],
            'JPY' => ['min' => 100, 'max' => 200],
            'AUD' => ['min' => 0.6, 'max' => 0.9],
            'CAD' => ['min' => 0.7, 'max' => 0.9],
        ];
        
        if (isset($majorCurrencyRanges[$isoCode])) {
            $range = $majorCurrencyRanges[$isoCode];
            return $rate < $range['min'] || $rate > $range['max'];
        }
        
        return false;
    }

    /**
     * Check cryptocurrency specific validation rules.
     */
    private function violatesCryptocurrencyRules(): bool
    {
        if ($this->currency_type !== 'cryptocurrency') {
            return false;
        }
        
        // Cryptocurrencies should not have country codes
        if ($this->filled('country_code')) {
            return true;
        }
        
        // Stable coins should have stable exchange rates
        if ($this->is_stable_coin && $this->exchange_rate_to_usd) {
            $rate = $this->exchange_rate_to_usd;
            // Stable coins should be close to 1 USD
            if ($rate < 0.95 || $rate > 1.05) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Validate historical date consistency.
     */
    private function hasInvalidHistoricalDates(): bool
    {
        $introduced = $this->introduced_date;
        $discontinued = $this->discontinued_date;
        
        if (!$introduced || !$discontinued) {
            return false;
        }
        
        // Discontinued date should be after introduced date
        return \Carbon\Carbon::parse($discontinued)->lte(\Carbon\Carbon::parse($introduced));
    }
}
