<?php

namespace App\Http\Requests\Location;

use App\Models\Country;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Comprehensive Form Request for storing Country data
 * Implements Laravel 12 best practices with geographic data validation.
 */
class StoreCountryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only admin users can create/modify country data.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Only admin or users with location management permissions
        return $user->hasRole('Admin') ||
               $user->hasRole('Location Manager') ||
               $user->can('manage-locations');
    }

    /**
     * Get the validation rules that apply to the request.
     * Comprehensive geographic data validation.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Basic country information
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'unique:countries,name',
                'regex:/^[a-zA-Z\s\-\.\(\)\']+$/', // Only letters, spaces, and basic punctuation
            ],

            'official_name' => [
                'nullable',
                'string',
                'min:2',
                'max:150',
                'regex:/^[a-zA-Z\s\-\.\(\)\']+$/',
            ],

            'native_name' => [
                'nullable',
                'string',
                'min:1',
                'max:150',
            ],

            // ISO codes
            'iso_code_2' => [
                'required',
                'string',
                'size:2',
                'uppercase',
                'unique:countries,iso_code_2',
                'regex:/^[A-Z]{2}$/',
            ],

            'iso_code_3' => [
                'required',
                'string',
                'size:3',
                'uppercase',
                'unique:countries,iso_code_3',
                'regex:/^[A-Z]{3}$/',
            ],

            'iso_numeric' => [
                'nullable',
                'string',
                'size:3',
                'unique:countries,iso_numeric',
                'regex:/^[0-9]{3}$/',
            ],

            // Geographic information
            'continent' => [
                'required',
                'string',
                Rule::in(['Africa', 'Antarctica', 'Asia', 'Europe', 'North America', 'South America', 'Oceania']),
            ],

            'region' => [
                'nullable',
                'string',
                'max:100',
            ],

            'subregion' => [
                'nullable',
                'string',
                'max:100',
            ],

            // Currency and locale
            'currency_code' => [
                'nullable',
                'string',
                'size:3',
                'uppercase',
                'exists:salary_currencies,iso_code',
                'regex:/^[A-Z]{3}$/',
            ],

            'currency_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'currency_symbol' => [
                'nullable',
                'string',
                'max:10',
            ],

            // Language and formatting
            'primary_language' => [
                'nullable',
                'string',
                'size:2',
                'exists:languages,iso_code',
                'regex:/^[a-z]{2}$/',
            ],

            'languages' => [
                'nullable',
                'array',
                'max:20',
            ],
            'languages.*' => [
                'string',
                'size:2',
                'exists:languages,iso_code',
                'distinct',
            ],

            // Coordinates and area
            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'area_km2' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999',
            ],

            // Phone and internet
            'phone_prefix' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^\+?[0-9]+$/',
            ],

            'internet_tld' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^\.[a-z]{2,}$/',
            ],

            // Display and sorting
            'flag_emoji' => [
                'nullable',
                'string',
                'max:10',
            ],

            'flag_image_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            'display_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
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

            'is_supported' => [
                'nullable',
                'boolean',
            ],

            // Additional data
            'timezone_default' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Za-z_\/\+\-0-9]+$/',
            ],

            'timezones' => [
                'nullable',
                'array',
                'max:50',
            ],
            'timezones.*' => [
                'string',
                'max:50',
                'distinct',
            ],

            // Border countries
            'borders' => [
                'nullable',
                'array',
                'max:20',
            ],
            'borders.*' => [
                'string',
                'size:2',
                'exists:countries,iso_code_2',
                'distinct',
            ],

            // Economic data
            'gdp_per_capita' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999',
            ],

            'population' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999999999',
            ],

            // Metadata
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
     * Multilingual geographic error messages.
     */
    public function messages(): array
    {
        return [
            // Basic information
            'name.required' => __('location.validation.country_name_required'),
            'name.unique' => __('location.validation.country_name_unique'),
            'name.regex' => __('location.validation.country_name_format'),

            // ISO codes
            'iso_code_2.required' => __('location.validation.iso2_required'),
            'iso_code_2.size' => __('location.validation.iso2_size'),
            'iso_code_2.unique' => __('location.validation.iso2_unique'),
            'iso_code_2.regex' => __('location.validation.iso2_format'),
            'iso_code_3.required' => __('location.validation.iso3_required'),
            'iso_code_3.size' => __('location.validation.iso3_size'),
            'iso_code_3.unique' => __('location.validation.iso3_unique'),
            'iso_code_3.regex' => __('location.validation.iso3_format'),
            'iso_numeric.regex' => __('location.validation.iso_numeric_format'),

            // Geographic
            'continent.required' => __('location.validation.continent_required'),
            'continent.in' => __('location.validation.continent_invalid'),

            // Currency
            'currency_code.exists' => __('location.validation.currency_invalid'),
            'currency_code.regex' => __('location.validation.currency_format'),

            // Language
            'primary_language.exists' => __('location.validation.language_invalid'),
            'languages.*.exists' => __('location.validation.language_not_found'),
            'languages.*.distinct' => __('location.validation.languages_unique'),

            // Coordinates
            'latitude.between' => __('location.validation.latitude_range'),
            'longitude.between' => __('location.validation.longitude_range'),

            // Phone and internet
            'phone_prefix.regex' => __('location.validation.phone_prefix_format'),
            'internet_tld.regex' => __('location.validation.tld_format'),

            // Borders
            'borders.*.exists' => __('location.validation.border_country_invalid'),
            'borders.*.distinct' => __('location.validation.borders_unique'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'name' => __('location.fields.country_name'),
            'official_name' => __('location.fields.official_name'),
            'native_name' => __('location.fields.native_name'),
            'iso_code_2' => __('location.fields.iso_code_2'),
            'iso_code_3' => __('location.fields.iso_code_3'),
            'iso_numeric' => __('location.fields.iso_numeric'),
            'continent' => __('location.fields.continent'),
            'region' => __('location.fields.region'),
            'subregion' => __('location.fields.subregion'),
            'currency_code' => __('location.fields.currency'),
            'currency_name' => __('location.fields.currency_name'),
            'currency_symbol' => __('location.fields.currency_symbol'),
            'primary_language' => __('location.fields.primary_language'),
            'languages' => __('location.fields.languages'),
            'latitude' => __('location.fields.latitude'),
            'longitude' => __('location.fields.longitude'),
            'area_km2' => __('location.fields.area'),
            'phone_prefix' => __('location.fields.phone_prefix'),
            'internet_tld' => __('location.fields.internet_tld'),
            'timezone_default' => __('location.fields.default_timezone'),
            'timezones' => __('location.fields.timezones'),
            'borders' => __('location.fields.border_countries'),
            'population' => __('location.fields.population'),
            'gdp_per_capita' => __('location.fields.gdp_per_capita'),
            'display_order' => __('location.fields.display_order'),
        ];
    }

    /**
     * Configure the validator instance.
     * Enhanced geographic validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check for geographic consistency
            if ($this->hasGeographicInconsistencies()) {
                $validator->errors()->add('continent', __('location.validation.geographic_inconsistency'));
            }

            // Validate currency and language compatibility
            if ($this->hasIncompatibleLocaleData()) {
                $validator->errors()->add('currency_code', __('location.validation.locale_incompatible'));
            }

            // Check for self-referencing borders
            if ($this->hasSelfReferencingBorders()) {
                $validator->errors()->add('borders', __('location.validation.self_referencing_border'));
            }

            // Validate coordinate consistency
            if ($this->hasInvalidCoordinates()) {
                $validator->errors()->add('latitude', __('location.validation.invalid_coordinates'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Geographic data normalization.
     */
    protected function prepareForValidation(): void
    {
        // Normalize text fields
        $this->merge([
            'name' => trim($this->name ?? ''),
            'official_name' => trim($this->official_name ?? '') ?: null,
            'native_name' => trim($this->native_name ?? '') ?: null,
        ]);

        // Normalize ISO codes to uppercase
        if ($this->filled('iso_code_2')) {
            $this->merge(['iso_code_2' => strtoupper($this->iso_code_2)]);
        }

        if ($this->filled('iso_code_3')) {
            $this->merge(['iso_code_3' => strtoupper($this->iso_code_3)]);
        }

        if ($this->filled('currency_code')) {
            $this->merge(['currency_code' => strtoupper($this->currency_code)]);
        }

        // Normalize language codes to lowercase
        if ($this->filled('primary_language')) {
            $this->merge(['primary_language' => strtolower($this->primary_language)]);
        }

        // Set defaults
        $this->merge([
            'is_active' => filter_var($this->is_active ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_visible' => filter_var($this->is_visible ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_supported' => filter_var($this->is_supported ?? true, FILTER_VALIDATE_BOOLEAN),
            'display_order' => (int) ($this->display_order ?? 0),
        ]);

        // Clean arrays
        if ($this->filled('languages')) {
            $this->merge([
                'languages' => array_filter(array_unique(array_map('strtolower', (array) $this->languages))),
            ]);
        }

        if ($this->filled('borders')) {
            $this->merge([
                'borders' => array_filter(array_unique(array_map('strtoupper', (array) $this->borders))),
            ]);
        }

        if ($this->filled('timezones')) {
            $this->merge([
                'timezones' => array_filter(array_unique((array) $this->timezones)),
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced geographic logging.
     */
    protected function failedValidation(Validator $validator): void
    {
        \Log::warning('Country creation validation failed', [
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
     * Get processed data for country creation.
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();

        // Add creator information
        $data['created_by'] = Auth::id();

        // Set timestamps
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Process arrays as JSON
        if (isset($data['languages'])) {
            $data['languages'] = json_encode($data['languages']);
        }

        if (isset($data['borders'])) {
            $data['borders'] = json_encode($data['borders']);
        }

        if (isset($data['timezones'])) {
            $data['timezones'] = json_encode($data['timezones']);
        }

        if (isset($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
        }

        return $data;
    }

    /**
     * Check for geographic inconsistencies.
     */
    private function hasGeographicInconsistencies(): bool
    {
        // Validate continent-region relationships
        $continent = $this->continent;
        $region = $this->region;

        if ($continent && $region) {
            $validRegions = [
                'Africa' => ['Northern Africa', 'Western Africa', 'Central Africa', 'Eastern Africa', 'Southern Africa'],
                'Asia' => ['Central Asia', 'Eastern Asia', 'South-eastern Asia', 'Southern Asia', 'Western Asia'],
                'Europe' => ['Eastern Europe', 'Northern Europe', 'Southern Europe', 'Western Europe'],
                'North America' => ['Northern America', 'Central America', 'Caribbean'],
                'South America' => ['South America'],
                'Oceania' => ['Australia and New Zealand', 'Melanesia', 'Micronesia', 'Polynesia'],
            ];

            if (isset($validRegions[$continent]) && ! in_array($region, $validRegions[$continent])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for incompatible locale data.
     */
    private function hasIncompatibleLocaleData(): bool
    {
        // Check if currency and country make sense together
        $countryCode = $this->iso_code_2;
        $currencyCode = $this->currency_code;

        if ($countryCode && $currencyCode) {
            // Example validation: EUR should only be used by EU countries
            if ($currencyCode === 'EUR' && ! in_array($countryCode, ['AT', 'BE', 'CY', 'EE', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PT', 'SK', 'SI', 'ES'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for self-referencing borders.
     */
    private function hasSelfReferencingBorders(): bool
    {
        $borders = $this->borders ?? [];
        $countryCode = $this->iso_code_2;

        return in_array($countryCode, $borders);
    }

    /**
     * Validate coordinate consistency.
     */
    private function hasInvalidCoordinates(): bool
    {
        $lat = $this->latitude;
        $lng = $this->longitude;

        // If one coordinate is provided, both should be provided
        if (($lat !== null && $lng === null) || ($lat === null && $lng !== null)) {
            return true;
        }

        // Validate specific impossible coordinates (e.g., ocean coordinates for landlocked countries)
        // This would require a more complex geographic database

        return false;
    }
}
