<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowApplyJobFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'jobId' => 'required|integer|exists:jobs,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jobId.required' => __('validation.required', ['attribute' => 'job ID']),
            'jobId.integer' => __('validation.integer', ['attribute' => 'job ID']),
            'jobId.exists' => __('validation.exists', ['attribute' => 'job ID']),
        ];
    }
}
