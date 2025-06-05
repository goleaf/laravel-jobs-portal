<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('Employer') || auth()->user()->hasRole('Admin'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'email' => ['required', 'email', 'max:255', 'unique:companies,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'industry_id' => ['required', 'exists:industries,id'],
            'ownership_type_id' => ['required', 'exists:ownership_types,id'],
            'company_size_id' => ['required', 'exists:company_sizes,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'location2' => ['nullable', 'string', 'max:255'],
            'established_in' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'description' => ['nullable', 'string', 'max:65535'],
            'details' => ['nullable', 'string', 'max:65535'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'google_plus_url' => ['nullable', 'url', 'max:255'],
            'pinterest_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation_custom.company.name_required'),
            'name.unique' => __('validation_custom.company.name_unique'),
            'email.required' => __('validation_custom.company.email_required'),
            'email.unique' => __('validation_custom.company.email_unique'),
            'website.url' => __('validation_custom.company.website_url'),
            'industry_id.required' => __('validation_custom.company.industry_required'),
            'company_size_id.required' => __('validation_custom.company.company_size_required'),
            'logo.image' => __('validation_custom.general.image_mimes'),
            'logo.max' => __('validation_custom.general.image_max'),
            'established_in.min' => 'Establishment year cannot be before 1800.',
            'established_in.max' => 'Establishment year cannot be in the future.',
            'facebook_url.url' => 'Please enter a valid Facebook URL.',
            'twitter_url.url' => 'Please enter a valid Twitter URL.',
            'linkedin_url.url' => 'Please enter a valid LinkedIn URL.',
            'google_plus_url.url' => 'Please enter a valid Google Plus URL.',
            'pinterest_url.url' => 'Please enter a valid Pinterest URL.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'industry_id' => 'industry',
            'ownership_type_id' => 'ownership type',
            'company_size_id' => 'company size',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'established_in' => 'establishment year',
            'facebook_url' => 'Facebook URL',
            'twitter_url' => 'Twitter URL',
            'linkedin_url' => 'LinkedIn URL',
            'google_plus_url' => 'Google Plus URL',
            'pinterest_url' => 'Pinterest URL',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'is_featured' => $this->boolean('is_featured', false),
        ]);
    }
}
