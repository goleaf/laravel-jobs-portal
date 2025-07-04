<?php

declare(strict_types=1);

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CompanyBulkActionRequest
 * Enterprise-grade validation for Enhanced company bulk action operations
 */
class CompanyBulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                'in:activate,deactivate,delete,export,verify,suspend,approve',
            ],
            'company_ids' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],
            'company_ids.*' => [
                'integer',
                'min:1',
                Rule::exists('companies', 'id'),
            ],
            'reason' => [
                'required_if:action,suspend,delete',
                'string',
                'max:500',
            ],
            'notify_companies' => [
                'sometimes',
                'boolean',
            ],
            'notification_message' => [
                'required_if:notify_companies,true',
                'string',
                'max:1000',
            ],
            'confirmation' => [
                'required_if:action,delete',
                'boolean',
                'accepted',
            ],
            'schedule_date' => [
                'sometimes',
                'date',
                'after:now',
            ],
            'export_format' => [
                'required_if:action,export',
                'string',
                'in:csv,excel,pdf',
            ],
            'include_statistics' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => __('validation.custom.bulk.action_required'),
            'action.in' => __('validation.custom.bulk.action_invalid'),
            'company_ids.required' => __('validation.custom.bulk.companies_required'),
            'company_ids.min' => __('validation.custom.bulk.companies_min'),
            'company_ids.max' => __('validation.custom.bulk.companies_limit'),
            'company_ids.*.exists' => __('validation.custom.bulk.company_not_found'),
            'reason.required_if' => __('validation.custom.bulk.reason_required'),
            'notification_message.required_if' => __('validation.custom.bulk.message_required'),
            'confirmation.required_if' => __('validation.custom.bulk.confirmation_required'),
            'confirmation.accepted' => __('validation.custom.bulk.confirmation_must_accept'),
            'export_format.required_if' => __('validation.custom.bulk.format_required'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('reason')) {
            $this->merge(['reason' => trim($this->reason)]);
        }
        if ($this->has('notification_message')) {
            $this->merge(['notification_message' => trim($this->notification_message)]);
        }
    }
}
