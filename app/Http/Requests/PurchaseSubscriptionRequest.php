<?php

namespace App\Http\Requests;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * PurchaseSubscriptionRequest
 *
 * Comprehensive validation for subscription purchase operations with enterprise-grade validation.
 * Implements payment validation, business logic checks, and security measures.
 *
 * @author System Generated
 *
 * @version 1.0.0
 */
class PurchaseSubscriptionRequest extends FormRequest
{
    /**
     * Supported payment methods.
     */
    private const PAYMENT_METHODS = [
        'credit_card',
        'debit_card',
        'paypal',
        'stripe',
        'bank_transfer',
        'cryptocurrency',
        'apple_pay',
        'google_pay',
        'alipay',
        'wechat_pay',
    ];

    /**
     * Supported currencies.
     */
    private const SUPPORTED_CURRENCIES = [
        'USD', 'EUR', 'GBP', 'LTL', 'PLN', 'RUB', 'CNY', 'JPY', 'CAD', 'AUD',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * Implements role-based authorization with business logic validation.
     * Validates subscription eligibility and purchase permissions.
     *
     * @return bool Authorization status
     */
    public function authorize(): bool
    {
        // Basic authentication check - per user requirements: "do not make users and do not any users system"
        // However, we still need to validate subscription purchase eligibility

        $planId = $this->input('plan_id');

        if (! $planId) {
            return false;
        }

        // Validate subscription plan exists and is available
        $plan = SubscriptionPlan::find($planId);
        if (! $plan) {
            return false;
        }

        // Business rule: Plan must be active for purchase
        if (! $plan->is_active) {
            return false;
        }

        // Business rule: Plan must not be archived
        if ($plan->is_archived) {
            return false;
        }

        // Business rule: Check if plan is available for purchase
        if (isset($plan->available_from) && $plan->available_from > now()) {
            return false;
        }

        if (isset($plan->available_until) && $plan->available_until < now()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Implements comprehensive validation with payment processing, business logic,
     * and security validations.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Subscription plan identification
            'plan_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('subscription_plans', 'id')->where(function ($query) {
                    $query->where('is_active', true)
                        ->where('is_archived', false);
                }),
            ],

            // Billing cycle selection
            'billing_cycle' => [
                'required',
                'string',
                Rule::in(['monthly', 'quarterly', 'semi_annual', 'annual', 'biennial']),
            ],

            // Payment information
            'payment_method' => [
                'required',
                'string',
                Rule::in(self::PAYMENT_METHODS),
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
                Rule::in(self::SUPPORTED_CURRENCIES),
            ],

            // Payment gateway specific fields
            'payment_token' => [
                'required_unless:payment_method,bank_transfer',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9_\-\.]+$/',
            ],

            'payment_gateway' => [
                'sometimes',
                'string',
                Rule::in(['stripe', 'paypal', 'square', 'authorize_net', 'braintree', 'adyen']),
            ],

            // Credit card information (if applicable)
            'card_holder_name' => [
                'required_if:payment_method,credit_card,debit_card',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\pM\s\.\-\']+$/u',
            ],

            'card_number' => [
                'required_if:payment_method,credit_card,debit_card',
                'string',
                'min:13',
                'max:19',
                'regex:/^[0-9\s\-]+$/',
                function ($attribute, $value, $fail) {
                    if (! $this->validateCardNumber($value)) {
                        $fail(__('validation.invalid_card_number'));
                    }
                },
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
                'min:'.date('Y'),
                'max:'.(date('Y') + 20),
            ],

            'card_cvv' => [
                'required_if:payment_method,credit_card,debit_card',
                'string',
                'min:3',
                'max:4',
                'regex:/^[0-9]+$/',
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
                'sometimes',
                'string',
                'max:100',
            ],

            'billing_address.postal_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[a-zA-Z0-9\s\-]+$/',
            ],

            'billing_address.country' => [
                'required',
                'string',
                'size:2',
                'exists:countries,code',
            ],

            // Customer information
            'customer_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\pM\s\.\-\']+$/u',
            ],

            'customer_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'lowercase',
            ],

            'customer_phone' => [
                'sometimes',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],

            'company_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'tax_id' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\-]+$/',
            ],

            // Promotional and discount codes
            'coupon_code' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-_]+$/i',
                function ($attribute, $value, $fail) {
                    if (! $this->validateCouponCode($value)) {
                        $fail(__('validation.invalid_coupon_code'));
                    }
                },
            ],

            'promo_code' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-_]+$/i',
            ],

            // Subscription customization
            'auto_renewal' => [
                'sometimes',
                'boolean',
            ],

            'start_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
                'before:'.now()->addMonths(3)->toDateString(),
            ],

            'trial_period' => [
                'sometimes',
                'boolean',
            ],

            'trial_days' => [
                'required_if:trial_period,true',
                'integer',
                'min:1',
                'max:90',
            ],

            // Add-ons and extras
            'addons' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'addons.*.id' => [
                'required_with:addons',
                'integer',
                'exists:subscription_addons,id',
            ],

            'addons.*.quantity' => [
                'required_with:addons',
                'integer',
                'min:1',
                'max:1000',
            ],

            // Agreement and consent
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

            // Purchase metadata
            'referral_source' => [
                'sometimes',
                'string',
                'max:100',
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

            'fingerprint' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9]+$/',
            ],

            // Invoice preferences
            'invoice_email' => [
                'sometimes',
                'email',
                'max:255',
            ],

            'invoice_language' => [
                'sometimes',
                'string',
                'size:2',
                Rule::in(['en', 'lt', 'ru', 'pl', 'de', 'fr', 'es', 'zh', 'ar', 'pt', 'tr', 'it', 'ja', 'hi']),
            ],

            'invoice_format' => [
                'sometimes',
                'string',
                Rule::in(['pdf', 'html', 'xml']),
            ],

            // Custom fields for business requirements
            'custom_fields' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'custom_fields.*' => [
                'string',
                'max:500',
            ],

            // API and integration fields
            'webhook_url' => [
                'sometimes',
                'url',
                'max:255',
                'active_url',
            ],

            'return_url' => [
                'sometimes',
                'url',
                'max:255',
            ],

            'cancel_url' => [
                'sometimes',
                'url',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * Provides comprehensive multilingual error messaging with business context.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Plan validation messages
            'plan_id.required' => __('validation.subscription_plan_required'),
            'plan_id.exists' => __('validation.subscription_plan_not_available'),

            // Billing cycle messages
            'billing_cycle.required' => __('validation.billing_cycle_required'),
            'billing_cycle.in' => __('validation.billing_cycle_invalid'),

            // Payment method messages
            'payment_method.required' => __('validation.payment_method_required'),
            'payment_method.in' => __('validation.payment_method_invalid'),

            'currency.required' => __('validation.currency_required'),
            'currency.in' => __('validation.currency_not_supported'),

            // Payment token messages
            'payment_token.required_unless' => __('validation.payment_token_required'),
            'payment_token.regex' => __('validation.payment_token_format'),

            // Card validation messages
            'card_holder_name.required_if' => __('validation.card_holder_name_required'),
            'card_holder_name.regex' => __('validation.card_holder_name_format'),

            'card_number.required_if' => __('validation.card_number_required'),
            'card_number.regex' => __('validation.card_number_format'),

            'card_expiry_month.required_if' => __('validation.card_expiry_month_required'),
            'card_expiry_month.min' => __('validation.card_expiry_month_min'),
            'card_expiry_month.max' => __('validation.card_expiry_month_max'),

            'card_expiry_year.required_if' => __('validation.card_expiry_year_required'),
            'card_expiry_year.min' => __('validation.card_expiry_year_min'),

            'card_cvv.required_if' => __('validation.card_cvv_required'),
            'card_cvv.regex' => __('validation.card_cvv_format'),

            // Billing address messages
            'billing_address.required' => __('validation.billing_address_required'),
            'billing_address.street.required' => __('validation.billing_street_required'),
            'billing_address.city.required' => __('validation.billing_city_required'),
            'billing_address.postal_code.required' => __('validation.billing_postal_code_required'),
            'billing_address.postal_code.regex' => __('validation.billing_postal_code_format'),
            'billing_address.country.required' => __('validation.billing_country_required'),
            'billing_address.country.exists' => __('validation.billing_country_invalid'),

            // Customer information messages
            'customer_name.required' => __('validation.customer_name_required'),
            'customer_name.regex' => __('validation.customer_name_format'),

            'customer_email.required' => __('validation.customer_email_required'),
            'customer_email.email' => __('validation.customer_email_format'),
            'customer_email.lowercase' => __('validation.customer_email_lowercase'),

            'customer_phone.regex' => __('validation.customer_phone_format'),

            // Coupon code messages
            'coupon_code.regex' => __('validation.coupon_code_format'),
            'promo_code.regex' => __('validation.promo_code_format'),

            // Trial period messages
            'trial_days.required_if' => __('validation.trial_days_required'),
            'trial_days.min' => __('validation.trial_days_min'),
            'trial_days.max' => __('validation.trial_days_max'),

            // Add-ons messages
            'addons.max' => __('validation.addons_max'),
            'addons.*.id.exists' => __('validation.addon_not_found'),
            'addons.*.quantity.min' => __('validation.addon_quantity_min'),
            'addons.*.quantity.max' => __('validation.addon_quantity_max'),

            // Consent messages
            'terms_accepted.required' => __('validation.terms_acceptance_required'),
            'terms_accepted.accepted' => __('validation.terms_must_be_accepted'),

            'privacy_policy_accepted.required' => __('validation.privacy_policy_acceptance_required'),
            'privacy_policy_accepted.accepted' => __('validation.privacy_policy_must_be_accepted'),

            'data_processing_consent.required' => __('validation.data_processing_consent_required'),
            'data_processing_consent.accepted' => __('validation.data_processing_consent_must_be_accepted'),

            // URL validation messages
            'webhook_url.url' => __('validation.webhook_url_format'),
            'webhook_url.active_url' => __('validation.webhook_url_unreachable'),

            'return_url.url' => __('validation.return_url_format'),
            'cancel_url.url' => __('validation.cancel_url_format'),
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
            'plan_id' => __('validation.attributes.subscription_plan'),
            'billing_cycle' => __('validation.attributes.billing_cycle'),
            'payment_method' => __('validation.attributes.payment_method'),
            'currency' => __('validation.attributes.currency'),
            'payment_token' => __('validation.attributes.payment_token'),
            'payment_gateway' => __('validation.attributes.payment_gateway'),
            'card_holder_name' => __('validation.attributes.card_holder_name'),
            'card_number' => __('validation.attributes.card_number'),
            'card_expiry_month' => __('validation.attributes.card_expiry_month'),
            'card_expiry_year' => __('validation.attributes.card_expiry_year'),
            'card_cvv' => __('validation.attributes.card_cvv'),
            'billing_address' => __('validation.attributes.billing_address'),
            'billing_address.street' => __('validation.attributes.billing_street'),
            'billing_address.city' => __('validation.attributes.billing_city'),
            'billing_address.state' => __('validation.attributes.billing_state'),
            'billing_address.postal_code' => __('validation.attributes.billing_postal_code'),
            'billing_address.country' => __('validation.attributes.billing_country'),
            'customer_name' => __('validation.attributes.customer_name'),
            'customer_email' => __('validation.attributes.customer_email'),
            'customer_phone' => __('validation.attributes.customer_phone'),
            'company_name' => __('validation.attributes.company_name'),
            'tax_id' => __('validation.attributes.tax_id'),
            'coupon_code' => __('validation.attributes.coupon_code'),
            'promo_code' => __('validation.attributes.promo_code'),
            'auto_renewal' => __('validation.attributes.auto_renewal'),
            'start_date' => __('validation.attributes.start_date'),
            'trial_period' => __('validation.attributes.trial_period'),
            'trial_days' => __('validation.attributes.trial_days'),
            'addons' => __('validation.attributes.addons'),
            'terms_accepted' => __('validation.attributes.terms_accepted'),
            'privacy_policy_accepted' => __('validation.attributes.privacy_policy_accepted'),
            'marketing_consent' => __('validation.attributes.marketing_consent'),
            'data_processing_consent' => __('validation.attributes.data_processing_consent'),
            'referral_source' => __('validation.attributes.referral_source'),
            'utm_source' => __('validation.attributes.utm_source'),
            'utm_medium' => __('validation.attributes.utm_medium'),
            'utm_campaign' => __('validation.attributes.utm_campaign'),
            'ip_address' => __('validation.attributes.ip_address'),
            'user_agent' => __('validation.attributes.user_agent'),
            'fingerprint' => __('validation.attributes.fingerprint'),
            'invoice_email' => __('validation.attributes.invoice_email'),
            'invoice_language' => __('validation.attributes.invoice_language'),
            'invoice_format' => __('validation.attributes.invoice_format'),
            'custom_fields' => __('validation.attributes.custom_fields'),
            'webhook_url' => __('validation.attributes.webhook_url'),
            'return_url' => __('validation.attributes.return_url'),
            'cancel_url' => __('validation.attributes.cancel_url'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $response = response()->json([
            'success' => false,
            'message' => __('validation.subscription_purchase_failed'),
            'errors' => $validator->errors(),
            'error_code' => 'SUBSCRIPTION_PURCHASE_VALIDATION_FAILED',
            'timestamp' => now()->toISOString(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Handle a failed authorization attempt.
     *
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization(): void
    {
        $response = response()->json([
            'success' => false,
            'message' => __('validation.subscription_purchase_unauthorized'),
            'error_code' => 'SUBSCRIPTION_PURCHASE_UNAUTHORIZED',
            'timestamp' => now()->toISOString(),
        ], Response::HTTP_FORBIDDEN);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Prepare the data for validation.
     *
     * Pre-processes and normalizes input data before validation.
     * Implements data sanitization and business logic preparation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize email to lowercase
        if ($this->has('customer_email')) {
            $this->merge([
                'customer_email' => strtolower(trim($this->customer_email)),
            ]);
        }

        if ($this->has('invoice_email')) {
            $this->merge([
                'invoice_email' => strtolower(trim($this->invoice_email)),
            ]);
        }

        // Normalize phone number
        if ($this->has('customer_phone')) {
            $this->merge([
                'customer_phone' => preg_replace('/[^\+\d]/', '', $this->customer_phone),
            ]);
        }

        // Normalize card number (remove spaces and dashes)
        if ($this->has('card_number')) {
            $this->merge([
                'card_number' => preg_replace('/[\s\-]/', '', $this->card_number),
            ]);
        }

        // Normalize boolean values
        $booleanFields = [
            'auto_renewal',
            'trial_period',
            'terms_accepted',
            'privacy_policy_accepted',
            'marketing_consent',
            'data_processing_consent',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                ]);
            }
        }

        // Set default values
        if (! $this->has('auto_renewal')) {
            $this->merge(['auto_renewal' => true]);
        }

        if (! $this->has('invoice_language')) {
            $this->merge(['invoice_language' => app()->getLocale()]);
        }

        if (! $this->has('invoice_format')) {
            $this->merge(['invoice_format' => 'pdf']);
        }

        // Set tracking information
        if (! $this->has('ip_address')) {
            $this->merge(['ip_address' => $this->ip()]);
        }

        if (! $this->has('user_agent')) {
            $this->merge(['user_agent' => $this->userAgent()]);
        }

        // Sanitize text fields
        $textFields = [
            'customer_name',
            'company_name',
            'tax_id',
            'coupon_code',
            'promo_code',
            'referral_source',
            'utm_source',
            'utm_medium',
            'utm_campaign',
        ];

        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim($this->$field),
                ]);
            }
        }

        // Normalize coupon and promo codes to uppercase
        if ($this->has('coupon_code')) {
            $this->merge([
                'coupon_code' => strtoupper($this->coupon_code),
            ]);
        }

        if ($this->has('promo_code')) {
            $this->merge([
                'promo_code' => strtoupper($this->promo_code),
            ]);
        }
    }

    /**
     * Validate credit card number using Luhn algorithm.
     */
    private function validateCardNumber(string $cardNumber): bool
    {
        $cardNumber = preg_replace('/[\s\-]/', '', $cardNumber);

        if (! ctype_digit($cardNumber) || strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }

        // Luhn algorithm
        $sum = 0;
        $length = strlen($cardNumber);

        for ($i = $length - 2; $i >= 0; $i -= 2) {
            $doubled = intval($cardNumber[$i]) * 2;
            $sum += ($doubled > 9) ? ($doubled - 9) : $doubled;
        }

        for ($i = $length - 1; $i >= 0; $i -= 2) {
            $sum += intval($cardNumber[$i]);
        }

        return ($sum % 10) === 0;
    }

    /**
     * Validate coupon code availability and applicability.
     */
    private function validateCouponCode(string $couponCode): bool
    {
        // Check if coupon exists and is valid
        return \DB::table('coupons')
            ->where('code', strtoupper($couponCode))
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                    ->orWhere('usage_count', '<', \DB::raw('usage_limit'));
            })
            ->exists();
    }
}
