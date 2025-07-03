<?php

namespace App\Http\Requests;

use App\Models\Taxonomy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * TaxonomyCreateRequest - Enhanced with Enhanced patterns.
 *
 * Validates taxonomy creation with comprehensive rules,
 * multilingual error messages, and custom validation.
 */
class TaxonomyCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create', Taxonomy::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('taxonomies', 'name'),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9_-]+$/',
                Rule::unique('taxonomies', 'slug'),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'type' => [
                'required',
                'string',
                'max:100',
                Rule::in(array_keys(Taxonomy::TYPES)),
            ],
            'is_hierarchical' => [
                'boolean',
            ],
            'is_active' => [
                'boolean',
            ],
            'is_public' => [
                'boolean',
            ],
            'meta' => [
                'nullable',
                'array',
            ],
            'meta.*' => [
                'nullable',
                'string',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('taxonomy.validation.name_required'),
            'name.string' => __('taxonomy.validation.name_string'),
            'name.max' => __('taxonomy.validation.name_max'),
            'name.min' => __('taxonomy.validation.name_min'),
            'name.unique' => __('taxonomy.validation.name_unique'),

            'slug.string' => __('taxonomy.validation.slug_string'),
            'slug.max' => __('taxonomy.validation.slug_max'),
            'slug.regex' => __('taxonomy.validation.slug_format'),
            'slug.unique' => __('taxonomy.validation.slug_unique'),

            'description.string' => __('taxonomy.validation.description_string'),
            'description.max' => __('taxonomy.validation.description_max'),

            'type.required' => __('taxonomy.validation.type_required'),
            'type.string' => __('taxonomy.validation.type_string'),
            'type.max' => __('taxonomy.validation.type_max'),
            'type.in' => __('taxonomy.validation.type_invalid'),

            'is_hierarchical.boolean' => __('taxonomy.validation.hierarchical_boolean'),
            'is_active.boolean' => __('taxonomy.validation.active_boolean'),
            'is_public.boolean' => __('taxonomy.validation.public_boolean'),

            'meta.array' => __('taxonomy.validation.meta_array'),
            'meta.*.string' => __('taxonomy.validation.meta_string'),

            'sort_order.integer' => __('taxonomy.validation.sort_order_integer'),
            'sort_order.min' => __('taxonomy.validation.sort_order_min'),
            'sort_order.max' => __('taxonomy.validation.sort_order_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('taxonomy.fields.name'),
            'slug' => __('taxonomy.fields.slug'),
            'description' => __('taxonomy.fields.description'),
            'type' => __('taxonomy.fields.type'),
            'is_hierarchical' => __('taxonomy.fields.is_hierarchical'),
            'is_active' => __('taxonomy.fields.is_active'),
            'is_public' => __('taxonomy.fields.is_public'),
            'meta' => __('taxonomy.fields.meta'),
            'sort_order' => __('taxonomy.fields.sort_order'),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  mixed  $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation for taxonomy type and hierarchical flag
            if ($this->type === 'tag' && $this->is_hierarchical) {
                $validator->errors()->add(
                    'is_hierarchical',
                    __('taxonomy.validation.tags_cannot_be_hierarchical')
                );
            }

            // Validate meta data structure based on type
            if ($this->meta && is_array($this->meta)) {
                $this->validateMetaByType($validator);
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug if not provided
        if (empty($this->slug) && ! empty($this->name)) {
            $this->merge([
                'slug' => Str::slug($this->name, '_'),
            ]);
        }

        // Set default values
        $this->merge([
            'is_hierarchical' => $this->boolean('is_hierarchical', false),
            'is_active' => $this->boolean('is_active', true),
            'is_public' => $this->boolean('is_public', true),
            'sort_order' => $this->integer('sort_order', 0),
        ]);
    }

    /**
     * Validate meta data based on taxonomy type.
     *
     * @param  mixed  $validator
     */
    private function validateMetaByType($validator): void
    {
        switch ($this->type) {
            case 'skill':
                $this->validateSkillMeta($validator);

                break;

            case 'job_category':
                $this->validateJobCategoryMeta($validator);

                break;

            case 'industry':
                $this->validateIndustryMeta($validator);

                break;
        }
    }

    /**
     * Validate skill-specific meta data.
     *
     * @param  mixed  $validator
     */
    private function validateSkillMeta($validator): void
    {
        $allowedFields = ['category', 'level', 'certification_available'];
        $this->validateMetaFields($validator, $allowedFields, 'skill');
    }

    /**
     * Validate job category-specific meta data.
     *
     * @param  mixed  $validator
     */
    private function validateJobCategoryMeta($validator): void
    {
        $allowedFields = ['icon', 'color', 'featured', 'parent_category'];
        $this->validateMetaFields($validator, $allowedFields, 'job_category');
    }

    /**
     * Validate industry-specific meta data.
     *
     * @param  mixed  $validator
     */
    private function validateIndustryMeta($validator): void
    {
        $allowedFields = ['growth_rate', 'size', 'economic_sector'];
        $this->validateMetaFields($validator, $allowedFields, 'industry');
    }

    /**
     * Validate meta fields for a specific type.
     *
     * @param  mixed  $validator
     */
    private function validateMetaFields($validator, array $allowedFields, string $type): void
    {
        $invalidFields = array_diff(array_keys($this->meta), $allowedFields);

        if (! empty($invalidFields)) {
            $validator->errors()->add(
                'meta',
                __('taxonomy.validation.invalid_meta_fields', [
                    'type' => $type,
                    'fields' => implode(', ', $invalidFields),
                    'allowed' => implode(', ', $allowedFields),
                ])
            );
        }
    }
}
