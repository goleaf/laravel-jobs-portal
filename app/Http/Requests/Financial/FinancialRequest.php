<?php

namespace App\Http\Requests\Financial;

use App\Http\Requests\Foundation\AbstractBaseRequest;
use App\Http\Requests\Foundation\Traits\AuditLoggingTrait;
use App\Http\Requests\Foundation\Traits\MultilingualValidationTrait;
use App\Http\Requests\Foundation\Traits\PerformanceOptimizationTrait;
use App\Http\Requests\Foundation\Traits\SecurityValidationTrait;

/**
 * Financial Request - Base class for financial validation
 *
 * Handles validation for:
 * - Payment processing and transactions
 * - Subscription and billing management
 * - Currency and financial calculations
 * - Compliance and security requirements
 * - Financial audit trails
 *
 * @version 1.0.0
 *
 * @since 2024-12-28
 */
abstract class FinancialRequest extends AbstractBaseRequest
{
    use AuditLoggingTrait;
    use MultilingualValidationTrait;
    use PerformanceOptimizationTrait;
    use SecurityValidationTrait;

    /**
     * Security level for financial operations (always critical)
     */
    protected string $securityLevel = 'critical';

    /**
     * Enable performance monitoring for financial operations
     */
    protected bool $performanceTracking = true;

    /**
     * Enable audit logging for financial operations
     */
    protected bool $auditLoggingEnabled = true;

    /**
     * Financial validation modules
     */
    protected array $validationModules = [
        'payment_security',
        'currency_validation',
        'amount_verification',
        'compliance_check',
    ];

    /**
     * Get domain-specific validation rules for financial data
     */
    protected function getDomainRules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'currency' => ['required', 'string', 'size:3', 'exists:currencies,code'],
            'payment_method' => ['required', 'string', 'in:card,bank_transfer,paypal,stripe'],
            'transaction_type' => ['required', 'string', 'in:payment,refund,subscription,cancellation'],
            'status' => ['sometimes', 'required', 'in:pending,completed,failed,cancelled,refunded'],
        ];
    }

    /**
     * Get domain-specific error messages for financial data
     */
    protected function getDomainMessages(): array
    {
        return [
            'amount.required' => __('validation.financial.amount_required'),
            'amount.numeric' => __('validation.financial.amount_numeric'),
            'amount.min' => __('validation.financial.amount_min'),
            'amount.max' => __('validation.financial.amount_max'),
            'currency.required' => __('validation.financial.currency_required'),
            'currency.size' => __('validation.financial.currency_format'),
            'currency.exists' => __('validation.financial.currency_invalid'),
            'payment_method.required' => __('validation.financial.payment_method_required'),
            'payment_method.in' => __('validation.financial.payment_method_invalid'),
            'transaction_type.required' => __('validation.financial.transaction_type_required'),
            'transaction_type.in' => __('validation.financial.transaction_type_invalid'),
            'status.in' => __('validation.financial.status_invalid'),
        ];
    }

    /**
     * Get domain-specific attribute names for financial data
     */
    protected function getDomainAttributes(): array
    {
        return [
            'amount' => __('validation.attributes.amount'),
            'currency' => __('validation.attributes.currency'),
            'payment_method' => __('validation.attributes.payment_method'),
            'transaction_type' => __('validation.attributes.transaction_type'),
            'status' => __('validation.attributes.status'),
            'transaction_id' => __('validation.attributes.transaction_id'),
            'subscription_id' => __('validation.attributes.subscription_id'),
            'plan_id' => __('validation.attributes.plan_id'),
        ];
    }

    /**
     * Common validation rules for payment data
     */
    protected function getPaymentRules(): array
    {
        return [
            'card_number' => ['sometimes', 'required', 'string', 'regex:/^\d{13,19}$/'],
            'card_holder_name' => ['sometimes', 'required', 'string', 'max:255'],
            'expiry_month' => ['sometimes', 'required', 'integer', 'between:1,12'],
            'expiry_year' => ['sometimes', 'required', 'integer', 'min:'.date('Y')],
            'cvv' => ['sometimes', 'required', 'string', 'regex:/^\d{3,4}$/'],
        ];
    }

    /**
     * Common validation rules for subscription data
     */
    protected function getSubscriptionRules(): array
    {
        return [
            'plan_id' => ['required', 'exists:plans,id'],
            'billing_cycle' => ['required', 'string', 'in:monthly,quarterly,yearly'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'auto_renewal' => ['required', 'boolean'],
        ];
    }

    /**
     * Common validation rules for currency operations
     */
    protected function getCurrencyRules(): array
    {
        return [
            'from_currency' => ['required', 'string', 'size:3', 'exists:currencies,code'],
            'to_currency' => ['required', 'string', 'size:3', 'exists:currencies,code'],
            'exchange_rate' => ['sometimes', 'numeric', 'min:0'],
            'conversion_date' => ['sometimes', 'date'],
        ];
    }

    /**
     * Common validation rules for billing address
     */
    protected function getBillingAddressRules(): array
    {
        return [
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_address' => ['required', 'string', 'max:500'],
            'billing_city' => ['required', 'string', 'max:100'],
            'billing_state' => ['required', 'string', 'max:100'],
            'billing_postal_code' => ['required', 'string', 'max:20'],
            'billing_country' => ['required', 'string', 'size:2', 'exists:countries,code'],
        ];
    }

    /**
     * Perform financial-specific validation
     */
    protected function performCustomValidation($validator): void
    {
        // Log financial validation attempt
        $this->logComplianceEvent('financial_validation_started', [
            'request_type' => static::class,
            'amount' => $this->input('amount'),
            'currency' => $this->input('currency'),
        ]);

        // Validate amount limits
        $this->validateAmountLimits($validator);

        // Validate currency compatibility
        $this->validateCurrencyCompatibility($validator);

        // Validate payment method security
        $this->validatePaymentSecurity($validator);

        // Validate compliance requirements
        $this->validateComplianceRequirements($validator);
    }

    /**
     * Validate amount limits based on transaction type
     */
    protected function validateAmountLimits($validator): void
    {
        $amount = $this->input('amount');
        $transactionType = $this->input('transaction_type');

        if ($amount && $transactionType) {
            $limits = $this->getAmountLimits($transactionType);

            if ($amount < $limits['min']) {
                $validator->errors()->add('amount', __('validation.financial.amount_below_minimum', ['min' => $limits['min']]));
            }

            if ($amount > $limits['max']) {
                $validator->errors()->add('amount', __('validation.financial.amount_above_maximum', ['max' => $limits['max']]));
            }
        }
    }

    /**
     * Validate currency compatibility
     */
    protected function validateCurrencyCompatibility($validator): void
    {
        $currency = $this->input('currency');
        $paymentMethod = $this->input('payment_method');

        if ($currency && $paymentMethod) {
            $supportedCurrencies = $this->getSupportedCurrencies($paymentMethod);

            if (! in_array($currency, $supportedCurrencies)) {
                $validator->errors()->add('currency', __('validation.financial.currency_not_supported_for_payment_method'));
            }
        }
    }

    /**
     * Validate payment method security
     */
    protected function validatePaymentSecurity($validator): void
    {
        // Log security validation
        $this->logSecurityEvent('payment_security_validation', [
            'payment_method' => $this->input('payment_method'),
            'security_level' => $this->getSecurityLevel(),
        ]);

        // Validate card data if present
        if ($this->has('card_number')) {
            $this->validateCardSecurity($validator);
        }
    }

    /**
     * Validate compliance requirements
     */
    protected function validateComplianceRequirements($validator): void
    {
        // PCI DSS compliance for card payments
        if ($this->input('payment_method') === 'card') {
            $this->validatePciCompliance($validator);
        }

        // Anti-money laundering checks
        $this->validateAmlRequirements($validator);

        // Tax compliance
        $this->validateTaxCompliance($validator);
    }

    /**
     * Validate card security (PCI DSS compliance)
     */
    protected function validateCardSecurity($validator): void
    {
        // Override in specific request classes
    }

    /**
     * Validate PCI compliance
     */
    protected function validatePciCompliance($validator): void
    {
        // Override in specific request classes
    }

    /**
     * Validate AML requirements
     */
    protected function validateAmlRequirements($validator): void
    {
        // Override in specific request classes
    }

    /**
     * Validate tax compliance
     */
    protected function validateTaxCompliance($validator): void
    {
        // Override in specific request classes
    }

    /**
     * Get amount limits for transaction type
     */
    protected function getAmountLimits(string $transactionType): array
    {
        return match ($transactionType) {
            'payment' => ['min' => 0.01, 'max' => 100000.00],
            'refund' => ['min' => 0.01, 'max' => 50000.00],
            'subscription' => ['min' => 1.00, 'max' => 10000.00],
            default => ['min' => 0.01, 'max' => 1000.00],
        };
    }

    /**
     * Get supported currencies for payment method
     */
    protected function getSupportedCurrencies(string $paymentMethod): array
    {
        return match ($paymentMethod) {
            'paypal' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
            'stripe' => ['USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF', 'NOK', 'SEK', 'DKK'],
            'bank_transfer' => ['USD', 'EUR', 'GBP'],
            default => ['USD', 'EUR'],
        };
    }

    /**
     * Apply financial-specific sanitization
     */
    protected function applySanitization(array $data): array
    {
        $sanitized = parent::applySanitization($data);

        // Sanitize sensitive financial data
        if (isset($sanitized['card_number'])) {
            $sanitized['card_number'] = preg_replace('/\D/', '', $sanitized['card_number']);
        }

        if (isset($sanitized['amount'])) {
            $sanitized['amount'] = round((float) $sanitized['amount'], 2);
        }

        if (isset($sanitized['currency'])) {
            $sanitized['currency'] = strtoupper($sanitized['currency']);
        }

        return $sanitized;
    }
}
