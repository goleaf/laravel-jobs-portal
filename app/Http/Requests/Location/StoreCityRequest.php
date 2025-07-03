<?php

namespace App\Http\Requests\Location;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Universal Form Request for storing City
 * Implements Laravel 12 best practices with Universal MCP patterns.
 */
class StoreCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: Authorization check.
     */
    public function authorize(): bool
    {
        // Only admin and location managers can create cities
        return true; // Based on user requirements: no auth system
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
            // City name - required
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-\'\.]+$/u',
                Rule::unique('cities', 'name')->where(function ($query) {
                    return $query->where('state_id', $this->state_id)
                        ->where('country_id', $this->country_id)
                        ->whereNull('deleted_at');
                }),
            ],

            // State ID - required
            'state_id' => [
                'required',
                'integer',
                'min:1',
                'exists:states,id',
                function ($attribute, $value, $fail) {
                    if (! $this->validateStateActive($value)) {
                        $fail(__('validation.state_not_active'));
                    }
                },
            ],

            // Country ID - required and must match state's country
            'country_id' => [
                'required',
                'integer',
                'min:1',
                'exists:countries,id',
                function ($attribute, $value, $fail) {
                    if (! $this->validateCountryStateMatch($value, $this->state_id)) {
                        $fail(__('validation.country_state_mismatch'));
                    }
                },
            ],

            // City code/abbreviation
            'code' => [
                'sometimes',
                'string',
                'min:2',
                'max:10',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('cities', 'code')->whereNull('deleted_at'),
            ],

            // Geographic coordinates
            'latitude' => [
                'sometimes',
                'numeric',
                'between:-90,90',
                function ($attribute, $value, $fail) {
                    if ($value && $this->has('longitude') && ! $this->validateCoordinates($value, $this->longitude)) {
                        $fail(__('validation.invalid_coordinates'));
                    }
                },
            ],

            'longitude' => [
                'sometimes',
                'numeric',
                'between:-180,180',
                'required_with:latitude',
            ],

            // Elevation (meters above sea level)
            'elevation' => [
                'sometimes',
                'numeric',
                'between:-500,9000', // Dead Sea to Mount Everest range
            ],

            // Population
            'population' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50000000', // Reasonable upper limit
            ],

            // Area in square kilometers
            'area_km2' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100000', // Reasonable upper limit for cities
            ],

            // Time zone
            'timezone' => [
                'sometimes',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if ($value && ! $this->validateTimezone($value)) {
                        $fail(__('validation.invalid_timezone'));
                    }
                },
            ],

            // Postal/ZIP code pattern
            'postal_code_pattern' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-\s\#]+$/',
            ],

            // Phone area code
            'area_code' => [
                'sometimes',
                'string',
                'max:10',
                'regex:/^[\+]?[0-9\-\(\)\s]+$/',
            ],

            // Economic indicators
            'economic_level' => [
                'sometimes',
                'string',
                Rule::in(['low', 'lower_middle', 'upper_middle', 'high']),
            ],

            'cost_of_living_index' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1000',
            ],

            // Administrative details
            'municipality_type' => [
                'sometimes',
                'string',
                Rule::in(['city', 'town', 'village', 'municipality', 'district', 'borough']),
            ],

            'founded_year' => [
                'sometimes',
                'integer',
                'min:1',
                'max:'.date('Y'),
            ],

            // Status and settings
            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'is_capital' => [
                'sometimes',
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && $this->hasStateCapital()) {
                        $fail(__('validation.state_capital_exists'));
                    }
                },
            ],

            'is_major_city' => [
                'sometimes',
                'boolean',
            ],

            // Language and localization
            'local_language' => [
                'sometimes',
                'string',
                'size:2',
                'exists:languages,code',
            ],

            'currency_code' => [
                'sometimes',
                'string',
                'size:3',
                'exists:salary_currencies,currency_code',
            ],

            // Description and notes
            'description' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // Image/logo
            'image_url' => [
                'sometimes',
                'url',
                'max:255',
            ],

            // Display order for listing
            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999',
            ],

            // SEO and URL
            'slug' => [
                'sometimes',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('cities', 'slug')->whereNull('deleted_at'),
            ],

            // Weather station ID
            'weather_station_id' => [
                'sometimes',
                'string',
                'max:20',
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
            'name.required' => __('validation.required_field', ['field' => __('validation.attributes.city_name')]),
            'name.unique' => __('validation.city_name_unique'),
            'name.regex' => __('validation.city_name_format'),

            'state_id.required' => __('validation.required_field', ['field' => __('validation.attributes.state')]),
            'state_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.state')]),

            'country_id.required' => __('validation.required_field', ['field' => __('validation.attributes.country')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.country')]),

            'code.unique' => __('validation.unique_field', ['field' => __('validation.attributes.city_code')]),
            'code.regex' => __('validation.city_code_format'),

            'latitude.between' => __('validation.latitude_range'),
            'longitude.between' => __('validation.longitude_range'),
            'longitude.required_with' => __('validation.longitude_required_with_latitude'),

            'elevation.between' => __('validation.elevation_range'),

            'population.min' => __('validation.min_value', ['attribute' => __('validation.attributes.population'), 'min' => 0]),
            'population.max' => __('validation.population_too_large'),

            'area_km2.min' => __('validation.min_value', ['attribute' => __('validation.attributes.area'), 'min' => 0]),

            'postal_code_pattern.regex' => __('validation.postal_code_pattern_format'),
            'area_code.regex' => __('validation.area_code_format'),

            'economic_level.in' => __('validation.invalid_economic_level'),
            'municipality_type.in' => __('validation.invalid_municipality_type'),

            'founded_year.min' => __('validation.min_value', ['attribute' => __('validation.attributes.founded_year'), 'min' => 1]),
            'founded_year.max' => __('validation.founded_year_future'),

            'local_language.exists' => __('validation.exists', ['attribute' => __('validation.attributes.language')]),
            'currency_code.exists' => __('validation.exists', ['attribute' => __('validation.attributes.currency')]),

            'image_url.url' => __('validation.valid_url', ['attribute' => __('validation.attributes.image_url')]),

            'slug.unique' => __('validation.unique_field', ['field' => __('validation.attributes.slug')]),
            'slug.regex' => __('validation.slug_format'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.city_name'),
            'state_id' => __('validation.attributes.state'),
            'country_id' => __('validation.attributes.country'),
            'code' => __('validation.attributes.city_code'),
            'latitude' => __('validation.attributes.latitude'),
            'longitude' => __('validation.attributes.longitude'),
            'elevation' => __('validation.attributes.elevation'),
            'population' => __('validation.attributes.population'),
            'area_km2' => __('validation.attributes.area'),
            'timezone' => __('validation.attributes.timezone'),
            'postal_code_pattern' => __('validation.attributes.postal_code_pattern'),
            'area_code' => __('validation.attributes.area_code'),
            'economic_level' => __('validation.attributes.economic_level'),
            'cost_of_living_index' => __('validation.attributes.cost_of_living_index'),
            'municipality_type' => __('validation.attributes.municipality_type'),
            'founded_year' => __('validation.attributes.founded_year'),
            'is_active' => __('validation.attributes.is_active'),
            'is_capital' => __('validation.attributes.is_capital'),
            'is_major_city' => __('validation.attributes.is_major_city'),
            'local_language' => __('validation.attributes.local_language'),
            'currency_code' => __('validation.attributes.currency_code'),
            'description' => __('validation.attributes.description'),
            'image_url' => __('validation.attributes.image_url'),
            'sort_order' => __('validation.attributes.sort_order'),
            'slug' => __('validation.attributes.slug'),
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
            'is_capital' => $this->boolean('is_capital', false),
            'is_major_city' => $this->boolean('is_major_city', false),
            'municipality_type' => $this->municipality_type ?? 'city',
            'economic_level' => $this->economic_level ?? 'upper_middle',
            'sort_order' => $this->sort_order ?? 0,
        ]);

        // Normalize city name
        if ($this->has('name')) {
            $this->merge([
                'name' => trim(ucwords(strtolower($this->name))),
            ]);
        }

        // Generate slug if not provided
        if (! $this->has('slug') && $this->has('name')) {
            $this->merge([
                'slug' => \Str::slug($this->name),
            ]);
        }

        // Normalize code to uppercase
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim($this->code)),
            ]);
        }

        // Log city creation attempt
        Log::info('City creation attempt', [
            'name' => $this->name ?? null,
            'state_id' => $this->state_id ?? null,
            'country_id' => $this->country_id ?? null,
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
        logger()->info('Store validation failed for StoreCityRequest', [
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
     * Validate if state is active.
     */
    private function validateStateActive($stateId): bool
    {
        return \DB::table('states')
            ->where('id', $stateId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Validate country-state relationship.
     */
    private function validateCountryStateMatch($countryId, $stateId): bool
    {
        if (! $stateId) {
            return true;
        }

        return \DB::table('states')
            ->where('id', $stateId)
            ->where('country_id', $countryId)
            ->exists();
    }

    /**
     * Validate coordinates are reasonable.
     */
    private function validateCoordinates($latitude, $longitude): bool
    {
        // Basic validation - could be enhanced with more sophisticated checks
        return is_numeric($latitude) && is_numeric($longitude) &&
               $latitude >= -90 && $latitude <= 90 &&
               $longitude >= -180 && $longitude <= 180;
    }

    /**
     * Validate timezone.
     */
    private function validateTimezone($timezone): bool
    {
        return in_array($timezone, timezone_identifiers_list());
    }

    /**
     * Check if state already has a capital city.
     */
    private function hasStateCapital(): bool
    {
        if (! $this->has('state_id')) {
            return false;
        }

        return \DB::table('cities')
            ->where('state_id', $this->state_id)
            ->where('is_capital', true)
            ->whereNull('deleted_at')
            ->exists();
    }
}
