<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class GetCitiesCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // This is a data retrieval endpoint that should be accessible
        // to authenticated users with appropriate permissions
        return true; // Public API endpoint for location data
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // State ID - required for fetching cities
            'state_id' => [
                'required',
                'integer',
                'min:1',
                'exists:states,id',
                function ($attribute, $value, $fail) {
                    // Additional business logic validation
                    if ($value && ! $this->validateStateExists($value)) {
                        $fail(__('validation.state_not_found'));
                    }
                },
            ],

            // Optional country ID for additional validation
            'country_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:countries,id',
            ],

            // Pagination parameters
            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],

            // Filtering options
            'search' => [
                'sometimes',
                'string',
                'min:1',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-\.\']+$/', // Allow basic search characters
            ],

            // Sorting options
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in(['name', 'created_at', 'id']),
            ],

            'sort_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Status filtering
            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'inactive', 'all']),
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'state_id.required' => __('validation.required_field', ['field' => __('validation.attributes.state_id')]),
            'state_id.integer' => __('validation.integer', ['attribute' => __('validation.attributes.state_id')]),
            'state_id.min' => __('validation.min_value', ['attribute' => __('validation.attributes.state_id'), 'min' => 1]),
            'state_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.state_id')]),

            'country_id.integer' => __('validation.integer', ['attribute' => __('validation.attributes.country_id')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.country_id')]),

            'page.integer' => __('validation.integer', ['attribute' => __('validation.attributes.page')]),
            'page.min' => __('validation.min_value', ['attribute' => __('validation.attributes.page'), 'min' => 1]),
            'page.max' => __('validation.max_value', ['attribute' => __('validation.attributes.page'), 'max' => 1000]),

            'per_page.integer' => __('validation.integer', ['attribute' => __('validation.attributes.per_page')]),
            'per_page.min' => __('validation.min_value', ['attribute' => __('validation.attributes.per_page'), 'min' => 1]),
            'per_page.max' => __('validation.max_value', ['attribute' => __('validation.attributes.per_page'), 'max' => 100]),

            'search.string' => __('validation.string', ['attribute' => __('validation.attributes.search')]),
            'search.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.search'), 'min' => 1]),
            'search.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.search'), 'max' => 100]),
            'search.regex' => __('validation.search_format', ['attribute' => __('validation.attributes.search')]),

            'sort_by.in' => __('validation.in_list', ['attribute' => __('validation.attributes.sort_by'), 'values' => 'name, created_at, id']),
            'sort_direction.in' => __('validation.in_list', ['attribute' => __('validation.attributes.sort_direction'), 'values' => 'asc, desc']),
            'status.in' => __('validation.in_list', ['attribute' => __('validation.attributes.status'), 'values' => 'active, inactive, all']),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'state_id' => __('validation.attributes.state_id'),
            'country_id' => __('validation.attributes.country_id'),
            'page' => __('validation.attributes.page'),
            'per_page' => __('validation.attributes.per_page'),
            'search' => __('validation.attributes.search'),
            'sort_by' => __('validation.attributes.sort_by'),
            'sort_direction' => __('validation.attributes.sort_direction'),
            'status' => __('validation.attributes.status'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values if not provided
        $this->merge([
            'page' => $this->page ?? 1,
            'per_page' => $this->per_page ?? 10,
            'sort_by' => $this->sort_by ?? 'name',
            'sort_direction' => $this->sort_direction ?? 'asc',
            'status' => $this->status ?? 'active',
        ]);

        // Clean and normalize search term
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->search),
            ]);
        }

        // Log the request for monitoring
        Log::info('Cities lookup request', [
            'state_id' => $this->state_id,
            'country_id' => $this->country_id ?? null,
            'search' => $this->search ?? null,
            'user_id' => Auth::id(),
            'ip' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Additional data processing after validation
        $this->merge([
            'validated_at' => now(),
        ]);
    }

    /**
     * Custom validation method to check if state exists and is active.
     *
     * @param  mixed  $stateId
     */
    private function validateStateExists($stateId): bool
    {
        return \DB::table('states')
            ->where('id', $stateId)
            ->where('status', 'active')
            ->exists();
    }
}
