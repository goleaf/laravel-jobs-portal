<?php

declare(strict_types=1);

namespace App\\Http\\Requests\\Content;

use Illuminate\\Foundation\\Http\\FormRequest;
use Illuminate\\Validation\\Rule;

/**
 * Class CreatePostRequest
 * Enterprise-grade validation for creating a new Blog Post
 */
class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system, adjust if authorization is needed
    }

    public function rules(): array
    {
        return [
            \'title\' => [
                \'required\',
                \'string\',
                \'max:255\',
                Rule::unique(\'posts\', \'title\'),
            ],
            \'short_description\' => [
                \'required\',
                \'string\',
                \'min:10\',
                \'max:500\',
            ],
            \'description\' => [
                \'required\',
                \'string\',
                \'min:50\',
            ],
            \'post_image\' => [
                \'nullable\',
                \'image\',
                \'mimes:jpeg,png,jpg\',
                \'max:2048\',
            ],
            \'is_active\' => [
                \'sometimes\',
                \'boolean\',
            ],
            \'blog_categories\' => [
                \'required\',
                \'array\',
                \'min:1\',
            ],
            \'blog_categories.*\' => [
                \'integer\',
                Rule::exists(\'post_categories\', \'id\'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            \'title.required\' => __(\'validation.custom.post.title_required\'),
            \'title.unique\' => __(\'validation.custom.post.title_unique\'),
            \'short_description.required\' => __(\'validation.custom.post.short_description_required\'),
            \'description.required\' => __(\'validation.custom.post.description_required\'),
            \'blog_categories.required\' => __(\'validation.custom.post.category_required\'),
            \'blog_categories.min\' => __(\'validation.custom.post.category_min\'),
            \'blog_categories.*.exists\' => __(\'validation.custom.post.category_invalid\'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach ([\'title\', \'short_description\', \'description\'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
    }
} 