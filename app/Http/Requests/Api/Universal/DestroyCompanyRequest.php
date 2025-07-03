<?php

namespace App\Http\Requests\Api\Universal;

use App\Models\Company;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DestroyCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user can delete companies
        // This should be restricted to admins or company owners
        return $this->user() && (
            $this->user()->hasRole('admin')
            || $this->user()->hasRole('employer') && $this->userOwnsCompany()
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'confirm' => 'required|boolean|accepted',
            'reason' => 'sometimes|string|max:500',
            'transfer_data_to' => 'sometimes|integer|exists:companies,id',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'confirm.required' => __('validation.required', ['attribute' => __('validation.attributes.confirm')]),
            'confirm.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.confirm')]),
            'confirm.accepted' => __('validation.accepted', ['attribute' => __('validation.attributes.confirm')]),
            'reason.string' => __('validation.string', ['attribute' => __('validation.attributes.reason')]),
            'reason.max' => __('validation.max.string', ['attribute' => __('validation.attributes.reason'), 'max' => 500]),
            'transfer_data_to.integer' => __('validation.integer', ['attribute' => __('validation.attributes.transfer_data_to')]),
            'transfer_data_to.exists' => __('validation.exists', ['attribute' => __('validation.attributes.transfer_data_to')]),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'confirm' => __('validation.attributes.confirm'),
            'reason' => __('validation.attributes.reason'),
            'transfer_data_to' => __('validation.attributes.transfer_data_to'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check if company has active jobs
            $companyId = $this->route('id');
            $company = Company::find($companyId);

            if ($company && $company->jobs()->where('status', 'active')->count() > 0) {
                if (! $this->has('transfer_data_to')) {
                    $validator->errors()->add('transfer_data_to', __('validation.required_when_active_jobs'));
                }
            }

            // Check if user owns the company (for non-admins)
            if ($this->user() && ! $this->user()->hasRole('admin')) {
                if (! $this->userOwnsCompany()) {
                    $validator->errors()->add('authorization', __('auth.forbidden'));
                }
            }

            // Validate transfer company is different
            if ($this->has('transfer_data_to') && $this->transfer_data_to == $companyId) {
                $validator->errors()->add('transfer_data_to', __('validation.different', ['attribute' => __('validation.attributes.transfer_data_to'), 'other' => __('validation.attributes.company')]));
            }
        });
    }

    /**
     * Handle a failed validation attempt for API requests.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('validation.failed'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('auth.forbidden'),
                'errors' => ['authorization' => [__('auth.forbidden')]],
            ], 403)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert confirm to boolean if provided
        if ($this->has('confirm')) {
            $this->merge([
                'confirm' => filter_var($this->confirm, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        // Clean reason if provided
        if ($this->has('reason')) {
            $this->merge([
                'reason' => trim($this->input('reason')),
            ]);
        }
    }

    /**
     * Check if the authenticated user owns the company.
     */
    private function userOwnsCompany(): bool
    {
        $companyId = $this->route('id');
        $user = $this->user();

        if (! $user || ! $companyId) {
            return false;
        }

        // Check if user has a company relationship
        return $user->companies()->where('companies.id', $companyId)->exists()
               || $user->company_id == $companyId;
    }
}
