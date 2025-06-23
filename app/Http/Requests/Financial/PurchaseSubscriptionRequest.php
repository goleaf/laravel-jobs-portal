<?php

namespace App\Http\Requests\Financial;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PurchaseSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Plan selection
            'plan_id' => [
                'required',
                'integer',
                'min:1',
                'exists:plans,id',
                function ($attribute, $value, $fail) {
                    if (!$this->validatePlanAvailable($value)) {
                        $fail(__('validation.plan_not_available'));
                    }
                },
            ],

            'plan_type' => [
                'required',
                'string',
                Rule::in([
                    'basic',
                    'premium',
                    'enterprise',
                    'custom',
                    'trial',
                    'promotional',
                ]),
            ],

            // Billing information
            'billing_cycle' => [
                'required',
                'string',
                Rule::in(['monthly', 'quarterly', 'semi_annual', 'annual', 'biennial']),
            ],

            'billing_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:100000',
                function ($attribute, $value, $fail) {
                    if (!$this->validateBillingAmount($value)) {
                        $fail(__('validation.invalid_billing_amount'));
                    }
                },
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
                'exists:currencies,code',
                Rule::in(['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CNY', 'INR']),
            ],

            'discount_code' => [
                'sometimes',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->validateDiscountCode($value)) {
                        $fail(__('validation.invalid_discount_code'));
                    }
                },
            ],

            'discount_amount' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:' . ($this->billing_amount ?? 0),
            ],

            'tax_amount' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000',
            ],

            'total_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:110000',
            ],

            // Payment method
            'payment_method' => [
                'required',
                'string',
                Rule::in([
                    'credit_card',
                    'debit_card',
                    'paypal',
                    'stripe',
                    'bank_transfer',
                    'cryptocurrency',
                    'apple_pay',
                    'google_pay',
                    'amazon_pay',
                ]),
            ],

            'payment_token' => [
                'required_unless:payment_method,bank_transfer',
                'string',
                'max:500',
            ],

            // Credit card details (if applicable)
            'card_number' => [
                'required_if:payment_method,credit_card,debit_card',
                'string',
                'regex:/^[0-9]{13,19}$/',
            ],

            'card_expiry_month' => [
                'required_if:payment_method,credit_card,debit_card',
                'integer',
                'min:1',
                'max:12',
            ],

            'card_expiry_year' => [
                'required_if:payment_method,credit_card,debit_card',
                'integer',
                'min:' . date('Y'),
                'max:' . (date('Y') + 20),
            ],

            'card_cvv' => [
                'required_if:payment_method,credit_card,debit_card',
                'string',
                'regex:/^[0-9]{3,4}$/',
            ],

            'card_holder_name' => [
                'required_if:payment_method,credit_card,debit_card',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\.\']+$/',
            ],

            // Billing address
            'billing_address' => [
                'required',
                'array',
            ],

            'billing_address.street' => [
                'required',
                'string',
                'max:255',
            ],

            'billing_address.city' => [
                'required',
                'string',
                'max:100',
            ],

            'billing_address.state' => [
                'required',
                'string',
                'max:100',
            ],

            'billing_address.postal_code' => [
                'required',
                'string',
                'max:20',
            ],

            'billing_address.country' => [
                'required',
                'string',
                'size:2',
                'exists:countries,code',
            ],

            // Company information
            'company_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'company_tax_id' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'company_registration' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Subscription preferences
            'auto_renewal' => [
                'sometimes',
                'boolean',
            ],

            'renewal_notification' => [
                'sometimes',
                'boolean',
            ],

            'start_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
                'before:' . now()->addMonths(3)->toDateString(),
            ],

            'end_date' => [
                'sometimes',
                'date',
                'after:start_date',
            ],

            // Add-ons and features
            'addons' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'addons.*' => [
                'integer',
                'exists:plan_addons,id',
            ],

            'addon_quantities' => [
                'sometimes',
                'array',
            ],

            'addon_quantities.*' => [
                'integer',
                'min:1',
                'max:1000',
            ],

            // Usage limits
            'job_postings_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:10000',
            ],

            'candidate_searches_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100000',
            ],

            'storage_limit_gb' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],

            // Legal and compliance
            'terms_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],

            'privacy_policy_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],

            'marketing_consent' => [
                'sometimes',
                'boolean',
            ],

            'data_processing_consent' => [
                'required',
                'boolean',
                'accepted',
            ],

            // Security and fraud prevention
            'ip_address' => [
                'sometimes',
                'ip',
            ],

            'user_agent' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'device_fingerprint' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'risk_score' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            // Referral and tracking
            'referral_code' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'utm_source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'utm_medium' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'utm_campaign' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Integration parameters
            'external_reference' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'webhook_url' => [
                'sometimes',
                'url',
                'max:500',
            ],

            'callback_url' => [
                'sometimes',
                'url',
                'max:500',
            ],

            // Trial and promotional
            'trial_days' => [
                'sometimes',
                'integer',
                'min:0',
                'max:365',
            ],

            'promotional_rate' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100',
            ],

            'promotional_duration' => [
                'sometimes',
                'integer',
                'min:1',
                'max:12',
            ],

            // Payment scheduling
            'payment_schedule' => [
                'sometimes',
                'string',
                Rule::in(['immediate', 'deferred', 'installments']),
            ],

            'installment_count' => [
                'required_if:payment_schedule,installments',
                'integer',
                'min:2',
                'max:12',
            ],

            'first_payment_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
                'before:' . now()->addDays(30)->toDateString(),
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'plan_id.required' => __('validation.required_field', ['field' => __('validation.attributes.plan')]),
            'plan_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.plan')]),
            
            'plan_type.required' => __('validation.required_field', ['field' => __('validation.attributes.plan_type')]),
            'plan_type.in' => __('validation.invalid_plan_type'),
            
            'billing_cycle.required' => __('validation.required_field', ['field' => __('validation.attributes.billing_cycle')]),
            'billing_cycle.in' => __('validation.invalid_billing_cycle'),
            
            'billing_amount.required' => __('validation.required_field', ['field' => __('validation.attributes.billing_amount')]),
            'billing_amount.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.billing_amount')]),
            'billing_amount.max' => __('validation.max_value', ['attribute' => __('validation.attributes.billing_amount'), 'max' => 100000]),
            
            'currency.required' => __('validation.required_field', ['field' => __('validation.attributes.currency')]),
            'currency.size' => __('validation.size', ['attribute' => __('validation.attributes.currency'), 'size' => 3]),
            'currency.exists' => __('validation.exists', ['attribute' => __('validation.attributes.currency')]),
            'currency.in' => __('validation.unsupported_currency'),
            
            'discount_code.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.discount_code'), 'max' => 50]),
            
            'total_amount.required' => __('validation.required_field', ['field' => __('validation.attributes.total_amount')]),
            'total_amount.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.total_amount')]),
            
            'payment_method.required' => __('validation.required_field', ['field' => __('validation.attributes.payment_method')]),
            'payment_method.in' => __('validation.invalid_payment_method'),
            
            'payment_token.required_unless' => __('validation.payment_token_required'),
            'payment_token.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.payment_token'), 'max' => 500]),
            
            'card_number.required_if' => __('validation.card_number_required'),
            'card_number.regex' => __('validation.invalid_card_number'),
            
            'card_expiry_month.required_if' => __('validation.card_expiry_required'),
            'card_expiry_month.min' => __('validation.invalid_month'),
            'card_expiry_month.max' => __('validation.invalid_month'),
            
            'card_expiry_year.required_if' => __('validation.card_expiry_required'),
            'card_expiry_year.min' => __('validation.card_expired'),
            
            'card_cvv.required_if' => __('validation.card_cvv_required'),
            'card_cvv.regex' => __('validation.invalid_cvv'),
            
            'card_holder_name.required_if' => __('validation.card_holder_required'),
            'card_holder_name.regex' => __('validation.invalid_card_holder_name'),
            
            'billing_address.required' => __('validation.required_field', ['field' => __('validation.attributes.billing_address')]),
            'billing_address.array' => __('validation.array', ['attribute' => __('validation.attributes.billing_address')]),
            
            'billing_address.street.required' => __('validation.required_field', ['field' => __('validation.attributes.street_address')]),
            'billing_address.city.required' => __('validation.required_field', ['field' => __('validation.attributes.city')]),
            'billing_address.state.required' => __('validation.required_field', ['field' => __('validation.attributes.state')]),
            'billing_address.postal_code.required' => __('validation.required_field', ['field' => __('validation.attributes.postal_code')]),
            'billing_address.country.required' => __('validation.required_field', ['field' => __('validation.attributes.country')]),
            'billing_address.country.exists' => __('validation.exists', ['attribute' => __('validation.attributes.country')]),
            
            'start_date.after_or_equal' => __('validation.start_date_future'),
            'start_date.before' => __('validation.start_date_limit'),
            
            'end_date.after' => __('validation.end_date_after_start'),
            
            'addons.array' => __('validation.array', ['attribute' => __('validation.attributes.addons')]),
            'addons.max' => __('validation.max_items', ['attribute' => __('validation.attributes.addons'), 'max' => 20]),
            'addons.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.addon')]),
            
            'terms_accepted.required' => __('validation.terms_required'),
            'terms_accepted.accepted' => __('validation.terms_must_accept'),
            
            'privacy_policy_accepted.required' => __('validation.privacy_policy_required'),
            'privacy_policy_accepted.accepted' => __('validation.privacy_policy_must_accept'),
            
            'data_processing_consent.required' => __('validation.data_processing_required'),
            'data_processing_consent.accepted' => __('validation.data_processing_must_accept'),
            
            'ip_address.ip' => __('validation.ip', ['attribute' => __('validation.attributes.ip_address')]),
            
            'webhook_url.url' => __('validation.url', ['attribute' => __('validation.attributes.webhook_url')]),
            'callback_url.url' => __('validation.url', ['attribute' => __('validation.attributes.callback_url')]),
            
            'trial_days.max' => __('validation.max_value', ['attribute' => __('validation.attributes.trial_days'), 'max' => 365]),
            
            'installment_count.required_if' => __('validation.installment_count_required'),
            'installment_count.min' => __('validation.min_installments'),
            'installment_count.max' => __('validation.max_installments'),
            
            'first_payment_date.after_or_equal' => __('validation.payment_date_future'),
            'first_payment_date.before' => __('validation.payment_date_limit'),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'plan_id' => __('validation.attributes.plan'),
            'plan_type' => __('validation.attributes.plan_type'),
            'billing_cycle' => __('validation.attributes.billing_cycle'),
            'billing_amount' => __('validation.attributes.billing_amount'),
            'currency' => __('validation.attributes.currency'),
            'discount_code' => __('validation.attributes.discount_code'),
            'discount_amount' => __('validation.attributes.discount_amount'),
            'tax_amount' => __('validation.attributes.tax_amount'),
            'total_amount' => __('validation.attributes.total_amount'),
            'payment_method' => __('validation.attributes.payment_method'),
            'payment_token' => __('validation.attributes.payment_token'),
            'card_number' => __('validation.attributes.card_number'),
            'card_expiry_month' => __('validation.attributes.card_expiry_month'),
            'card_expiry_year' => __('validation.attributes.card_expiry_year'),
            'card_cvv' => __('validation.attributes.card_cvv'),
            'card_holder_name' => __('validation.attributes.card_holder_name'),
            'billing_address' => __('validation.attributes.billing_address'),
            'company_name' => __('validation.attributes.company_name'),
            'company_tax_id' => __('validation.attributes.company_tax_id'),
            'auto_renewal' => __('validation.attributes.auto_renewal'),
            'start_date' => __('validation.attributes.start_date'),
            'end_date' => __('validation.attributes.end_date'),
            'addons' => __('validation.attributes.addons'),
            'terms_accepted' => __('validation.attributes.terms_accepted'),
            'privacy_policy_accepted' => __('validation.attributes.privacy_policy_accepted'),
            'data_processing_consent' => __('validation.attributes.data_processing_consent'),
            'trial_days' => __('validation.attributes.trial_days'),
            'installment_count' => __('validation.attributes.installment_count'),
            'first_payment_date' => __('validation.attributes.first_payment_date'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'auto_renewal' => $this->boolean('auto_renewal', true),
            'renewal_notification' => $this->boolean('renewal_notification', true),
            'marketing_consent' => $this->boolean('marketing_consent', false),
            'start_date' => $this->start_date ?? now()->toDateString(),
            'ip_address' => $this->ip_address ?? $this->ip(),
            'user_agent' => $this->user_agent ?? $this->userAgent(),
            'payment_schedule' => $this->payment_schedule ?? 'immediate',
        ]);

        // Process card number (remove spaces and dashes)
        if ($this->has('card_number')) {
            $this->merge([
                'card_number' => preg_replace('/[\s\-]/', '', $this->card_number),
            ]);
        }

        // Calculate end date based on billing cycle
        if ($this->has('start_date') && $this->has('billing_cycle') && !$this->has('end_date')) {
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $endDate = match($this->billing_cycle) {
                'monthly' => $startDate->copy()->addMonth(),
                'quarterly' => $startDate->copy()->addMonths(3),
                'semi_annual' => $startDate->copy()->addMonths(6),
                'annual' => $startDate->copy()->addYear(),
                'biennial' => $startDate->copy()->addYears(2),
                default => $startDate->copy()->addMonth(),
            };
            
            $this->merge([
                'end_date' => $endDate->toDateString(),
            ]);
        }

        // Process addons array
        if ($this->has('addons') && is_string($this->addons)) {
            $this->merge([
                'addons' => array_filter(array_map('intval', explode(',', $this->addons))),
            ]);
        }

        // Calculate total amount if not provided
        if (!$this->has('total_amount') && $this->has('billing_amount')) {
            $total = $this->billing_amount;
            $total -= $this->discount_amount ?? 0;
            $total += $this->tax_amount ?? 0;
            
            $this->merge([
                'total_amount' => round($total, 2),
            ]);
        }

        // Generate device fingerprint if not provided
        if (!$this->has('device_fingerprint')) {
            $this->merge([
                'device_fingerprint' => md5($this->userAgent() . $this->ip()),
            ]);
        }

        // Log purchase attempt for security monitoring
        Log::info('Subscription purchase attempt', [
            'plan_id' => $this->plan_id,
            'plan_type' => $this->plan_type,
            'billing_amount' => $this->billing_amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Set transaction metadata
        $this->merge([
            'transaction_id' => 'SUB-' . date('Ymd') . '-' . strtoupper(substr(md5(time() . $this->ip()), 0, 10)),
            'validated_at' => now(),
            'request_source' => $this->header('X-Request-Source', 'web'),
        ]);

        // Security and compliance flags
        $this->merge([
            'requires_verification' => $this->shouldRequireVerification(),
            'fraud_check_required' => $this->shouldCheckFraud(),
            'compliance_verified' => $this->verifyCompliance(),
            'payment_ready' => true,
        ]);
    }

    /**
     * Validate if plan is available for purchase.
     */
    private function validatePlanAvailable($planId): bool
    {
        $plan = \DB::table('plans')
            ->where('id', $planId)
            ->where('is_active', true)
            ->where('is_available', true)
            ->first();

        return $plan !== null;
    }

    /**
     * Validate billing amount against plan pricing.
     */
    private function validateBillingAmount($amount): bool
    {
        if (!$this->has('plan_id')) {
            return true; // Will be caught by plan_id validation
        }

        $plan = \DB::table('plans')->where('id', $this->plan_id)->first();
        if (!$plan) {
            return false;
        }

        // Allow for reasonable variance (±5%) to account for taxes, discounts
        $expectedAmount = $plan->price ?? 0;
        $variance = $expectedAmount * 0.05;
        
        return $amount >= ($expectedAmount - $variance) && $amount <= ($expectedAmount + $variance + 1000); // +1000 for taxes
    }

    /**
     * Validate discount code.
     */
    private function validateDiscountCode($code): bool
    {
        $discount = \DB::table('discount_codes')
            ->where('code', $code)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->first();

        return $discount !== null;
    }

    /**
     * Determine if verification is required.
     */
    private function shouldRequireVerification(): bool
    {
        return $this->total_amount > 1000 || 
               $this->payment_method === 'bank_transfer' ||
               ($this->risk_score ?? 0) > 50;
    }

    /**
     * Determine if fraud check is required.
     */
    private function shouldCheckFraud(): bool
    {
        return $this->total_amount > 500 ||
               $this->payment_method === 'cryptocurrency' ||
               ($this->risk_score ?? 0) > 30;
    }

    /**
     * Verify compliance requirements.
     */
    private function verifyCompliance(): bool
    {
        return $this->terms_accepted && 
               $this->privacy_policy_accepted && 
               $this->data_processing_consent;
    }
} 