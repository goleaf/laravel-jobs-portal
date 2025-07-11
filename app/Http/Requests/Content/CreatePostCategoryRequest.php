<?php

declare(strict_types=1);

namespace App\\Http\\Requests\\Content;

use Illuminate\\Foundation\\Http\\FormRequest;
use Illuminate\\Validation\\Rule;

/**
 * Class CreatePostCategoryRequest
 * Enterprise-grade validation for creating a new Post Category
 */
class CreatePostCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system, adjust if authorization is needed
    }

    public function rules(): array
    {
        return [
            \'name\' => [
                \'required\',
                \'string\',
                \'max:255\',\
                Rule::unique(\'post_categories\', \'name\'),
            ],
            \'description\' => [
                \'nullable\',\
                \'string\',\
                \'max:1000\',\
            ],
            \'is_active\' => [
                \'sometimes\',\
                \'boolean\',\
            ],
        ];
    }

    public function messages(): array
    {
        return [
            \'name.required\' => __(\'validation.custom.post_category.name_required\'),
            \'name.unique\' => __(\'validation.custom.post_category.name_unique\'),
            \'name.max\' => __(\'validation.custom.post_category.name_max\'),
            \'description.max\' => __(\'validation.custom.post_category.description_max\'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach ([\'name\', \'description\'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
    }
} 