<?php

namespace App\Http\Requests\Enhanced;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class PlanManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = $this->getBaseRules();
        $rules = array_merge($rules, $this->getSubscriptionRules());
        $rules = array_merge($rules, $this->getPricingRules());
        $rules = array_merge($rules, $this->getBillingRules());
        $rules = array_merge($rules, $this->getUsageLimitRules());
        $rules = array_merge($rules, $this->getPromoCodeRules());
        $rules = array_merge($rules, $this->getUpgradeDowngradeRules());
        $rules = array_merge($rules, $this->getMetricsRules());
        $rules = array_merge($rules, $this->getAdvancedSubscriptionRules());
        $rules = array_merge($rules, $this->getCustomizationRules());

        return $rules;
    }

    private function getBaseRules(): array
    {
        return [
            // Basic Plan Information
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'plan_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-_]+$/'],
            'plan_slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9\-_]+$/', 'unique:plans,slug'],
            'plan_description' => ['nullable', 'string', 'max:2000'],
            'plan_short_description' => ['nullable', 'string', 'max:500'],
            'plan_type' => ['nullable', 'string', Rule::in(['basic', 'premium', 'enterprise', 'custom', 'trial', 'freemium'])],
            'plan_category' => ['nullable', 'string', Rule::in(['individual', 'business', 'corporate', 'startup', 'agency'])],
            'plan_status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'deprecated', 'coming_soon', 'archived'])],

            // Plan Lifecycle
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_popular' => ['nullable', 'boolean'],
            'is_recommended' => ['nullable', 'boolean'],
            'is_trial_available' => ['nullable', 'boolean'],
            'is_custom_pricing' => ['nullable', 'boolean'],
            'is_enterprise_only' => ['nullable', 'boolean'],
            'requires_approval' => ['nullable', 'boolean'],

            // Availability
            'available_from' => ['nullable', 'date', 'after_or_equal:today'],
            'available_until' => ['nullable', 'date', 'after:available_from'],
            'geographic_restrictions' => ['nullable', 'array'],
            'geographic_restrictions.*' => ['string', 'max:2', 'regex:/^[A-Z]{2}$/'],
            'timezone_support' => ['nullable', 'array'],
            'timezone_support.*' => ['string', 'timezone'],
        ];
    }

    private function getSubscriptionRules(): array
    {
        return [
            // Subscription Management
            'subscription_id' => ['nullable', 'integer', 'exists:subscriptions,id'],
            'subscription_status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'cancelled', 'suspended', 'pending', 'trial', 'expired', 'past_due'])],
            'subscription_type' => ['nullable', 'string', Rule::in(['monthly', 'quarterly', 'yearly', 'lifetime', 'pay_per_use', 'custom'])],
            'billing_cycle' => ['nullable', 'string', Rule::in(['monthly', 'quarterly', 'semi_annually', 'annually', 'biennial', 'triennial'])],
            'billing_interval' => ['nullable', 'integer', 'min:1', 'max:36'],
            'auto_renewal' => ['nullable', 'boolean'],
            'grace_period_days' => ['nullable', 'integer', 'min:0', 'max:30'],
            'trial_period_days' => ['nullable', 'integer', 'min:0', 'max:365'],

            // Subscription Dates
            'subscription_start_date' => ['nullable', 'date'],
            'subscription_end_date' => ['nullable', 'date', 'after:subscription_start_date'],
            'next_billing_date' => ['nullable', 'date', 'after_or_equal:today'],
            'trial_ends_at' => ['nullable', 'date', 'after_or_equal:today'],
            'cancelled_at' => ['nullable', 'date'],
            'suspended_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],

            // Cancellation Management
            'cancellation_reason' => ['nullable', 'string', Rule::in(['too_expensive', 'not_using', 'found_alternative', 'poor_service', 'business_closure', 'other'])],
            'cancellation_feedback' => ['nullable', 'string', 'max:2000'],
            'immediate_cancellation' => ['nullable', 'boolean'],
            'cancel_at_period_end' => ['nullable', 'boolean'],
            'refund_requested' => ['nullable', 'boolean'],
            'retention_offer_applied' => ['nullable', 'boolean'],
        ];
    }

    private function getPricingRules(): array
    {
        return [
            // Pricing Structure
            'base_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'setup_fee' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'monthly_price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'yearly_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'lifetime_price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'currency' => ['nullable', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'currency_symbol' => ['nullable', 'string', 'max:5'],

            // Pricing Tiers
            'pricing_tiers' => ['nullable', 'array', 'max:10'],
            'pricing_tiers.*.tier_name' => ['string', 'max:100'],
            'pricing_tiers.*.min_quantity' => ['integer', 'min:1'],
            'pricing_tiers.*.max_quantity' => ['integer', 'min:1'],
            'pricing_tiers.*.price_per_unit' => ['numeric', 'min:0'],
            'pricing_tiers.*.discount_percentage' => ['numeric', 'min:0', 'max:100'],

            // Dynamic Pricing
            'dynamic_pricing_enabled' => ['nullable', 'boolean'],
            'peak_season_multiplier' => ['nullable', 'numeric', 'min:0.5', 'max:5.0'],
            'off_season_discount' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'volume_discount_threshold' => ['nullable', 'integer', 'min:1'],
            'volume_discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:80'],
            'early_bird_discount' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'loyalty_discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:30'],

            // Tax Configuration
            'tax_inclusive' => ['nullable', 'boolean'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'tax_region' => ['nullable', 'string', 'max:100'],
            'vat_applicable' => ['nullable', 'boolean'],
            'reverse_charge_applicable' => ['nullable', 'boolean'],
        ];
    }

    private function getBillingRules(): array
    {
        return [
            // Billing Information
            'billing_method' => ['nullable', 'string', Rule::in(['credit_card', 'debit_card', 'paypal', 'bank_transfer', 'invoice', 'crypto', 'wallet'])],
            'payment_processor' => ['nullable', 'string', Rule::in(['stripe', 'paypal', 'square', 'braintree', 'razorpay', 'mollie'])],
            'invoice_prefix' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z0-9\-]+$/'],
            'invoice_number_sequence' => ['nullable', 'integer', 'min:1'],
            'payment_terms_days' => ['nullable', 'integer', 'min:0', 'max:180'],
            'late_payment_fee' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'late_payment_fee_type' => ['nullable', 'string', Rule::in(['fixed', 'percentage'])],

            // Billing Address
            'billing_address' => ['nullable', 'array'],
            'billing_address.company_name' => ['nullable', 'string', 'max:255'],
            'billing_address.street_address' => ['nullable', 'string', 'max:255'],
            'billing_address.city' => ['nullable', 'string', 'max:100'],
            'billing_address.state' => ['nullable', 'string', 'max:100'],
            'billing_address.postal_code' => ['nullable', 'string', 'max:20'],
            'billing_address.country' => ['nullable', 'string', 'size:2'],
            'billing_address.tax_id' => ['nullable', 'string', 'max:50'],

            // Payment Configuration
            'dunning_management' => ['nullable', 'boolean'],
            'retry_failed_payments' => ['nullable', 'boolean'],
            'max_retry_attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
            'retry_interval_days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'smart_retry_enabled' => ['nullable', 'boolean'],
            'payment_reminder_days' => ['nullable', 'array'],
            'payment_reminder_days.*' => ['integer', 'min:1', 'max:30'],
        ];
    }

    private function getUsageLimitRules(): array
    {
        return [
            // Usage Limits and Quotas
            'job_posting_limit' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'application_limit' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'search_limit_daily' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'search_limit_monthly' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'profile_views_limit' => ['nullable', 'integer', 'min:0', 'max:50000'],
            'message_limit_daily' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'api_calls_limit' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'storage_limit_gb' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'bandwidth_limit_gb' => ['nullable', 'numeric', 'min:0', 'max:10000'],

            // Feature Access Control
            'features_included' => ['nullable', 'array'],
            'features_included.*' => ['string', 'max:100'],
            'advanced_search_enabled' => ['nullable', 'boolean'],
            'analytics_access' => ['nullable', 'boolean'],
            'api_access_enabled' => ['nullable', 'boolean'],
            'priority_support' => ['nullable', 'boolean'],
            'custom_branding' => ['nullable', 'boolean'],
            'white_label_enabled' => ['nullable', 'boolean'],
            'multi_user_support' => ['nullable', 'boolean'],
            'team_collaboration' => ['nullable', 'boolean'],
            'advanced_reporting' => ['nullable', 'boolean'],

            // Usage Tracking
            'usage_tracking_enabled' => ['nullable', 'boolean'],
            'overage_billing_enabled' => ['nullable', 'boolean'],
            'overage_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'usage_alert_thresholds' => ['nullable', 'array'],
            'usage_alert_thresholds.*' => ['integer', 'min:10', 'max:100'],
            'soft_limit_enforcement' => ['nullable', 'boolean'],
            'hard_limit_enforcement' => ['nullable', 'boolean'],
        ];
    }

    private function getPromoCodeRules(): array
    {
        return [
            // Promotional Codes
            'promo_code' => ['nullable', 'string', 'max:50', 'regex:/^[A-Z0-9\-_]+$/'],
            'promo_code_type' => ['nullable', 'string', Rule::in(['percentage', 'fixed_amount', 'free_trial', 'upgrade'])],
            'promo_discount_value' => ['nullable', 'numeric', 'min:0'],
            'promo_max_discount' => ['nullable', 'numeric', 'min:0'],
            'promo_valid_from' => ['nullable', 'date'],
            'promo_valid_until' => ['nullable', 'date', 'after:promo_valid_from'],
            'promo_usage_limit' => ['nullable', 'integer', 'min:1'],
            'promo_per_user_limit' => ['nullable', 'integer', 'min:1'],
            'promo_minimum_spend' => ['nullable', 'numeric', 'min:0'],
            'promo_first_time_only' => ['nullable', 'boolean'],
            'promo_stackable' => ['nullable', 'boolean'],

            // Referral System
            'referral_program_enabled' => ['nullable', 'boolean'],
            'referral_bonus_type' => ['nullable', 'string', Rule::in(['credit', 'discount', 'free_period', 'cash'])],
            'referral_bonus_amount' => ['nullable', 'numeric', 'min:0'],
            'referral_minimum_spend' => ['nullable', 'numeric', 'min:0'],
            'referral_expiry_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'multi_tier_referral' => ['nullable', 'boolean'],
            'referral_tier_bonuses' => ['nullable', 'array', 'max:5'],
            'referral_tier_bonuses.*.tier' => ['integer', 'min:1', 'max:5'],
            'referral_tier_bonuses.*.bonus_amount' => ['numeric', 'min:0'],
        ];
    }

    private function getUpgradeDowngradeRules(): array
    {
        return [
            // Plan Changes
            'target_plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'change_type' => ['nullable', 'string', Rule::in(['upgrade', 'downgrade', 'sidegrade', 'addon', 'removal'])],
            'change_effective_date' => ['nullable', 'date'],
            'proration_enabled' => ['nullable', 'boolean'],
            'proration_method' => ['nullable', 'string', Rule::in(['immediate', 'next_cycle', 'custom_date'])],
            'preserve_usage_data' => ['nullable', 'boolean'],
            'migration_required' => ['nullable', 'boolean'],
            'data_migration_plan' => ['nullable', 'string', 'max:1000'],

            // Change Impact Analysis
            'feature_impact_analysis' => ['nullable', 'array'],
            'feature_impact_analysis.removed_features' => ['nullable', 'array'],
            'feature_impact_analysis.added_features' => ['nullable', 'array'],
            'feature_impact_analysis.modified_limits' => ['nullable', 'array'],
            'user_notification_required' => ['nullable', 'boolean'],
            'admin_approval_required' => ['nullable', 'boolean'],
            'contract_amendment_needed' => ['nullable', 'boolean'],

            // Grandfathering Rules
            'grandfathered_pricing' => ['nullable', 'boolean'],
            'grandfathered_features' => ['nullable', 'array'],
            'grandfathered_until' => ['nullable', 'date'],
            'force_migration_date' => ['nullable', 'date'],
            'legacy_support_duration' => ['nullable', 'integer', 'min:0', 'max:1460'], // days
        ];
    }

    private function getMetricsRules(): array
    {
        return [
            // Performance Metrics
            'metrics_tracking' => ['nullable', 'boolean'],
            'revenue_tracking' => ['nullable', 'boolean'],
            'churn_analysis' => ['nullable', 'boolean'],
            'ltv_calculation' => ['nullable', 'boolean'],
            'cohort_analysis' => ['nullable', 'boolean'],
            'conversion_tracking' => ['nullable', 'boolean'],

            // KPI Thresholds
            'churn_rate_threshold' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'satisfaction_score_minimum' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'support_response_time_hours' => ['nullable', 'integer', 'min:1', 'max:168'],
            'uptime_guarantee_percentage' => ['nullable', 'numeric', 'min:90', 'max:100'],
            'performance_benchmark_ms' => ['nullable', 'integer', 'min:100', 'max:10000'],

            // Reporting Configuration
            'automated_reports' => ['nullable', 'boolean'],
            'report_frequency' => ['nullable', 'string', Rule::in(['daily', 'weekly', 'monthly', 'quarterly'])],
            'stakeholder_reports' => ['nullable', 'array'],
            'stakeholder_reports.*.recipient_email' => ['email', 'max:255'],
            'stakeholder_reports.*.report_types' => ['array'],
            'custom_metrics' => ['nullable', 'array', 'max:20'],
            'custom_metrics.*.metric_name' => ['string', 'max:100'],
            'custom_metrics.*.metric_formula' => ['string', 'max:500'],
        ];
    }

    private function getAdvancedSubscriptionRules(): array
    {
        return [
            // Multi-Tenant Support
            'tenant_isolation' => ['nullable', 'boolean'],
            'shared_resources' => ['nullable', 'boolean'],
            'tenant_customization' => ['nullable', 'boolean'],
            'cross_tenant_billing' => ['nullable', 'boolean'],
            'tenant_admin_access' => ['nullable', 'boolean'],

            // Enterprise Features
            'sso_integration' => ['nullable', 'boolean'],
            'ldap_integration' => ['nullable', 'boolean'],
            'saml_support' => ['nullable', 'boolean'],
            'audit_logging' => ['nullable', 'boolean'],
            'compliance_reporting' => ['nullable', 'boolean'],
            'data_residency_requirements' => ['nullable', 'array'],
            'security_certifications' => ['nullable', 'array'],

            // API & Integration Limits
            'webhook_endpoints_limit' => ['nullable', 'integer', 'min:0', 'max:100'],
            'api_rate_limit_per_minute' => ['nullable', 'integer', 'min:10', 'max:10000'],
            'concurrent_connections_limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'data_export_frequency' => ['nullable', 'string', Rule::in(['real_time', 'hourly', 'daily', 'weekly'])],
            'third_party_integrations' => ['nullable', 'array'],
            'custom_api_endpoints' => ['nullable', 'boolean'],

            // Advanced Analytics
            'predictive_analytics' => ['nullable', 'boolean'],
            'machine_learning_features' => ['nullable', 'boolean'],
            'ai_recommendations' => ['nullable', 'boolean'],
            'advanced_segmentation' => ['nullable', 'boolean'],
            'behavioral_analytics' => ['nullable', 'boolean'],
            'real_time_insights' => ['nullable', 'boolean'],
        ];
    }

    private function getCustomizationRules(): array
    {
        return [
            // UI/UX Customization
            'custom_branding_enabled' => ['nullable', 'boolean'],
            'logo_upload_allowed' => ['nullable', 'boolean'],
            'color_scheme_customization' => ['nullable', 'boolean'],
            'custom_css_allowed' => ['nullable', 'boolean'],
            'custom_domain_supported' => ['nullable', 'boolean'],
            'subdomain_customization' => ['nullable', 'boolean'],

            // Workflow Customization
            'custom_workflows' => ['nullable', 'boolean'],
            'workflow_automation' => ['nullable', 'boolean'],
            'custom_fields_limit' => ['nullable', 'integer', 'min:0', 'max:100'],
            'custom_forms_limit' => ['nullable', 'integer', 'min:0', 'max:50'],
            'business_rules_engine' => ['nullable', 'boolean'],
            'conditional_logic_support' => ['nullable', 'boolean'],

            // Content Management
            'custom_content_types' => ['nullable', 'boolean'],
            'content_approval_workflow' => ['nullable', 'boolean'],
            'multilingual_support' => ['nullable', 'boolean'],
            'content_versioning' => ['nullable', 'boolean'],
            'content_scheduling' => ['nullable', 'boolean'],
            'content_collaboration' => ['nullable', 'boolean'],

            // Communication Customization
            'custom_email_templates' => ['nullable', 'boolean'],
            'email_template_limit' => ['nullable', 'integer', 'min:0', 'max:100'],
            'notification_customization' => ['nullable', 'boolean'],
            'communication_channels' => ['nullable', 'array'],
            'automated_messaging' => ['nullable', 'boolean'],
            'chatbot_integration' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // Plan Management Messages
            'plan_name.required' => __('validation.plan_management.plan_name_required'),
            'plan_name.string' => __('validation.plan_management.plan_name_string'),
            'plan_name.max' => __('validation.plan_management.plan_name_max'),
            'plan_slug.unique' => __('validation.plan_management.plan_slug_unique'),
            'plan_type.in' => __('validation.plan_management.plan_type_invalid'),

            // Subscription Messages
            'subscription_status.in' => __('validation.plan_management.subscription_status_invalid'),
            'billing_cycle.in' => __('validation.plan_management.billing_cycle_invalid'),
            'next_billing_date.after_or_equal' => __('validation.plan_management.next_billing_date_future'),
            'subscription_end_date.after' => __('validation.plan_management.end_date_after_start'),

            // Pricing Messages
            'base_price.numeric' => __('validation.plan_management.base_price_numeric'),
            'base_price.min' => __('validation.plan_management.base_price_minimum'),
            'currency.size' => __('validation.plan_management.currency_code_length'),
            'tax_rate.max' => __('validation.plan_management.tax_rate_maximum'),

            // Usage Limit Messages
            'job_posting_limit.integer' => __('validation.plan_management.job_posting_limit_integer'),
            'api_calls_limit.max' => __('validation.plan_management.api_calls_limit_maximum'),
            'storage_limit_gb.numeric' => __('validation.plan_management.storage_limit_numeric'),

            // Promo Code Messages
            'promo_code.regex' => __('validation.plan_management.promo_code_format'),
            'promo_valid_until.after' => __('validation.plan_management.promo_end_after_start'),
            'promo_usage_limit.min' => __('validation.plan_management.promo_usage_minimum'),

            // Advanced Feature Messages
            'webhook_endpoints_limit.max' => __('validation.plan_management.webhook_limit_exceeded'),
            'api_rate_limit_per_minute.min' => __('validation.plan_management.api_rate_limit_minimum'),
            'custom_fields_limit.max' => __('validation.plan_management.custom_fields_maximum'),
        ];
    }

    protected function passedValidation(): void
    {
        // Enhanced security and performance optimizations
        $this->validateBusinessRules();
        $this->optimizePerformance();
        $this->logPlanActivity();
    }

    private function validateBusinessRules(): void
    {
        // Validate pricing consistency
        if ($this->has(['monthly_price', 'yearly_price'])) {
            $monthlyAnnual = $this->monthly_price * 12;
            $yearlyPrice = $this->yearly_price;

            if ($yearlyPrice > $monthlyAnnual) {
                throw new \InvalidArgumentException(__('validation.plan_management.yearly_price_invalid'));
            }
        }

        // Validate usage limits consistency
        if ($this->has(['search_limit_daily', 'search_limit_monthly'])) {
            $dailyMonthly = $this->search_limit_daily * 31;

            if ($this->search_limit_monthly > $dailyMonthly) {
                throw new \InvalidArgumentException(__('validation.plan_management.monthly_limit_exceeds_daily'));
            }
        }

        // Validate subscription dates
        if ($this->has(['subscription_start_date', 'trial_ends_at'])) {
            $startDate = Carbon::parse($this->subscription_start_date);
            $trialEnd = Carbon::parse($this->trial_ends_at);

            if ($trialEnd->lt($startDate)) {
                throw new \InvalidArgumentException(__('validation.plan_management.trial_end_before_start'));
            }
        }
    }

    private function optimizePerformance(): void
    {
        // Cache frequently accessed plan data
        if ($this->has('plan_id')) {
            Cache::remember("plan_data_{$this->plan_id}", 3600, function () {
                return $this->validated();
            });
        }

        // Pre-calculate pricing tiers for performance
        if ($this->has('pricing_tiers')) {
            $this->merge([
                'calculated_pricing_matrix' => $this->calculatePricingMatrix(),
            ]);
        }
    }

    private function calculatePricingMatrix(): array
    {
        $matrix = [];
        $tiers = $this->pricing_tiers ?? [];

        foreach ($tiers as $tier) {
            $matrix[] = [
                'tier_name' => $tier['tier_name'],
                'range' => "{$tier['min_quantity']}-{$tier['max_quantity']}",
                'effective_price' => $tier['price_per_unit'] * (1 - ($tier['discount_percentage'] / 100)),
                'savings' => $tier['discount_percentage'],
            ];
        }

        return $matrix;
    }

    private function logPlanActivity(): void
    {
        // Comprehensive audit logging for plan management
        if ($this->has('plan_id')) {
            \Log::info('Plan Management Request', [
                'plan_id' => $this->plan_id,
                'action_type' => $this->getActionType(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'timestamp' => now(),
                'request_data' => $this->except(['password', 'token']),
            ]);
        }
    }

    private function getActionType(): string
    {
        if ($this->has('subscription_status')) {
            return 'subscription_management';
        }
        if ($this->has('target_plan_id')) {
            return 'plan_change';
        }
        if ($this->has('promo_code')) {
            return 'promotional_action';
        }
        if ($this->has('billing_method')) {
            return 'billing_update';
        }

        return 'general_plan_operation';
    }
}
