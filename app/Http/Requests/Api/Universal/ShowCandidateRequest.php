<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShowCandidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $candidate = $this->route('candidate');

        // Public profiles can be viewed by anyone
        if ($candidate && 'public' === $candidate->visibility) {
            return true;
        }

        // Private profiles require authentication and ownership or admin access
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->id === $candidate->user_id
               || auth()->user()->hasRole('admin')
               || auth()->user()->hasRole('employer');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'include' => 'sometimes|string|max:500',
            'fields' => 'sometimes|string|max:300',
            'with_stats' => 'sometimes|boolean',
            'with_applications' => 'sometimes|boolean',
            'with_reviews' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'include.string' => 'Include parameter must be a valid string.',
            'include.max' => 'Include parameter cannot exceed 500 characters.',
            'fields.string' => 'Fields parameter must be a valid string.',
            'fields.max' => 'Fields parameter cannot exceed 300 characters.',
            'with_stats.boolean' => 'Statistics flag must be true or false.',
            'with_applications.boolean' => 'Applications flag must be true or false.',
            'with_reviews.boolean' => 'Reviews flag must be true or false.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'include' => 'include relationships',
            'fields' => 'field selection',
            'with_stats' => 'include statistics',
            'with_applications' => 'include applications',
            'with_reviews' => 'include reviews',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate include relationships
            if ($this->has('include')) {
                $allowedIncludes = [
                    'user', 'skills', 'languages', 'educations', 'experiences',
                    'applications', 'resumes', 'reviews', 'country', 'state',
                    'city', 'careerLevel', 'industry', 'salaryCurrency',
                ];

                $includes = explode(',', $this->include);
                foreach ($includes as $include) {
                    $include = trim($include);
                    if (!in_array($include, $allowedIncludes)) {
                        $validator->errors()->add('include', "Invalid include relationship: {$include}");
                    }
                }
            }

            // Validate field selection
            if ($this->has('fields')) {
                $allowedFields = [
                    'id', 'first_name', 'last_name', 'email', 'phone', 'bio',
                    'location', 'career', 'salary', 'skills', 'availability',
                    'profile', 'social_links', 'statistics', 'timestamps',
                ];

                $fields = explode(',', $this->fields);
                foreach ($fields as $field) {
                    $field = trim($field);
                    if (!in_array($field, $allowedFields)) {
                        $validator->errors()->add('fields', "Invalid field selection: {$field}");
                    }
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
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert boolean strings
        foreach (['with_stats', 'with_applications', 'with_reviews'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }
}
