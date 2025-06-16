<?php

namespace App\Http\Requests\Financial;

use App\Models\Plan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Comprehensive Form Request for storing Subscription Plans
 * Implements Laravel 12 best practices with comprehensive financial validation.
 */
class StorePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only admin/financial managers can create subscription plans.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Only admin or users with financial management permissions
        return $user->hasRole('Admin') || 
               $user->hasRole('Financial Manager') || 
               $user->can('manage-financial-plans');
    }

    /**
     * Get the validation rules that apply to the request.
     * Comprehensive financial plan validation.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Plan identification
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'unique:plans,name',
                'regex:/^[a-zA-Z0-9\s\-\_\.\(\)]+$/', // Alphanumeric with basic symbols
            ],
            
            'slug' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
                'unique:plans,slug',
                'regex:/^[a-z0-9\-]+$/', // URL-friendly slug
            ],
            
            'description' => [
                'nullable',
                'string',
                'min:10',
                'max:2000',
            ],
            
            'short_description' => [
                'nullable',
                'string',
                'max:255',
            ],

            // Plan type and category
            'plan_type' => [
                'required',
                'string',
                Rule::in(['basic', 'premium', 'enterprise', 'custom', 'trial']),
            ],
            
            'category' => [
                'required',
                'string',
                Rule::in(['job_posting', 'candidate_access', 'company_profile', 'analytics', 'support']),
            ],

            // Pricing structure
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/', // Valid decimal with max 2 decimal places
            ],
            
            'currency' => [
                'required',
                'string',
                'size:3',
                'exists:salary_currencies,iso_code',
            ],
            
            'billing_cycle' => [
                'required',
                'string',
                Rule::in(['monthly', 'quarterly', 'semi_annually', 'annually', 'lifetime', 'one_time']),
            ],
            
            'trial_days' => [
                'nullable',
                'integer',
                'min:0',
                'max:365',
            ],

            // Plan limits and features
            'job_postings_limit' => [
                'required',
                'integer',
                'min:0',
                'max:99999',
            ],
            
            'featured_jobs_limit' => [
                'nullable',
                'integer',
                'min:0',
                'max:99999',
            ],
            
            'candidate_cv_views_limit' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            
            'company_profile_boost' => [
                'nullable',
                'boolean',
            ],
            
            'analytics_access' => [
                'nullable',
                'boolean',
            ],
            
            'priority_support' => [
                'nullable',
                'boolean',
            ],

            // Additional features
            'features' => [
                'nullable',
                'array',
                'max:20',
            ],
            'features.*' => [
                'string',
                'max:100',
                'distinct',
            ],
            
            'restrictions' => [
                'nullable',
                'array',
                'max:20',
            ],
            'restrictions.*' => [
                'string',
                'max:100',
                'distinct',
            ],

            // Marketing and display
            'is_popular' => [
                'nullable',
                'boolean',
            ],
            
            'highlight_color' => [
                'nullable',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', // Valid hex color
            ],
            
            'display_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],

            // Status and availability
            'status' => [
                'required',
                'string',
                Rule::in(['active', 'inactive', 'draft', 'archived']),
            ],
            
            'is_visible' => [
                'nullable',
                'boolean',
            ],
            
            'available_from' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            
            'available_until' => [
                'nullable',
                'date',
                'after:available_from',
            ],

            // Terms and conditions
            'terms_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            
            'cancellation_policy' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // Metadata
            'metadata' => [
                'nullable',
                'array',
            ],
            
            'tags' => [
                'nullable',
                'array',
                'max:10',
            ],
            'tags.*' => [
                'string',
                'max:50',
                'distinct',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Multilingual financial error messages.
     */
    public function messages(): array
    {
        return [
            // Plan identification
            'name.required' => __('financial.validation.plan_name_required'),
            'name.unique' => __('financial.validation.plan_name_unique'),
            'name.regex' => __('financial.validation.plan_name_format'),
            'slug.unique' => __('financial.validation.plan_slug_unique'),
            'slug.regex' => __('financial.validation.plan_slug_format'),
            
            // Pricing
            'price.required' => __('financial.validation.price_required'),
            'price.numeric' => __('financial.validation.price_numeric'),
            'price.min' => __('financial.validation.price_min'),
            'price.max' => __('financial.validation.price_max'),
            'price.regex' => __('financial.validation.price_format'),
            'currency.required' => __('financial.validation.currency_required'),
            'currency.exists' => __('financial.validation.currency_invalid'),
            'billing_cycle.required' => __('financial.validation.billing_cycle_required'),
            'billing_cycle.in' => __('financial.validation.billing_cycle_invalid'),
            
            // Limits
            'job_postings_limit.required' => __('financial.validation.job_limit_required'),
            'job_postings_limit.min' => __('financial.validation.job_limit_min'),
            'job_postings_limit.max' => __('financial.validation.job_limit_max'),
            
            // Features
            'features.array' => __('financial.validation.features_array'),
            'features.max' => __('financial.validation.features_max'),
            'features.*.distinct' => __('financial.validation.features_unique'),
            
            // Display
            'highlight_color.regex' => __('financial.validation.color_format'),
            'available_until.after' => __('financial.validation.end_date_after_start'),
            
            // General
            'plan_type.in' => __('financial.validation.plan_type_invalid'),
            'category.in' => __('financial.validation.category_invalid'),
            'status.in' => __('financial.validation.status_invalid'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'name' => __('financial.fields.plan_name'),
            'slug' => __('financial.fields.plan_slug'),
            'description' => __('financial.fields.description'),
            'short_description' => __('financial.fields.short_description'),
            'plan_type' => __('financial.fields.plan_type'),
            'category' => __('financial.fields.category'),
            'price' => __('financial.fields.price'),
            'currency' => __('financial.fields.currency'),
            'billing_cycle' => __('financial.fields.billing_cycle'),
            'trial_days' => __('financial.fields.trial_days'),
            'job_postings_limit' => __('financial.fields.job_limit'),
            'featured_jobs_limit' => __('financial.fields.featured_limit'),
            'candidate_cv_views_limit' => __('financial.fields.cv_views_limit'),
            'features' => __('financial.fields.features'),
            'restrictions' => __('financial.fields.restrictions'),
            'highlight_color' => __('financial.fields.highlight_color'),
            'display_order' => __('financial.fields.display_order'),
            'status' => __('financial.fields.status'),
            'available_from' => __('financial.fields.available_from'),
            'available_until' => __('financial.fields.available_until'),
            'terms_url' => __('financial.fields.terms_url'),
            'cancellation_policy' => __('financial.fields.cancellation_policy'),
        ];
    }

    /**
     * Configure the validator instance.
     * Enhanced business logic validation.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check for pricing conflicts
            if ($this->hasPricingConflicts()) {
                $validator->errors()->add('price', __('financial.validation.pricing_conflicts'));
            }
            
            // Validate feature combinations
            if ($this->hasInvalidFeatureCombinations()) {
                $validator->errors()->add('features', __('financial.validation.invalid_feature_combination'));
            }
            
            // Check plan type restrictions
            if ($this->violatesPlanTypeRestrictions()) {
                $validator->errors()->add('plan_type', __('financial.validation.plan_type_restrictions'));
            }
            
            // Validate business logic consistency
            if ($this->hasBusinessLogicErrors()) {
                $validator->errors()->add('general', __('financial.validation.business_logic_error'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Financial data normalization.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug if not provided
        if (empty($this->slug) && !empty($this->name)) {
            $this->merge([
                'slug' => \Str::slug($this->name),
            ]);
        }
        
        // Normalize text fields
        $this->merge([
            'name' => trim($this->name ?? ''),
            'description' => trim($this->description ?? '') ?: null,
            'short_description' => trim($this->short_description ?? '') ?: null,
        ]);
        
        // Normalize numeric fields
        if ($this->filled('price')) {
            $this->merge([
                'price' => round((float) $this->price, 2),
            ]);
        }
        
        // Set defaults
        $this->merge([
            'status' => $this->status ?? 'draft',
            'is_visible' => filter_var($this->is_visible ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_popular' => filter_var($this->is_popular ?? false, FILTER_VALIDATE_BOOLEAN),
            'display_order' => (int) ($this->display_order ?? 0),
        ]);
        
        // Clean arrays
        if ($this->filled('features')) {
            $this->merge([
                'features' => array_filter(array_unique((array) $this->features)),
            ]);
        }
        
        if ($this->filled('restrictions')) {
            $this->merge([
                'restrictions' => array_filter(array_unique((array) $this->restrictions)),
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced financial logging.
     */
    protected function failedValidation(Validator $validator): void
    {
        \Log::warning('Financial plan creation validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input_data' => $this->safe()->toArray(),
            'user_id' => Auth::id(),
            'user_role' => Auth::user()?->getRoleNames(),
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Get processed data for plan creation.
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();
        
        // Add creator information
        $data['created_by'] = Auth::id();
        
        // Set timestamps
        $data['created_at'] = now();
        $data['updated_at'] = now();
        
        // Process features as JSON
        if (isset($data['features'])) {
            $data['features'] = json_encode($data['features']);
        }
        
        if (isset($data['restrictions'])) {
            $data['restrictions'] = json_encode($data['restrictions']);
        }
        
        if (isset($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
        }
        
        return $data;
    }

    /**
     * Check for pricing conflicts with existing plans.
     */
    private function hasPricingConflicts(): bool
    {
        if (!$this->filled(['price', 'billing_cycle', 'plan_type'])) {
            return false;
        }
        
        // Check for duplicate pricing in same category
        $existingPlan = Plan::where('price', $this->price)
            ->where('billing_cycle', $this->billing_cycle)
            ->where('plan_type', $this->plan_type)
            ->where('status', 'active')
            ->first();
            
        return $existingPlan !== null;
    }

    /**
     * Validate feature combinations.
     */
    private function hasInvalidFeatureCombinations(): bool
    {
        $features = $this->features ?? [];
        
        // Example: Premium support requires premium plan
        if (in_array('premium_support', $features) && $this->plan_type !== 'premium') {
            return true;
        }
        
        // Example: Analytics access requires certain job limits
        if (in_array('advanced_analytics', $features) && ($this->job_postings_limit ?? 0) < 10) {
            return true;
        }
        
        return false;
    }

    /**
     * Check plan type restrictions.
     */
    private function violatesPlanTypeRestrictions(): bool
    {
        $planType = $this->plan_type;
        $price = $this->price ?? 0;
        
        // Business rules for plan types
        switch ($planType) {
            case 'trial':
                return $price > 0; // Trial plans should be free
            case 'basic':
                return $price > 100; // Basic plans shouldn't exceed $100
            case 'enterprise':
                return $price < 500; // Enterprise plans should be $500+
        }
        
        return false;
    }

    /**
     * Additional business logic validation.
     */
    private function hasBusinessLogicErrors(): bool
    {
        // Check if trial days make sense for billing cycle
        if ($this->filled(['trial_days', 'billing_cycle'])) {
            $trialDays = $this->trial_days;
            $cycle = $this->billing_cycle;
            
            // Trial shouldn't be longer than billing cycle
            if ($cycle === 'monthly' && $trialDays > 30) {
                return true;
            }
        }
        
        // Check if limits make business sense
        if ($this->filled(['job_postings_limit', 'featured_jobs_limit'])) {
            $jobLimit = $this->job_postings_limit;
            $featuredLimit = $this->featured_jobs_limit ?? 0;
            
            // Featured jobs can't exceed total job limit
            if ($featuredLimit > $jobLimit) {
                return true;
            }
        }
        
        return false;
    }
}
