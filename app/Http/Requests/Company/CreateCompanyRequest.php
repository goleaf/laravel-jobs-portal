<?php

namespace App\Http\Requests\Company;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Company::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:companies,name',
            'email' => 'required|email|unique:companies,email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|min:50',
            'established_in' => 'nullable|integer|min:1800|max:' . date('Y'),
            'company_size_id' => 'nullable|integer|exists:company_sizes,id',
            'industry_id' => 'nullable|integer|exists:industries,id',
            'ownership_type_id' => 'nullable|integer|exists:ownership_types,id',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'postal_code' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.company.name.required'),
            'name.string' => __('validation.company.name.string'),
            'name.max' => __('validation.company.name.max'),
            'name.unique' => __('validation.company.name.unique'),
            'email.required' => __('validation.company.email.required'),
            'email.email' => __('validation.company.email.email'),
            'email.unique' => __('validation.company.email.unique'),
            'email.max' => __('validation.company.email.max'),
            'phone.string' => __('validation.company.phone.string'),
            'phone.max' => __('validation.company.phone.max'),
            'website.url' => __('validation.company.website.url'),
            'website.max' => __('validation.company.website.max'),
            'description.string' => __('validation.company.description.string'),
            'description.min' => __('validation.company.description.min'),
            'established_in.integer' => __('validation.company.established_in.integer'),
            'established_in.min' => __('validation.company.established_in.min'),
            'established_in.max' => __('validation.company.established_in.max'),
            'company_size_id.exists' => __('validation.company.company_size_id.exists'),
            'industry_id.exists' => __('validation.company.industry_id.exists'),
            'ownership_type_id.exists' => __('validation.company.ownership_type_id.exists'),
            'country_id.required' => __('validation.company.country_id.required'),
            'country_id.exists' => __('validation.company.country_id.exists'),
            'state_id.exists' => __('validation.company.state_id.exists'),
            'city_id.exists' => __('validation.company.city_id.exists'),
            'address.string' => __('validation.company.address.string'),
            'address.max' => __('validation.company.address.max'),
            'postal_code.string' => __('validation.company.postal_code.string'),
            'postal_code.max' => __('validation.company.postal_code.max'),
            'logo.image' => __('validation.company.logo.image'),
            'logo.mimes' => __('validation.company.logo.mimes'),
            'logo.max' => __('validation.company.logo.max'),
            'facebook_url.url' => __('validation.company.facebook_url.url'),
            'twitter_url.url' => __('validation.company.twitter_url.url'),
            'linkedin_url.url' => __('validation.company.linkedin_url.url'),
            'instagram_url.url' => __('validation.company.instagram_url.url'),
            'youtube_url.url' => __('validation.company.youtube_url.url'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('attributes.company.name'),
            'email' => __('attributes.company.email'),
            'phone' => __('attributes.company.phone'),
            'website' => __('attributes.company.website'),
            'description' => __('attributes.company.description'),
            'established_in' => __('attributes.company.established_in'),
            'company_size_id' => __('attributes.company.company_size'),
            'industry_id' => __('attributes.company.industry'),
            'ownership_type_id' => __('attributes.company.ownership_type'),
            'country_id' => __('attributes.company.country'),
            'state_id' => __('attributes.company.state'),
            'city_id' => __('attributes.company.city'),
            'address' => __('attributes.company.address'),
            'postal_code' => __('attributes.company.postal_code'),
            'logo' => __('attributes.company.logo'),
            'facebook_url' => __('attributes.company.facebook_url'),
            'twitter_url' => __('attributes.company.twitter_url'),
            'linkedin_url' => __('attributes.company.linkedin_url'),
            'instagram_url' => __('attributes.company.instagram_url'),
            'youtube_url' => __('attributes.company.youtube_url'),
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

        // Clean phone number
        if ($this->has('phone')) {
            $phone = preg_replace('/[^0-9+\-\s]/', '', $this->input('phone'));
            $this->merge(['phone' => $phone]);
        }

        // Ensure URLs have proper protocol
        $urlFields = ['website', 'facebook_url', 'twitter_url', 'linkedin_url', 'instagram_url', 'youtube_url'];
        foreach ($urlFields as $field) {
            if ($this->has($field) && $this->input($field)) {
                $url = $this->input($field);
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = 'https://' . $url;
                }
                $this->merge([$field => $url]);
            }
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log company creation attempt for security
        \Log::info('Company creation attempted', [
            'user_id' => $this->user()->id,
            'company_name' => $this->input('name'),
            'company_email' => $this->input('email'),
            'ip' => $this->ip(),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate location hierarchy
            if ($this->input('state_id') && $this->input('country_id')) {
                $state = \App\Models\State::find($this->input('state_id'));
                if ($state && $state->country_id != $this->input('country_id')) {
                    $validator->errors()->add('state_id', __('validation.company.state_country_mismatch'));
                }
            }

            if ($this->input('city_id') && $this->input('state_id')) {
                $city = \App\Models\City::find($this->input('city_id'));
                if ($city && $city->state_id != $this->input('state_id')) {
                    $validator->errors()->add('city_id', __('validation.company.city_state_mismatch'));
                }
            }

            // Validate establishment year is reasonable
            if ($this->input('established_in')) {
                $currentYear = date('Y');
                $establishedYear = $this->input('established_in');
                
                if ($establishedYear > $currentYear) {
                    $validator->errors()->add('established_in', __('validation.company.established_in.future'));
                }
                
                if ($establishedYear < 1800) {
                    $validator->errors()->add('established_in', __('validation.company.established_in.too_old'));
                }
            }

            // Validate social media URLs format
            $socialUrls = [
                'facebook_url' => 'facebook.com',
                'twitter_url' => 'twitter.com',
                'linkedin_url' => 'linkedin.com',
                'instagram_url' => 'instagram.com',
                'youtube_url' => 'youtube.com',
            ];

            foreach ($socialUrls as $field => $domain) {
                if ($this->input($field) && !str_contains($this->input($field), $domain)) {
                    $validator->errors()->add($field, __('validation.company.social_url_invalid', ['platform' => ucfirst(str_replace('_url', '', $field))]));
                }
            }
        });
    }
} 