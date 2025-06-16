<?php

namespace App\Http\Requests\Financial;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PaymentSuccessRequest extends FormRequest
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
            // Transaction identification
            'transaction_id' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z0-9\-_]+$/',
                function ($attribute, $value, $fail) {
                    if (!$this->validateTransactionExists($value)) {
                        $fail(__('validation.transaction_not_found'));
                    }
                },
            ],

            'payment_id' => [
                'required',
                'string',
                'max:255',
            ],

            'external_transaction_id' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'gateway_reference' => [
                'sometimes',
                'string',
                'max:255',
            ],

            // Payment details
            'amount_paid' => [
                'required',
                'numeric',
                'min:0',
                'max:1000000',
                function ($attribute, $value, $fail) {
                    if (!$this->validateAmountMatches($value)) {
                        $fail(__('validation.amount_mismatch'));
                    }
                },
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
                'exists:currencies,code',
            ],

            'exchange_rate' => [
                'sometimes',
                'numeric',
                'min:0.0001',
                'max:10000',
            ],

            'amount_in_base_currency' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1000000',
            ],

            // Payment method details
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
                    'wire_transfer',
                    'ach',
                    'sepa',
                ]),
            ],

            'payment_processor' => [
                'required',
                'string',
                'max:100',
                Rule::in([
                    'stripe',
                    'paypal',
                    'square',
                    'braintree',
                    'authorize_net',
                    'worldpay',
                    'adyen',
                    'razorpay',
                    'payu',
                    'mollie',
                ]),
            ],

            // Card details (if applicable)
            'card_last_four' => [
                'sometimes',
                'string',
                'regex:/^[0-9]{4}$/',
            ],

            'card_brand' => [
                'sometimes',
                'string',
                Rule::in(['visa', 'mastercard', 'amex', 'discover', 'jcb', 'diners', 'unionpay']),
            ],

            'card_type' => [
                'sometimes',
                'string',
                Rule::in(['credit', 'debit', 'prepaid', 'unknown']),
            ],

            'card_country' => [
                'sometimes',
                'string',
                'size:2',
                'exists:countries,code',
            ],

            // Transaction status
            'status' => [
                'required',
                'string',
                Rule::in([
                    'completed',
                    'pending',
                    'processing',
                    'authorized',
                    'captured',
                    'settled',
                    'failed',
                    'cancelled',
                    'refunded',
                    'partially_refunded',
                ]),
            ],

            'gateway_status' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'authorization_code' => [
                'sometimes',
                'string',
                'max:50',
            ],

            // Timestamps
            'payment_date' => [
                'required',
                'date',
                'before_or_equal:now',
                'after:' . now()->subDays(30)->toDateString(),
            ],

            'processed_at' => [
                'sometimes',
                'date',
                'before_or_equal:now',
            ],

            'settled_at' => [
                'sometimes',
                'date',
                'before_or_equal:now',
                'after_or_equal:payment_date',
            ],

            // Fees and charges
            'processing_fee' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000',
            ],

            'gateway_fee' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000',
            ],

            'net_amount' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1000000',
            ],

            // Risk and fraud assessment
            'risk_score' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            'fraud_check_result' => [
                'sometimes',
                'string',
                Rule::in(['pass', 'fail', 'review', 'not_checked']),
            ],

            'avs_result' => [
                'sometimes',
                'string',
                'max:10',
            ],

            'cvv_result' => [
                'sometimes',
                'string',
                'max:10',
            ],

            // Customer information
            'customer_id' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'customer_email' => [
                'sometimes',
                'email',
                'max:255',
            ],

            'customer_ip' => [
                'sometimes',
                'ip',
            ],

            'customer_country' => [
                'sometimes',
                'string',
                'size:2',
                'exists:countries,code',
            ],

            // Subscription details (if applicable)
            'subscription_id' => [
                'sometimes',
                'integer',
                'exists:subscriptions,id',
            ],

            'plan_id' => [
                'sometimes',
                'integer',
                'exists:plans,id',
            ],

            'billing_cycle' => [
                'sometimes',
                'string',
                Rule::in(['monthly', 'quarterly', 'semi_annual', 'annual', 'biennial']),
            ],

            'subscription_start_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
            ],

            'subscription_end_date' => [
                'sometimes',
                'date',
                'after:subscription_start_date',
            ],

            // Invoice details
            'invoice_id' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'invoice_number' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'tax_amount' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100000',
            ],

            'discount_amount' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100000',
            ],

            // Webhook and callback data
            'webhook_signature' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'webhook_timestamp' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'callback_url' => [
                'sometimes',
                'url',
                'max:500',
            ],

            'return_url' => [
                'sometimes',
                'url',
                'max:500',
            ],

            // Metadata and additional information
            'metadata' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'metadata.*' => [
                'string',
                'max:500',
            ],

            'notes' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            'description' => [
                'sometimes',
                'string',
                'max:500',
            ],

            // Compliance and regulatory
            'pci_compliance' => [
                'sometimes',
                'boolean',
            ],

            'gdpr_consent' => [
                'sometimes',
                'boolean',
            ],

            'data_retention_period' => [
                'sometimes',
                'integer',
                'min:30',
                'max:2555', // 7 years in days
            ],

            // Reconciliation
            'reconciliation_id' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'batch_id' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'settlement_batch' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Refund information
            'refundable_amount' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1000000',
            ],

            'refund_policy' => [
                'sometimes',
                'string',
                Rule::in(['full', 'partial', 'none', 'time_limited']),
            ],

            'refund_deadline' => [
                'sometimes',
                'date',
                'after:payment_date',
            ],

            // Integration and tracking
            'source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'campaign_id' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'affiliate_id' => [
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

            // Notification preferences
            'send_receipt' => [
                'sometimes',
                'boolean',
            ],

            'receipt_email' => [
                'sometimes',
                'email',
                'max:255',
            ],

            'notification_sent' => [
                'sometimes',
                'boolean',
            ],

            // Verification flags
            'verified' => [
                'sometimes',
                'boolean',
            ],

            'verification_method' => [
                'sometimes',
                'string',
                Rule::in(['automatic', 'manual', 'webhook', 'api']),
            ],

            'verification_timestamp' => [
                'sometimes',
                'date',
                'before_or_equal:now',
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
            'transaction_id.required' => __('validation.required_field', ['field' => __('validation.attributes.transaction_id')]),
            'transaction_id.regex' => __('validation.invalid_transaction_format'),
            
            'payment_id.required' => __('validation.required_field', ['field' => __('validation.attributes.payment_id')]),
            'payment_id.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.payment_id'), 'max' => 255]),
            
            'amount_paid.required' => __('validation.required_field', ['field' => __('validation.attributes.amount_paid')]),
            'amount_paid.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.amount_paid')]),
            'amount_paid.max' => __('validation.max_value', ['attribute' => __('validation.attributes.amount_paid'), 'max' => 1000000]),
            
            'currency.required' => __('validation.required_field', ['field' => __('validation.attributes.currency')]),
            'currency.size' => __('validation.size', ['attribute' => __('validation.attributes.currency'), 'size' => 3]),
            'currency.exists' => __('validation.exists', ['attribute' => __('validation.attributes.currency')]),
            
            'payment_method.required' => __('validation.required_field', ['field' => __('validation.attributes.payment_method')]),
            'payment_method.in' => __('validation.invalid_payment_method'),
            
            'payment_processor.required' => __('validation.required_field', ['field' => __('validation.attributes.payment_processor')]),
            'payment_processor.in' => __('validation.invalid_payment_processor'),
            
            'card_last_four.regex' => __('validation.invalid_card_last_four'),
            'card_brand.in' => __('validation.invalid_card_brand'),
            'card_type.in' => __('validation.invalid_card_type'),
            'card_country.exists' => __('validation.exists', ['attribute' => __('validation.attributes.country')]),
            
            'status.required' => __('validation.required_field', ['field' => __('validation.attributes.status')]),
            'status.in' => __('validation.invalid_payment_status'),
            
            'payment_date.required' => __('validation.required_field', ['field' => __('validation.attributes.payment_date')]),
            'payment_date.before_or_equal' => __('validation.payment_date_future'),
            'payment_date.after' => __('validation.payment_date_too_old'),
            
            'processed_at.before_or_equal' => __('validation.processed_date_future'),
            'settled_at.after_or_equal' => __('validation.settled_after_payment'),
            
            'risk_score.min' => __('validation.min_value', ['attribute' => __('validation.attributes.risk_score'), 'min' => 0]),
            'risk_score.max' => __('validation.max_value', ['attribute' => __('validation.attributes.risk_score'), 'max' => 100]),
            
            'fraud_check_result.in' => __('validation.invalid_fraud_result'),
            
            'customer_email.email' => __('validation.email', ['attribute' => __('validation.attributes.customer_email')]),
            'customer_ip.ip' => __('validation.ip', ['attribute' => __('validation.attributes.customer_ip')]),
            'customer_country.exists' => __('validation.exists', ['attribute' => __('validation.attributes.country')]),
            
            'subscription_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.subscription')]),
            'plan_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.plan')]),
            
            'billing_cycle.in' => __('validation.invalid_billing_cycle'),
            
            'subscription_start_date.after_or_equal' => __('validation.subscription_start_future'),
            'subscription_end_date.after' => __('validation.subscription_end_after_start'),
            
            'callback_url.url' => __('validation.url', ['attribute' => __('validation.attributes.callback_url')]),
            'return_url.url' => __('validation.url', ['attribute' => __('validation.attributes.return_url')]),
            
            'metadata.array' => __('validation.array', ['attribute' => __('validation.attributes.metadata')]),
            'metadata.max' => __('validation.max_items', ['attribute' => __('validation.attributes.metadata'), 'max' => 50]),
            'metadata.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.metadata_value'), 'max' => 500]),
            
            'notes.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.notes'), 'max' => 1000]),
            'description.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.description'), 'max' => 500]),
            
            'data_retention_period.min' => __('validation.min_retention_period'),
            'data_retention_period.max' => __('validation.max_retention_period'),
            
            'refund_policy.in' => __('validation.invalid_refund_policy'),
            'refund_deadline.after' => __('validation.refund_deadline_after_payment'),
            
            'receipt_email.email' => __('validation.email', ['attribute' => __('validation.attributes.receipt_email')]),
            
            'verification_method.in' => __('validation.invalid_verification_method'),
            'verification_timestamp.before_or_equal' => __('validation.verification_timestamp_future'),
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
            'transaction_id' => __('validation.attributes.transaction_id'),
            'payment_id' => __('validation.attributes.payment_id'),
            'external_transaction_id' => __('validation.attributes.external_transaction_id'),
            'gateway_reference' => __('validation.attributes.gateway_reference'),
            'amount_paid' => __('validation.attributes.amount_paid'),
            'currency' => __('validation.attributes.currency'),
            'exchange_rate' => __('validation.attributes.exchange_rate'),
            'payment_method' => __('validation.attributes.payment_method'),
            'payment_processor' => __('validation.attributes.payment_processor'),
            'card_last_four' => __('validation.attributes.card_last_four'),
            'card_brand' => __('validation.attributes.card_brand'),
            'card_type' => __('validation.attributes.card_type'),
            'status' => __('validation.attributes.status'),
            'payment_date' => __('validation.attributes.payment_date'),
            'processed_at' => __('validation.attributes.processed_at'),
            'settled_at' => __('validation.attributes.settled_at'),
            'processing_fee' => __('validation.attributes.processing_fee'),
            'gateway_fee' => __('validation.attributes.gateway_fee'),
            'net_amount' => __('validation.attributes.net_amount'),
            'risk_score' => __('validation.attributes.risk_score'),
            'fraud_check_result' => __('validation.attributes.fraud_check_result'),
            'customer_email' => __('validation.attributes.customer_email'),
            'customer_ip' => __('validation.attributes.customer_ip'),
            'subscription_id' => __('validation.attributes.subscription_id'),
            'plan_id' => __('validation.attributes.plan_id'),
            'billing_cycle' => __('validation.attributes.billing_cycle'),
            'invoice_id' => __('validation.attributes.invoice_id'),
            'tax_amount' => __('validation.attributes.tax_amount'),
            'discount_amount' => __('validation.attributes.discount_amount'),
            'metadata' => __('validation.attributes.metadata'),
            'notes' => __('validation.attributes.notes'),
            'description' => __('validation.attributes.description'),
            'refund_policy' => __('validation.attributes.refund_policy'),
            'receipt_email' => __('validation.attributes.receipt_email'),
            'verification_method' => __('validation.attributes.verification_method'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'pci_compliance' => $this->boolean('pci_compliance', true),
            'gdpr_consent' => $this->boolean('gdpr_consent', false),
            'send_receipt' => $this->boolean('send_receipt', true),
            'notification_sent' => $this->boolean('notification_sent', false),
            'verified' => $this->boolean('verified', false),
            'verification_method' => $this->verification_method ?? 'automatic',
            'data_retention_period' => $this->integer('data_retention_period', 2555), // 7 years
        ]);

        // Calculate net amount if not provided
        if (!$this->has('net_amount') && $this->has('amount_paid')) {
            $netAmount = $this->amount_paid;
            $netAmount -= $this->processing_fee ?? 0;
            $netAmount -= $this->gateway_fee ?? 0;
            
            $this->merge([
                'net_amount' => round($netAmount, 2),
            ]);
        }

        // Set customer IP if not provided
        if (!$this->has('customer_ip')) {
            $this->merge([
                'customer_ip' => $this->ip(),
            ]);
        }

        // Process metadata array
        if ($this->has('metadata') && is_string($this->metadata)) {
            try {
                $metadata = json_decode($this->metadata, true);
                if (is_array($metadata)) {
                    $this->merge(['metadata' => $metadata]);
                }
            } catch (\Exception $e) {
                // Keep as string if JSON decode fails
            }
        }

        // Set verification timestamp if verified but no timestamp
        if ($this->verified && !$this->has('verification_timestamp')) {
            $this->merge([
                'verification_timestamp' => now()->toDateTimeString(),
            ]);
        }

        // Log payment success for monitoring
        Log::info('Payment success notification received', [
            'transaction_id' => $this->transaction_id,
            'payment_id' => $this->payment_id,
            'amount_paid' => $this->amount_paid,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'payment_processor' => $this->payment_processor,
            'status' => $this->status,
            'ip' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Set processing metadata
        $this->merge([
            'processed_request_id' => 'PAY-SUCCESS-' . date('Ymd') . '-' . strtoupper(substr(md5($this->transaction_id . time()), 0, 8)),
            'validated_at' => now(),
            'request_source' => $this->header('X-Request-Source', 'webhook'),
        ]);

        // Set processing flags
        $this->merge([
            'requires_manual_review' => $this->shouldRequireManualReview(),
            'auto_activate_subscription' => $this->shouldAutoActivateSubscription(),
            'send_confirmation' => $this->shouldSendConfirmation(),
            'update_customer_status' => true,
        ]);
    }

    /**
     * Validate if transaction exists in our system.
     */
    private function validateTransactionExists($transactionId): bool
    {
        $transaction = \DB::table('transactions')
            ->where('transaction_id', $transactionId)
            ->orWhere('external_id', $transactionId)
            ->first();

        return $transaction !== null;
    }

    /**
     * Validate if amount matches expected transaction amount.
     */
    private function validateAmountMatches($amount): bool
    {
        if (!$this->has('transaction_id')) {
            return true; // Will be caught by transaction_id validation
        }

        $transaction = \DB::table('transactions')
            ->where('transaction_id', $this->transaction_id)
            ->first();

        if (!$transaction) {
            return false;
        }

        // Allow for small variance due to currency conversion or fees
        $expectedAmount = $transaction->amount ?? 0;
        $variance = max(0.01, $expectedAmount * 0.001); // 0.1% or 1 cent minimum
        
        return abs($amount - $expectedAmount) <= $variance;
    }

    /**
     * Determine if manual review is required.
     */
    private function shouldRequireManualReview(): bool
    {
        return ($this->risk_score ?? 0) > 70 ||
               $this->fraud_check_result === 'review' ||
               $this->amount_paid > 10000 ||
               $this->status === 'pending';
    }

    /**
     * Determine if subscription should be auto-activated.
     */
    private function shouldAutoActivateSubscription(): bool
    {
        return $this->has('subscription_id') &&
               $this->status === 'completed' &&
               !$this->shouldRequireManualReview();
    }

    /**
     * Determine if confirmation should be sent.
     */
    private function shouldSendConfirmation(): bool
    {
        return $this->send_receipt &&
               $this->has('customer_email') &&
               in_array($this->status, ['completed', 'settled']);
    }
}
 