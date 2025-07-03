<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreBlogCommentRequest
 * Enterprise-grade validation for Front blog comment storage operations
 */
class StoreBlogCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'blog_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('posts', 'id')->where('is_active', 1),
            ],
            'comment' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
            'author_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s\-\'\.]+$/u',
            ],
            'author_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'author_website' => [
                'sometimes',
                'url',
                'max:255',
            ],
            'parent_id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('blog_comments', 'id'),
            ],
            'rating' => [
                'sometimes',
                'integer',
                'min:1',
                'max:5',
            ],
            'notify_replies' => [
                'sometimes',
                'boolean',
            ],
            'terms_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],
            'captcha' => [
                'sometimes',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'blog_id.required' => __('validation.custom.comment.blog_required'),
            'blog_id.exists' => __('validation.custom.comment.blog_not_found'),
            'comment.required' => __('validation.custom.comment.content_required'),
            'comment.min' => __('validation.custom.comment.content_too_short'),
            'comment.max' => __('validation.custom.comment.content_too_long'),
            'author_name.required' => __('validation.custom.comment.name_required'),
            'author_name.regex' => __('validation.custom.comment.name_format'),
            'author_email.required' => __('validation.custom.comment.email_required'),
            'author_email.email' => __('validation.custom.comment.email_format'),
            'parent_id.exists' => __('validation.custom.comment.parent_not_found'),
            'terms_accepted.required' => __('validation.custom.comment.terms_required'),
            'terms_accepted.accepted' => __('validation.custom.comment.terms_must_accept'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['comment', 'author_name'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }

        if ($this->has('author_email')) {
            $this->merge(['author_email' => strtolower(trim($this->author_email))]);
        }

        if (! $this->has('notify_replies')) {
            $this->merge(['notify_replies' => false]);
        }
    }
}
