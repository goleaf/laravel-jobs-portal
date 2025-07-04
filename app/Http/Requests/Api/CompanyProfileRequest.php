<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CompanyProfileRequest
 * Enterprise-grade validation for API Company profile operations
 */
class CompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'company_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('companies', 'id')->where('is_active', 1),
            ],
            'include_details' => [
                'sometimes',
                'array',
                'max:15',
            ],
            'include_details.*' => [
                'string',
                'in:basic_info,contact_info,social_media,jobs,statistics,reviews,financial_info,certifications,team,gallery,benefits,culture,history,awards,locations',
            ],
            'with_analytics' => [
                'sometimes',
                'boolean',
            ],
            'analytics_period' => [
                'sometimes',
                'string',
                'in:today,week,month,quarter,year,all_time',
            ],
            'include_jobs' => [
                'sometimes',
                'boolean',
            ],
            'jobs_status' => [
                'sometimes',
                'string',
                'in:active,inactive,all',
            ],
            'jobs_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'include_reviews' => [
                'sometimes',
                'boolean',
            ],
            'reviews_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:50',
            ],
            'reviews_rating_filter' => [
                'sometimes',
                'integer',
                'min:1',
                'max:5',
            ],
            'include_team' => [
                'sometimes',
                'boolean',
            ],
            'team_roles' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'team_roles.*' => [
                'string',
                'in:management,hr,technical,sales,marketing,support,finance,operations',
            ],
            'with_contact_info' => [
                'sometimes',
                'boolean',
            ],
            'include_locations' => [
                'sometimes',
                'boolean',
            ],
            'location_type' => [
                'sometimes',
                'string',
                'in:headquarters,branches,all',
            ],
            'include_certifications' => [
                'sometimes',
                'boolean',
            ],
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'timezone' => [
                'sometimes',
                'string',
                'timezone',
                'max:50',
            ],
            'format' => [
                'sometimes',
                'string',
                'in:json,xml,detailed',
            ],
            'api_version' => [
                'sometimes',
                'string',
                'in:v1,v2,latest',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'with_analytics' => false,
            'analytics_period' => 'month',
            'include_jobs' => false,
            'jobs_status' => 'active',
            'jobs_limit' => 10,
            'include_reviews' => false,
            'reviews_limit' => 10,
            'include_team' => false,
            'with_contact_info' => true,
            'include_locations' => false,
            'location_type' => 'headquarters',
            'include_certifications' => false,
            'locale' => app()->getLocale(),
            'format' => 'json',
            'api_version' => 'v1',
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'company_id.required' => __('validation.custom.company_profile.company_id_required'),
            'company_id.exists' => __('validation.custom.company_profile.company_not_found'),
            'include_details.max' => __('validation.custom.company_profile.details_limit'),
            'include_details.*.in' => __('validation.custom.company_profile.detail_invalid'),
            'analytics_period.in' => __('validation.custom.company_profile.period_invalid'),
            'jobs_limit.max' => __('validation.custom.company_profile.jobs_limit'),
            'reviews_limit.max' => __('validation.custom.company_profile.reviews_limit'),
            'team_roles.max' => __('validation.custom.company_profile.team_roles_limit'),
            'team_roles.*.in' => __('validation.custom.company_profile.team_role_invalid'),
            'locale.regex' => __('validation.custom.company_profile.locale_format'),
            'timezone.timezone' => __('validation.custom.company_profile.timezone_invalid'),
            'format.in' => __('validation.custom.company_profile.format_invalid'),
            'api_version.in' => __('validation.custom.company_profile.version_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('locale')) {
            $this->merge(['locale' => strtolower(trim($this->locale))]);
        }

        foreach (['include_details', 'team_roles'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
