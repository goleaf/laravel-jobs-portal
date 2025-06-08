<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Company;

class ShowCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $company = $this->route('company');
        
        // Admin can view any company
        if (Auth::user()->hasRole('Admin')) {
            return true;
        }
        
        // Company owner can view their own company
        if ($company && Auth::user()->id === $company->user_id) {
            return true;
        }
        
        // Public companies can be viewed by authenticated users
        if ($company && $company->is_active && $company->is_public) {
            return true;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'include' => [
                'sometimes',
                'array',
            ],
            'include.*' => [
                'string',
                Rule::in([
                    'user', 'country', 'state', 'city', 'industry',
                    'ownershipType', 'companySize', 'jobs', 'activeJobs',
                    'statistics', 'socialLinks'
                ]),
            ],
            'with_statistics' => [
                'sometimes',
                'boolean',
            ],
            'with_jobs' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'include.array' => __('companies.validation.include_array'),
            'include.*.in' => __('companies.validation.include_item_invalid'),
            'with_statistics.boolean' => __('companies.validation.with_statistics_boolean'),
            'with_jobs.boolean' => __('companies.validation.with_jobs_boolean'),
        ];
    }

    /**
     * Get the relationships to include.
     */
    public function getIncludes(): array
    {
        return $this->input('include', []);
    }

    /**
     * Check if statistics should be included.
     */
    public function shouldIncludeStatistics(): bool
    {
        return $this->boolean('with_statistics', false);
    }

    /**
     * Check if jobs should be included.
     */
    public function shouldIncludeJobs(): bool
    {
        return $this->boolean('with_jobs', false);
    }
}
