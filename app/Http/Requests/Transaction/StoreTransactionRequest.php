<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(['Admin', 'Employer']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'owner_id' => ['required', 'integer'],
            'owner_type' => ['required', 'string', 'in:App\\Models\\Plan,App\\Models\\FeaturedRecord'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_type' => ['required', 'integer', 'in:1,2,3,4'], // 1=Stripe, 2=PayPal, 3=Manual, 4=Paystack
            'status' => ['required', 'integer', 'in:0,1,2'], // 0=Pending, 1=Approved, 2=Denied
            'meta' => ['nullable', 'array'],
            'stripe_id' => ['nullable', 'string'],
            'stripe_status' => ['nullable', 'string'],
            'invoice_id' => ['nullable', 'string'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => __('validation.required', ['attribute' => __('common.user')]),
            'user_id.exists' => __('validation.exists', ['attribute' => __('common.user')]),
            'owner_id.required' => __('validation.required', ['attribute' => __('common.owner_id')]),
            'owner_id.integer' => __('validation.integer', ['attribute' => __('common.owner_id')]),
            'owner_type.required' => __('validation.required', ['attribute' => __('common.owner_type')]),
            'owner_type.in' => __('validation.in', ['attribute' => __('common.owner_type')]),
            'amount.required' => __('validation.required', ['attribute' => __('common.amount')]),
            'amount.numeric' => __('validation.numeric', ['attribute' => __('common.amount')]),
            'amount.min' => __('validation.min.numeric', ['attribute' => __('common.amount'), 'min' => 0]),
            'payment_type.required' => __('validation.required', ['attribute' => __('common.payment_type')]),
            'payment_type.in' => __('validation.in', ['attribute' => __('common.payment_type')]),
            'status.required' => __('validation.required', ['attribute' => __('common.status')]),
            'status.in' => __('validation.in', ['attribute' => __('common.status')]),
            'description.max' => __('validation.max.string', ['attribute' => __('common.description'), 'max' => 1000]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => __('common.user'),
            'owner_id' => __('common.owner_id'),
            'owner_type' => __('common.owner_type'),
            'amount' => __('common.amount'),
            'payment_type' => __('common.payment_type'),
            'status' => __('common.status'),
            'description' => __('common.description'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate owner_type and owner_id combination
            if ($this->filled('owner_type') && $this->filled('owner_id')) {
                $ownerType = $this->owner_type;
                $ownerId = $this->owner_id;
                
                // Check if the owner exists
                if (!class_exists($ownerType)) {
                    $validator->errors()->add('owner_type', __('validation.invalid_owner_type'));
                } else {
                    $model = new $ownerType();
                    if (!$model->find($ownerId)) {
                        $validator->errors()->add('owner_id', __('validation.invalid_owner_id'));
                    }
                }
            }

            // Validate payment type specific fields
            $paymentType = $this->payment_type;
            
            if ($paymentType == 1 && !$this->filled('stripe_id')) { // Stripe
                $validator->errors()->add('stripe_id', __('validation.required_for_stripe'));
            }
            
            if ($paymentType == 3 && !$this->filled('description')) { // Manual
                $validator->errors()->add('description', __('validation.required_for_manual_payment'));
            }
        });
    }
}
