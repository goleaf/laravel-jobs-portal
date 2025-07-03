<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DestroyCandidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $candidate = $this->route('candidate');

        if (! $candidate) {
            return false;
        }

        // Admin can delete any candidate
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        // Users can only delete their own candidate profile
        return auth()->user()->id === $candidate->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'reason' => 'sometimes|string|max:500',
            'force_delete' => 'sometimes|boolean',
            'cleanup_data' => 'sometimes|boolean',
            'preserve_applications' => 'sometimes|boolean',
            'preserve_reviews' => 'sometimes|boolean',
            'notify_employers' => 'sometimes|boolean',
            'confirmation' => 'required|boolean|accepted',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'reason.max' => 'Deletion reason cannot exceed 500 characters.',
            'force_delete.boolean' => 'Force delete flag must be true or false.',
            'cleanup_data.boolean' => 'Cleanup data flag must be true or false.',
            'preserve_applications.boolean' => 'Preserve applications flag must be true or false.',
            'preserve_reviews.boolean' => 'Preserve reviews flag must be true or false.',
            'notify_employers.boolean' => 'Notify employers flag must be true or false.',
            'confirmation.required' => 'Deletion confirmation is required.',
            'confirmation.accepted' => 'You must confirm the deletion by setting confirmation to true.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'reason' => 'deletion reason',
            'force_delete' => 'force delete',
            'cleanup_data' => 'cleanup data',
            'preserve_applications' => 'preserve applications',
            'preserve_reviews' => 'preserve reviews',
            'notify_employers' => 'notify employers',
            'confirmation' => 'deletion confirmation',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $candidate = $this->route('candidate');

            if (! $candidate) {
                $validator->errors()->add('candidate', 'Candidate not found.');

                return;
            }

            // Check for active applications if preserve_applications is false
            if (! $this->preserve_applications) {
                $activeApplications = $candidate->jobApplications()->whereIn('status', ['pending', 'interviewing', 'shortlisted'])->count();
                if ($activeApplications > 0) {
                    $validator->errors()->add('preserve_applications', "Cannot delete applications while {$activeApplications} are still active. Set preserve_applications to true.");
                }
            }

            // Force delete requires admin role
            if ($this->force_delete && ! auth()->user()->hasRole('admin')) {
                $validator->errors()->add('force_delete', 'Only administrators can perform force deletion.');
            }

            // Validate reason is required for force delete
            if ($this->force_delete && ! $this->filled('reason')) {
                $validator->errors()->add('reason', 'Deletion reason is required for force deletion.');
            }

            // Check for dependencies that cannot be preserved
            if ($this->cleanup_data) {
                $hasLinkedData = $this->checkLinkedData($candidate);
                if ($hasLinkedData && ! $this->force_delete) {
                    $validator->errors()->add('force_delete', 'Candidate has linked data that requires force deletion or data preservation.');
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Deletion validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $defaults = [
            'force_delete' => false,
            'cleanup_data' => true,
            'preserve_applications' => true,
            'preserve_reviews' => true,
            'notify_employers' => false,
        ];

        foreach ($defaults as $key => $default) {
            if (! $this->has($key)) {
                $this->merge([$key => $default]);
            }
        }

        // Convert boolean strings
        foreach (array_keys($defaults) as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }

        // Convert confirmation to boolean
        if ($this->has('confirmation')) {
            $this->merge([
                'confirmation' => filter_var($this->confirmation, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        // Trim reason if provided
        if ($this->has('reason')) {
            $this->merge([
                'reason' => trim($this->reason),
            ]);
        }
    }

    /**
     * Check for linked data that might prevent deletion.
     *
     * @param  mixed  $candidate
     */
    private function checkLinkedData($candidate): bool
    {
        // Check for various data dependencies
        $dependencies = [
            'resumes' => $candidate->resumes()->count(),
            'educations' => $candidate->candidateEducations()->count(),
            'experiences' => $candidate->candidateExperiences()->count(),
            'skills' => $candidate->candidateSkills()->count(),
            'languages' => $candidate->candidateLanguages()->count(),
        ];

        return collect($dependencies)->sum() > 0;
    }
}
