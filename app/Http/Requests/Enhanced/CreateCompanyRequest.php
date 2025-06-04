<?php

namespace App\Http\Requests\Enhanced;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Admin can create companies for any user
        if ($user->hasRole(['Admin', 'Super Admin'])) {
            return true;
        }

        // Employer can create their own company
        if ($user->hasRole('Employer')) {
            // Check if user already has a company
            if ($user->company) {
                return false; // User already has a company
            }
            
            // If user_id is provided, it must match the authenticated user
            if ($this->has('user_id') && $this->user_id != $user->id) {
                return false;
            }
            
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
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'unique:companies,name',
                'regex:/^[a-zA-Z0-9\s\-\&\.\,\(\)]+$/'
            ],
            'ceo' => [
                'required',
                'string',
                'min:2',
                'max:180',
                'regex:/^[a-zA-Z\s\-\'\.]+$/'
            ],
            'industry_id' => [
                'required',
                'integer',
                'exists:industries,id'
            ],
            'ownership_type_id' => [
                'required',
                'integer',
                'exists:ownership_types,id'
            ],
            'company_size_id' => [
                'required',
                'integer',
                'exists:company_sizes,id'
            ],
            'established_in' => [
                'required',
                'integer',
                'min:1800',
                'max:' . date('Y'),
                'digits:4'
            ],
            'details' => [
                'nullable',
                'string',
                'min:10',
                'max:5000'
            ],
            'website' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(\/.*)?$/'
            ],
            'location' => [
                'required',
                'string',
                'min:2',
                'max:255'
            ],
            'location2' => [
                'nullable',
                'string',
                'max:255'
            ],
            'no_of_offices' => [
                'required',
                'integer',
                'min:1',
                'max:10000'
            ],
            'fax' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[1-9][\d\s\-\(\)]{0,19}$/'
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!$user) {
                        $fail('The selected user is invalid.');
                        return;
                    }
                    
                    if (!$user->hasRole(['Employer', 'Admin', 'Super Admin'])) {
                        $fail('The selected user must be an employer or admin.');
                        return;
                    }
                    
                    if ($user->hasRole('Employer') && $user->company) {
                        $fail('The selected user already has a company.');
                        return;
                    }
                }
            ],
            'facebook_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?facebook\.com\/.*$/'
            ],
            'twitter_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?twitter\.com\/.*$/'
            ],
            'linkedin_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?linkedin\.com\/.*$/'
            ],
            'google_plus_url' => [
                'nullable',
                'url',
                'max:255'
            ],
            'pinterest_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?pinterest\.com\/.*$/'
            ],
            'is_featured' => [
                'nullable',
                'boolean'
            ],
            'status' => [
                'nullable',
                'integer',
                'in:' . implode(',', [Company::STATUS_ACTIVE, Company::STATUS_INACTIVE, Company::STATUS_PENDING])
            ]
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required.',
            'name.min' => 'Company name must be at least 2 characters.',
            'name.max' => 'Company name cannot exceed 255 characters.',
            'name.unique' => 'A company with this name already exists.',
            'name.regex' => 'Company name can only contain letters, numbers, spaces, and common punctuation.',
            
            'ceo.required' => 'CEO name is required.',
            'ceo.min' => 'CEO name must be at least 2 characters.',
            'ceo.max' => 'CEO name cannot exceed 180 characters.',
            'ceo.regex' => 'CEO name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            
            'industry_id.required' => 'Please select an industry.',
            'industry_id.exists' => 'The selected industry is invalid.',
            
            'ownership_type_id.required' => 'Please select an ownership type.',
            'ownership_type_id.exists' => 'The selected ownership type is invalid.',
            
            'company_size_id.required' => 'Please select company size.',
            'company_size_id.exists' => 'The selected company size is invalid.',
            
            'established_in.required' => 'Establishment year is required.',
            'established_in.integer' => 'Establishment year must be a valid year.',
            'established_in.min' => 'Establishment year must be after 1800.',
            'established_in.max' => 'Establishment year cannot be in the future.',
            'established_in.digits' => 'Establishment year must be a 4-digit year.',
            
            'details.min' => 'Company details must be at least 10 characters.',
            'details.max' => 'Company details cannot exceed 5000 characters.',
            
            'website.url' => 'Please provide a valid website URL.',
            'website.max' => 'Website URL cannot exceed 255 characters.',
            'website.regex' => 'Please provide a valid website URL format.',
            
            'location.required' => 'Company location is required.',
            'location.min' => 'Location must be at least 2 characters.',
            'location.max' => 'Location cannot exceed 255 characters.',
            
            'location2.max' => 'Secondary location cannot exceed 255 characters.',
            
            'no_of_offices.required' => 'Number of offices is required.',
            'no_of_offices.integer' => 'Number of offices must be a valid number.',
            'no_of_offices.min' => 'Number of offices must be at least 1.',
            'no_of_offices.max' => 'Number of offices cannot exceed 10,000.',
            
            'fax.max' => 'Fax number cannot exceed 20 characters.',
            'fax.regex' => 'Please provide a valid fax number format.',
            
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a JPEG, PNG, JPG, GIF, or SVG file.',
            'logo.max' => 'Logo file size cannot exceed 2MB.',
            'logo.dimensions' => 'Logo dimensions must be between 100x100 and 2000x2000 pixels.',
            
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'The selected user is invalid.',
            
            'facebook_url.url' => 'Please provide a valid Facebook URL.',
            'facebook_url.regex' => 'Please provide a valid Facebook page URL.',
            'twitter_url.url' => 'Please provide a valid Twitter URL.',
            'twitter_url.regex' => 'Please provide a valid Twitter profile URL.',
            'linkedin_url.url' => 'Please provide a valid LinkedIn URL.',
            'linkedin_url.regex' => 'Please provide a valid LinkedIn company page URL.',
            'pinterest_url.url' => 'Please provide a valid Pinterest URL.',
            'pinterest_url.regex' => 'Please provide a valid Pinterest profile URL.',
            
            'is_featured.boolean' => 'Featured status must be true or false.',
            'status.in' => 'Please select a valid status.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'ceo' => 'CEO name',
            'industry_id' => 'industry',
            'ownership_type_id' => 'ownership type',
            'company_size_id' => 'company size',
            'established_in' => 'establishment year',
            'no_of_offices' => 'number of offices',
            'user_id' => 'user',
            'facebook_url' => 'Facebook URL',
            'twitter_url' => 'Twitter URL',
            'linkedin_url' => 'LinkedIn URL',
            'google_plus_url' => 'Google+ URL',
            'pinterest_url' => 'Pinterest URL',
            'is_featured' => 'featured status',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up company name
        if ($this->name) {
            $this->merge([
                'name' => trim($this->name)
            ]);
        }

        // Clean up CEO name
        if ($this->ceo) {
            $this->merge([
                'ceo' => ucwords(strtolower(trim($this->ceo)))
            ]);
        }

        // Clean up website URL
        if ($this->website && !str_starts_with($this->website, 'http')) {
            $this->merge([
                'website' => 'https://' . $this->website
            ]);
        }

        // Clean up social media URLs
        if ($this->facebook_url && !str_starts_with($this->facebook_url, 'http')) {
            $this->merge(['facebook_url' => 'https://' . $this->facebook_url]);
        }

        if ($this->twitter_url && !str_starts_with($this->twitter_url, 'http')) {
            $this->merge(['twitter_url' => 'https://' . $this->twitter_url]);
        }

        if ($this->linkedin_url && !str_starts_with($this->linkedin_url, 'http')) {
            $this->merge(['linkedin_url' => 'https://' . $this->linkedin_url]);
        }

        if ($this->pinterest_url && !str_starts_with($this->pinterest_url, 'http')) {
            $this->merge(['pinterest_url' => 'https://' . $this->pinterest_url]);
        }

        // Set user_id to authenticated user if not provided and user is employer
        if (!$this->user_id && auth()->user()?->hasRole('Employer')) {
            $this->merge(['user_id' => auth()->id()]);
        }

        // Convert boolean strings to actual booleans
        if ($this->has('is_featured')) {
            $this->merge([
                'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if website domain matches company name (optional business rule)
            if ($this->website && $this->name) {
                $domain = parse_url($this->website, PHP_URL_HOST);
                $companySlug = \Illuminate\Support\Str::slug($this->name);
                
                // This is just a warning, not a hard validation
                if ($domain && !str_contains($domain, $companySlug)) {
                    // Could add a warning here if needed
                }
            }

            // Validate establishment year against company size
            if ($this->established_in && $this->company_size_id) {
                $currentYear = date('Y');
                $yearsInBusiness = $currentYear - $this->established_in;
                
                // Business logic: Very large companies should have been established for some time
                $companySize = \App\Models\CompanySize::find($this->company_size_id);
                if ($companySize && str_contains(strtolower($companySize->size), 'large') && $yearsInBusiness < 5) {
                    $validator->errors()->add('established_in', 'Large companies typically have been established for at least 5 years.');
                }
            }

            // Validate number of offices against company size
            if ($this->no_of_offices && $this->company_size_id) {
                $companySize = \App\Models\CompanySize::find($this->company_size_id);
                if ($companySize) {
                    $sizeText = strtolower($companySize->size);
                    
                    if (str_contains($sizeText, 'small') && $this->no_of_offices > 10) {
                        $validator->errors()->add('no_of_offices', 'Small companies typically have 10 or fewer offices.');
                    } elseif (str_contains($sizeText, 'startup') && $this->no_of_offices > 3) {
                        $validator->errors()->add('no_of_offices', 'Startups typically have 3 or fewer offices.');
                    }
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Company validation failed',
                'errors' => $validator->errors(),
                'error_count' => $validator->errors()->count(),
                'tips' => [
                    'Make sure all required fields are filled',
                    'Company name must be unique',
                    'Website URL should include http:// or https://',
                    'Logo file should be under 2MB',
                    'Establishment year should be realistic'
                ]
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }

    /**
     * Handle a failed authorization attempt.
     */
    public function failedAuthorization(): void
    {
        $user = auth()->user();
        
        if (!$user) {
            throw new \Illuminate\Auth\AuthenticationException('You must be logged in to create a company.');
        }

        if ($user->hasRole('Employer') && $user->company) {
            throw new \Illuminate\Auth\Access\AuthorizationException('You already have a company. Each employer can only have one company.');
        }

        if (!$user->hasRole(['Admin', 'Super Admin', 'Employer'])) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Only employers and administrators can create companies.');
        }

        throw new \Illuminate\Auth\Access\AuthorizationException('You are not authorized to create a company.');
    }
} 