<?php

namespace App\Http\Requests\Location;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class GetStatesLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Public location data retrieval endpoint
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Country ID - required for fetching states
            'country_id' => [
                'required',
                'integer',
                'min:1',
                'exists:countries,id',
                function ($attribute, $value, $fail) {
                    if ($value && ! $this->validateCountryActive($value)) {
                        $fail(__('validation.country_not_active'));
                    }
                },
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

            // Search functionality
            'search' => [
                'sometimes',
                'string',
                'min:1',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-\.\']+$/',
            ],

            // Sorting options
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in(['name', 'code', 'created_at', 'id']),
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

            // Include additional data
            'include_cities' => [
                'sometimes',
                'boolean',
            ],

            'include_statistics' => [
                'sometimes',
                'boolean',
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
            'country_id.required' => __('validation.required_field', ['field' => __('validation.attributes.country_id')]),
            'country_id.integer' => __('validation.integer', ['attribute' => __('validation.attributes.country_id')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.country_id')]),

            'page.integer' => __('validation.integer', ['attribute' => __('validation.attributes.page')]),
            'page.min' => __('validation.min_value', ['attribute' => __('validation.attributes.page'), 'min' => 1]),

            'search.regex' => __('validation.search_format', ['attribute' => __('validation.attributes.search')]),
            'sort_by.in' => __('validation.in_list', ['attribute' => __('validation.attributes.sort_by')]),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'page' => $this->page ?? 1,
            'per_page' => $this->per_page ?? 20,
            'sort_by' => $this->sort_by ?? 'name',
            'sort_direction' => $this->sort_direction ?? 'asc',
            'status' => $this->status ?? 'active',
            'include_cities' => $this->boolean('include_cities', false),
            'include_statistics' => $this->boolean('include_statistics', false),
        ]);

        // Log request
        Log::info('States lookup request', [
            'country_id' => $this->country_id,
            'search' => $this->search ?? null,
            'user_id' => Auth::id(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Validate if country is active.
     */
    private function validateCountryActive($countryId): bool
    {
        return \DB::table('countries')
            ->where('id', $countryId)
            ->where('status', 'active')
            ->exists();
    }
}
