<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ReportJobAbuseRequest
 * Enterprise-grade validation for reporting job abuse
 */
class ReportJobAbuseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public access for reporting
    }

    public function rules(): array
    {
        return [
            'job_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('jobs', 'id'),
            ],
            'reason' => [
                'required',
                'string',
                'in:spam,inappropriate,misleading,fake,offensive,duplicate,other',
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
            'reporter_email' => [
                'sometimes',
                'email:rfc,dns',
                'max:255',
            ],
            'evidence_urls' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'evidence_urls.*' => [
                'url',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.required' => __('validation.custom.report.job_required'),
            'job_id.exists' => __('validation.custom.report.job_not_found'),
            'reason.required' => __('validation.custom.report.reason_required'),
            'reason.in' => __('validation.custom.report.reason_invalid'),
            'description.required' => __('validation.custom.report.description_required'),
            'description.min' => __('validation.custom.report.description_min'),
            'evidence_urls.max' => __('validation.custom.report.evidence_limit'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('description')) {
            $this->merge(['description' => trim($this->description)]);
        }

        if ($this->has('reporter_email')) {
            $this->merge(['reporter_email' => strtolower(trim($this->reporter_email))]);
        }
    }
}
