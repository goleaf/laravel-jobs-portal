<?php

declare(strict_types=1);

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ReportCandidateRequest
 * Enterprise-grade validation for Employer candidate reporting operations
 */
class ReportCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'candidate_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('candidates', 'id'),
            ],
            'user_id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('users', 'id'),
            ],
            'reason' => [
                'required',
                'string',
                'in:inappropriate_profile,fake_profile,spam,harassment,misrepresentation,offensive_content,other',
            ],
            'note' => [
                'required',
                'string',
                'min:10',
                'max:1000',
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
            'severity' => [
                'sometimes',
                'string',
                'in:low,medium,high,critical',
            ],
            'reporter_contact' => [
                'sometimes',
                'email:rfc,dns',
                'max:255',
            ],
            'block_candidate' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'candidate_id.required' => __('validation.custom.report.candidate_required'),
            'candidate_id.exists' => __('validation.custom.report.candidate_not_found'),
            'reason.required' => __('validation.custom.report.reason_required'),
            'reason.in' => __('validation.custom.report.reason_invalid'),
            'note.required' => __('validation.custom.report.note_required'),
            'note.min' => __('validation.custom.report.note_min_length'),
            'evidence_urls.max' => __('validation.custom.report.evidence_limit'),
            'severity.in' => __('validation.custom.report.severity_invalid'),
            'reporter_contact.email' => __('validation.custom.report.contact_email_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('note')) {
            $this->merge(['note' => trim($this->note)]);
        }

        if ($this->has('reporter_contact')) {
            $this->merge(['reporter_contact' => strtolower(trim($this->reporter_contact))]);
        }

        if (! $this->has('severity')) {
            $this->merge(['severity' => 'medium']);
        }
    }

    public function getBusinessContext(): array
    {
        return [
            'operation' => 'report_candidate',
            'candidate_id' => $this->input('candidate_id'),
            'reason' => $this->input('reason'),
            'severity' => $this->input('severity', 'medium'),
            'has_evidence' => $this->filled('evidence_urls'),
            'block_requested' => $this->boolean('block_candidate'),
            'reporter_provided_contact' => $this->filled('reporter_contact'),
        ];
    }
}
