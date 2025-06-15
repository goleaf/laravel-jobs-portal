<?php

namespace App\Http\Requests\Transaction;

use App\Rules\NoMaliciousContent;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Request validation for TransactionController::store.
 *
 * @enhanced by RequestValidationImprover
 */
class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Implement proper authorization logic based on user permissions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'transaction_id' => 'required|string|unique:transactions,transaction_id',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:stripe,paypal,razorpay,paystack,manual',
            'status' => 'required|in:pending,approved,denied,cancelled',
            'meta' => 'nullable|json',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'User is required',
            'subscription_plan_id.required' => 'Subscription plan is required',
            'transaction_id.required' => 'Transaction ID is required',
            'transaction_id.unique' => 'Transaction ID already exists',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'payment_type.required' => 'Payment type is required',
            'status.required' => 'Status is required',
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
            'user.first_name' => 'first name',
            'user.last_name' => 'last name',
            'user.email' => 'email address',
            'user.phone' => 'phone number',
            'job_title' => 'job title',
            'job_description' => 'job description',
            'job_expiry_date' => 'job expiry date',
            'salary_from' => 'minimum salary',
            'salary_to' => 'maximum salary',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Add custom validation logic here
            if ($this->has('salary_from') && $this->has('salary_to')) {
                if ($this->salary_from > $this->salary_to) {
                    $validator->errors()->add('salary_to', 'Maximum salary must be greater than minimum salary');
                }
            }

            // Check for malicious content in text fields
            foreach (['job_description', 'job_requirement', 'job_benefit'] as $field) {
                if ($this->has($field) && $this->{$field}) {
                    $rule = new NoMaliciousContent();
                    if (!$rule->passes($field, $this->{$field})) {
                        $validator->errors()->add($field, $rule->message());
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input data
        if ($this->has('job_title')) {
            $this->merge([
                'job_title' => strip_tags($this->job_title),
            ]);
        }

        if ($this->has('job_description')) {
            $this->merge([
                'job_description' => strip_tags($this->job_description, '<p><br><ul><ol><li><strong><em>'),
            ]);
        }
    }
}
