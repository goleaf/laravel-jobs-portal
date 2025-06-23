<?php

namespace App\Http\Requests\JobType;

use App\Models\JobType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateJobTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $jobType = $this->route('job_type') ?? $this->route('jobType');

        return $this->user()->can('update', $jobType);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        $jobType = $this->route('job_type') ?? $this->route('jobType');
        $jobTypeId = $jobType instanceof JobType ? $jobType->id : $jobType;

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('job_types', 'name')->ignore($jobTypeId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('job_types', 'slug')->ignore($jobTypeId),
                'regex:/^[a-z0-9\-]+$/',
            ],
            'is_default' => [
                'sometimes',
                'boolean',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'is_featured' => [
                'sometimes',
                'boolean',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'icon' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9\-_]+$/',
            ],
            'color' => [
                'nullable',
                'string',
                'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:60',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:160',
            ],
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
            'name.required' => __('validation.job_type.name.required'),
            'name.string' => __('validation.job_type.name.string'),
            'name.max' => __('validation.job_type.name.max'),
            'name.unique' => __('validation.job_type.name.unique'),
            'description.string' => __('validation.job_type.description.string'),
            'description.max' => __('validation.job_type.description.max'),
            'slug.string' => __('validation.job_type.slug.string'),
            'slug.max' => __('validation.job_type.slug.max'),
            'slug.unique' => __('validation.job_type.slug.unique'),
            'slug.regex' => __('validation.job_type.slug.regex'),
            'is_default.boolean' => __('validation.job_type.is_default.boolean'),
            'is_active.boolean' => __('validation.job_type.is_active.boolean'),
            'is_featured.boolean' => __('validation.job_type.is_featured.boolean'),
            'sort_order.integer' => __('validation.job_type.sort_order.integer'),
            'sort_order.min' => __('validation.job_type.sort_order.min'),
            'sort_order.max' => __('validation.job_type.sort_order.max'),
            'icon.string' => __('validation.job_type.icon.string'),
            'icon.max' => __('validation.job_type.icon.max'),
            'icon.regex' => __('validation.job_type.icon.regex'),
            'color.string' => __('validation.job_type.color.string'),
            'color.regex' => __('validation.job_type.color.regex'),
            'meta_title.string' => __('validation.job_type.meta_title.string'),
            'meta_title.max' => __('validation.job_type.meta_title.max'),
            'meta_description.string' => __('validation.job_type.meta_description.string'),
            'meta_description.max' => __('validation.job_type.meta_description.max'),
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
            'name' => __('job_type.fields.name'),
            'description' => __('job_type.fields.description'),
            'slug' => __('job_type.fields.slug'),
            'is_default' => __('job_type.fields.is_default'),
            'is_active' => __('job_type.fields.is_active'),
            'is_featured' => __('job_type.fields.is_featured'),
            'sort_order' => __('job_type.fields.sort_order'),
            'icon' => __('job_type.fields.icon'),
            'color' => __('job_type.fields.color'),
            'meta_title' => __('job_type.fields.meta_title'),
            'meta_description' => __('job_type.fields.meta_description'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug if name changed but slug not provided
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }

        // Ensure boolean fields are properly cast
        if ($this->has('is_default')) {
            $this->merge(['is_default' => $this->boolean('is_default')]);
        }
        if ($this->has('is_active')) {
            $this->merge(['is_active' => $this->boolean('is_active')]);
        }
        if ($this->has('is_featured')) {
            $this->merge(['is_featured' => $this->boolean('is_featured')]);
        }

        // Clean and format color value
        if ($this->has('color') && $this->color) {
            $this->merge([
                'color' => strtoupper($this->color),
            ]);
        }

        // Clean icon name
        if ($this->has('icon') && $this->icon) {
            $this->merge([
                'icon' => strtolower(trim($this->icon)),
            ]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $jobType = $this->route('job_type') ?? $this->route('jobType');

        // Log significant changes
        if ($this->has('is_active') && $jobType->is_active !== $this->is_active) {
            \Log::info('Job type status changed', [
                'job_type_id' => $jobType->id,
                'old_status' => $jobType->is_active ? 'active' : 'inactive',
                'new_status' => $this->is_active ? 'active' : 'inactive',
                'user_id' => $this->user()->id,
            ]);
        }

        if ($this->has('is_default') && $this->is_default && !$jobType->is_default) {
            \Log::info('Job type set as default', [
                'job_type_id' => $jobType->id,
                'name' => $this->name ?? $jobType->name,
                'user_id' => $this->user()->id,
            ]);
        }
    }
}
