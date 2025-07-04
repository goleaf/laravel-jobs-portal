<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class EmailJobToFriendRequest
 * Enterprise-grade validation for emailing jobs to friends
 */
class EmailJobToFriendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public access for sharing
    }

    public function rules(): array
    {
        return [
            'job_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('jobs', 'id')->where('status', 'open'),
            ],
            'sender_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s\-\'\.]+$/u',
            ],
            'sender_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'friend_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s\-\'\.]+$/u',
            ],
            'friend_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'different:sender_email',
            ],
            'message' => [
                'sometimes',
                'string',
                'max:500',
            ],
            'include_sender_contact' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.required' => __('validation.custom.share.job_required'),
            'job_id.exists' => __('validation.custom.share.job_not_found'),
            'sender_name.required' => __('validation.custom.share.sender_name_required'),
            'sender_name.regex' => __('validation.custom.share.name_format'),
            'sender_email.required' => __('validation.custom.share.sender_email_required'),
            'friend_name.required' => __('validation.custom.share.friend_name_required'),
            'friend_name.regex' => __('validation.custom.share.name_format'),
            'friend_email.required' => __('validation.custom.share.friend_email_required'),
            'friend_email.different' => __('validation.custom.share.emails_must_differ'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['sender_name', 'friend_name', 'message'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }

        foreach (['sender_email', 'friend_email'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => strtolower(trim($this->input($field)))]);
            }
        }
    }
}
