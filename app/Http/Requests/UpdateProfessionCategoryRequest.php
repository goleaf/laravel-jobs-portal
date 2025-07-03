<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfessionCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('professionCategory')->id;

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:10',
                Rule::unique('profession_categories', 'code')->ignore($categoryId),
                'regex:/^[0-9]+$/', // Only numbers for codes
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:profession_categories,id',
                Rule::notIn([$categoryId]), // Cannot be its own parent
            ],
            'level' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
            'sort_order' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'metadata.icon' => [
                'nullable',
                'string',
                'max:100',
            ],
            'metadata.color' => [
                'nullable',
                'string',
                'regex:/^#[0-9a-f]{6}$/i', // Hex color validation
            ],

            // Translation validation
            'translations' => [
                'sometimes',
                'array',
                'min:1',
            ],
            'translations.*' => [
                'array',
                'required',
            ],
            'translations.*.name' => [
                'required',
                'string',
                'max:255',
            ],
            'translations.*.description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'This profession category code already exists.',
            'code.regex' => 'The profession category code must contain only numbers.',
            'parent_id.exists' => 'The selected parent category does not exist.',
            'parent_id.not_in' => 'A category cannot be its own parent.',
            'level.min' => 'The category level must be at least 1.',
            'level.max' => 'The category level cannot exceed 10.',
            'sort_order.min' => 'The sort order must be 0 or greater.',
            'metadata.color.regex' => 'The color must be a valid hex color (e.g., #FF5733).',

            'translations.min' => 'At least one translation is required.',
            'translations.*.name.required' => 'The category name is required for each language.',
            'translations.*.name.max' => 'The category name cannot exceed 255 characters.',
            'translations.*.description.max' => 'The category description cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'code' => 'category code',
            'parent_id' => 'parent category',
            'level' => 'category level',
            'sort_order' => 'sort order',
            'is_active' => 'active status',
            'metadata.icon' => 'icon',
            'metadata.color' => 'color',
            'translations.*.name' => 'category name',
            'translations.*.description' => 'category description',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $categoryId = $this->route('professionCategory')->id;

            // Validate that if parent_id is provided, level is parent's level + 1
            if ($this->parent_id) {
                $parent = \App\Models\ProfessionCategory::find($this->parent_id);
                if ($parent && $this->level !== ($parent->level + 1)) {
                    $validator->errors()->add(
                        'level',
                        'The level must be '.($parent->level + 1).' for this parent category.'
                    );
                }
            }

            // Validate that root categories (level 1) don't have parent_id
            if ($this->level == 1 && $this->parent_id) {
                $validator->errors()->add(
                    'parent_id',
                    'Root categories (level 1) cannot have a parent.'
                );
            }

            // Validate that non-root categories have parent_id
            if ($this->level > 1 && ! $this->parent_id) {
                $validator->errors()->add(
                    'parent_id',
                    'Categories with level > 1 must have a parent.'
                );
            }

            // Validate that category is not being made a child of its own descendant
            if ($this->parent_id) {
                $category = \App\Models\ProfessionCategory::find($categoryId);
                $proposedParent = \App\Models\ProfessionCategory::find($this->parent_id);

                if ($category && $proposedParent && $proposedParent->isDescendantOf($category)) {
                    $validator->errors()->add(
                        'parent_id',
                        'A category cannot be moved under one of its own descendants.'
                    );
                }
            }

            // Validate translation locales
            if ($this->translations) {
                $supportedLocales = ['en', 'lt', 'ru', 'pl', 'de', 'fr', 'es', 'zh', 'ar', 'pt', 'tr', 'it', 'ja', 'hi'];

                foreach ($this->translations as $locale => $translation) {
                    if (! in_array($locale, $supportedLocales)) {
                        $validator->errors()->add(
                            "translations.{$locale}",
                            "The locale '{$locale}' is not supported."
                        );
                    }
                }
            }

            // Validate that category can be deactivated (no active children or professions)
            if ($this->has('is_active') && ! $this->is_active) {
                $category = \App\Models\ProfessionCategory::find($categoryId);

                if ($category) {
                    $activeChildren = $category->children()->where('is_active', true)->count();
                    if ($activeChildren > 0) {
                        $validator->errors()->add(
                            'is_active',
                            'Cannot deactivate category that has active subcategories.'
                        );
                    }

                    $activeProfessions = $category->professions()->where('is_active', true)->count();
                    if ($activeProfessions > 0) {
                        $validator->errors()->add(
                            'is_active',
                            'Cannot deactivate category that has active professions.'
                        );
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up metadata if provided
        if ($this->has('metadata') && is_array($this->metadata)) {
            $metadata = $this->metadata;

            // Remove empty values
            $metadata = array_filter($metadata, function ($value) {
                return ! is_null($value) && $value !== '';
            });

            $this->merge(['metadata' => $metadata]);
        }
    }
}
