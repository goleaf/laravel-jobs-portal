<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Requires authentication for creating resources
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Base validation rules that apply to most resource creation
        return [
            'name' => 'sometimes|required|string|max:255',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|string|max:2000',
            'status' => 'sometimes|string|in:active,inactive,draft,published,pending',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
            'metadata' => 'sometimes|array',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'notes' => 'sometimes|string|max:1000',
            'external_id' => 'sometimes|string|max:100',
            'reference' => 'sometimes|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required when provided.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'title.required' => 'Title is required when provided.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 2000 characters.',
            'status.in' => 'Status must be one of: active, inactive, draft, published, pending.',
            'is_active.boolean' => 'Active status must be true or false.',
            'is_featured.boolean' => 'Featured status must be true or false.',
            'is_verified.boolean' => 'Verified status must be true or false.',
            'sort_order.integer' => 'Sort order must be a valid integer.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'metadata.array' => 'Metadata must be provided as an array.',
            'tags.array' => 'Tags must be provided as an array.',
            'tags.*.max' => 'Each tag cannot exceed 50 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'external_id.max' => 'External ID cannot exceed 100 characters.',
            'reference.max' => 'Reference cannot exceed 100 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'title' => 'title',
            'description' => 'description',
            'status' => 'status',
            'is_active' => 'active status',
            'is_featured' => 'featured status',
            'is_verified' => 'verified status',
            'sort_order' => 'sort order',
            'metadata' => 'metadata',
            'tags' => 'tags',
            'notes' => 'notes',
            'external_id' => 'external ID',
            'reference' => 'reference',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate metadata structure if provided
            if ($this->has('metadata') && is_array($this->metadata)) {
                $maxMetadataSize = 10; // Maximum number of metadata fields
                if (count($this->metadata) > $maxMetadataSize) {
                    $validator->errors()->add('metadata', "Metadata cannot have more than {$maxMetadataSize} fields.");
                }

                // Validate metadata values
                foreach ($this->metadata as $key => $value) {
                    if (!is_string($key) || strlen($key) > 50) {
                        $validator->errors()->add('metadata', 'Metadata keys must be strings with maximum 50 characters.');
                    }
                    if (!is_scalar($value) && !is_null($value)) {
                        $validator->errors()->add('metadata', 'Metadata values must be scalar or null.');
                    }
                }
            }

            // Validate tags if provided
            if ($this->has('tags') && is_array($this->tags)) {
                $maxTags = 20; // Maximum number of tags
                if (count($this->tags) > $maxTags) {
                    $validator->errors()->add('tags', "Cannot have more than {$maxTags} tags.");
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
                'message' => 'Resource creation validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug from name or title if not provided
        if (!$this->slug && ($this->name || $this->title)) {
            $this->merge([
                'slug' => \Str::slug($this->name ?: $this->title),
            ]);
        }

        // Convert boolean strings
        foreach (['is_active', 'is_featured', 'is_verified'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }

        // Clean and format tags
        if ($this->has('tags') && is_array($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', $this->tags)),
            ]);
        }

        // Clean external_id and reference
        foreach (['external_id', 'reference'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim($this->{$field}),
                ]);
            }
        }
    }
}
