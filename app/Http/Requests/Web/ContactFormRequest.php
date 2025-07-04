<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ContactFormRequest
 * Enterprise-grade validation for Web contact form operations
 */
class ContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public access
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s\-\'\.]+$/u',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'subject' => [
                'required',
                'string',
                'min:5',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.\,\!\?]+$/u',
            ],
            'message' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
            'phone' => [
                'sometimes',
                'string',
                'min:10',
                'max:20',
                'regex:/^\+?[1-9]\d{1,14}$/',
            ],
            'company' => [
                'sometimes',
                'string',
                'max:150',
                'regex:/^[\p{L}\p{N}\s\-_\.\&\,]+$/u',
            ],
            'inquiry_type' => [
                'sometimes',
                'string',
                'in:general,support,business,partnership,complaint,feedback',
            ],
            'how_did_you_hear' => [
                'sometimes',
                'string',
                'in:search_engine,social_media,referral,advertisement,direct,other',
            ],
            'newsletter_consent' => [
                'sometimes',
                'boolean',
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
            'name.required' => __('validation.custom.contact.name_required'),
            'name.regex' => __('validation.custom.contact.name_format'),
            'email.required' => __('validation.custom.contact.email_required'),
            'email.email' => __('validation.custom.contact.email_format'),
            'subject.required' => __('validation.custom.contact.subject_required'),
            'subject.regex' => __('validation.custom.contact.subject_format'),
            'message.required' => __('validation.custom.contact.message_required'),
            'message.min' => __('validation.custom.contact.message_min'),
            'phone.regex' => __('validation.custom.contact.phone_format'),
            'company.regex' => __('validation.custom.contact.company_format'),
            'inquiry_type.in' => __('validation.custom.contact.inquiry_type_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['name', 'subject', 'message', 'company'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }

        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }
    }
}
