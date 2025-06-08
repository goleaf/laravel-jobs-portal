<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for CreateJobShiftRequest
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Following proven MasterData pattern
 */
class CreateJobShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        return $user && (
            $user->hasRole('Admin') || 
            $user->hasRole('Employer')
        );
    }

    public function rules(): array
    {
        return [
            'shift' => ['required', 'string', 'max:255', 'unique:job_shifts,shift'],
            'description' => ['nullable', 'string', 'max:500'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'shift.required' => __('validation.jobshift_shift_required'),
            'shift.max' => __('validation.jobshift_shift_max'),
            'shift.unique' => __('validation.jobshift_shift_unique'),
            'description.max' => __('validation.jobshift_description_max'),
            'start_time.date_format' => __('validation.jobshift_start_time_format'),
            'end_time.date_format' => __('validation.jobshift_end_time_format'),
            'end_time.after' => __('validation.jobshift_end_time_after'),
            'is_active.boolean' => __('validation.jobshift_is_active_boolean'),
        ];
    }

    public function attributes(): array
    {
        return [
            'shift' => __('validation.attributes.jobshift_shift'),
            'description' => __('validation.attributes.jobshift_description'),
            'start_time' => __('validation.attributes.jobshift_start_time'),
            'end_time' => __('validation.attributes.jobshift_end_time'),
            'is_active' => __('validation.attributes.jobshift_is_active'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default active status
        if (!$this->has('is_active')) {
            $this->merge([
                'is_active' => true,
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => __('validation.failed'),
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
} 