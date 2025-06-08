<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequiredDegreeLevelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', \App\Models\RequiredDegreeLevel::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'active' => ['sometimes', 'boolean'],
            'default' => ['sometimes', 'boolean'],
            'with_jobs' => ['sometimes', 'boolean'],
            'sort_by' => ['sometimes', 'string', 'in:name,created_at,updated_at,alphabetical,popular'],
            'sort_direction' => ['sometimes', 'string', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'include' => ['sometimes', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'search.string' => __('validation.string', ['attribute' => __('required_degree_levels.attributes.search')]),
            'search.max' => __('validation.max.string', ['attribute' => __('required_degree_levels.attributes.search'), 'max' => 255]),
            'active.boolean' => __('validation.boolean', ['attribute' => __('required_degree_levels.attributes.active')]),
            'default.boolean' => __('validation.boolean', ['attribute' => __('required_degree_levels.attributes.default')]),
            'with_jobs.boolean' => __('validation.boolean', ['attribute' => __('required_degree_levels.attributes.with_jobs')]),
            'sort_by.string' => __('validation.string', ['attribute' => __('required_degree_levels.attributes.sort_by')]),
            'sort_by.in' => __('validation.in', ['attribute' => __('required_degree_levels.attributes.sort_by')]),
            'sort_direction.string' => __('validation.string', ['attribute' => __('required_degree_levels.attributes.sort_direction')]),
            'sort_direction.in' => __('validation.in', ['attribute' => __('required_degree_levels.attributes.sort_direction')]),
            'per_page.integer' => __('validation.integer', ['attribute' => __('required_degree_levels.attributes.per_page')]),
            'per_page.min' => __('validation.min.numeric', ['attribute' => __('required_degree_levels.attributes.per_page'), 'min' => 1]),
            'per_page.max' => __('validation.max.numeric', ['attribute' => __('required_degree_levels.attributes.per_page'), 'max' => 100]),
            'page.integer' => __('validation.integer', ['attribute' => __('required_degree_levels.attributes.page')]),
            'page.min' => __('validation.min.numeric', ['attribute' => __('required_degree_levels.attributes.page'), 'min' => 1]),
            'include.string' => __('validation.string', ['attribute' => __('required_degree_levels.attributes.include')]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'search' => __('required_degree_levels.attributes.search'),
            'active' => __('required_degree_levels.attributes.active'),
            'default' => __('required_degree_levels.attributes.default'),
            'with_jobs' => __('required_degree_levels.attributes.with_jobs'),
            'sort_by' => __('required_degree_levels.attributes.sort_by'),
            'sort_direction' => __('required_degree_levels.attributes.sort_direction'),
            'per_page' => __('required_degree_levels.attributes.per_page'),
            'page' => __('required_degree_levels.attributes.page'),
            'include' => __('required_degree_levels.attributes.include'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string booleans to actual booleans
        if ($this->has('active')) {
            $this->merge([
                'active' => filter_var($this->input('active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }

        if ($this->has('default')) {
            $this->merge([
                'default' => filter_var($this->input('default'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }

        if ($this->has('with_jobs')) {
            $this->merge([
                'with_jobs' => filter_var($this->input('with_jobs'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }

        // Set default values
        $this->merge([
            'sort_by' => $this->input('sort_by', 'name'),
            'sort_direction' => $this->input('sort_direction', 'asc'),
            'per_page' => $this->input('per_page', 15),
            'page' => $this->input('page', 1),
        ]);
    }
} 