<?php

namespace App\Http\Requests\Api\Universal;

use App\Models\Skill;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SkillStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Authenticated users can create skills
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:skills,name',
            'description' => 'sometimes|string|max:1000',
            'category' => 'sometimes|string|max:100',
            'type' => 'sometimes|string|in:technical,soft,language,certification,industry',
            'level' => 'sometimes|string|in:beginner,intermediate,advanced,expert',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'synonyms' => 'sometimes|array',
            'synonyms.*' => 'string|max:255|distinct',
            'parent_skill_id' => 'sometimes|integer|exists:skills,id',
            'demand_score' => 'sometimes|numeric|min:0|max:100',
            'market_trend' => 'sometimes|string|in:rising,stable,declining,emerging',
            'certification_required' => 'sometimes|boolean',
            'external_id' => 'sometimes|string|max:100|unique:skills,external_id',
            'icon' => 'sometimes|string|max:255',
            'color' => 'sometimes|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'sometimes|integer|min:0',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'metadata' => 'sometimes|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Skill name is required.',
            'name.unique' => 'A skill with this name already exists.',
            'name.max' => 'Skill name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'category.max' => 'Category cannot exceed 100 characters.',
            'type.in' => 'Skill type must be one of: technical, soft, language, certification, industry.',
            'level.in' => 'Skill level must be one of: beginner, intermediate, advanced, expert.',
            'is_active.boolean' => 'Active status must be true or false.',
            'is_featured.boolean' => 'Featured status must be true or false.',
            'synonyms.array' => 'Synonyms must be provided as an array.',
            'synonyms.*.distinct' => 'Synonym values must be unique.',
            'synonyms.*.max' => 'Each synonym cannot exceed 255 characters.',
            'parent_skill_id.exists' => 'Parent skill does not exist.',
            'demand_score.numeric' => 'Demand score must be a number.',
            'demand_score.min' => 'Demand score must be at least 0.',
            'demand_score.max' => 'Demand score cannot exceed 100.',
            'market_trend.in' => 'Market trend must be one of: rising, stable, declining, emerging.',
            'certification_required.boolean' => 'Certification requirement must be true or false.',
            'external_id.unique' => 'External ID already exists.',
            'icon.max' => 'Icon name cannot exceed 255 characters.',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF5733).',
            'sort_order.integer' => 'Sort order must be a valid integer.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'tags.array' => 'Tags must be provided as an array.',
            'tags.*.max' => 'Each tag cannot exceed 50 characters.',
            'metadata.array' => 'Metadata must be provided as an array.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'skill name',
            'description' => 'description',
            'category' => 'category',
            'type' => 'skill type',
            'level' => 'skill level',
            'is_active' => 'active status',
            'is_featured' => 'featured status',
            'synonyms' => 'synonyms',
            'parent_skill_id' => 'parent skill',
            'demand_score' => 'demand score',
            'market_trend' => 'market trend',
            'certification_required' => 'certification requirement',
            'external_id' => 'external ID',
            'icon' => 'icon',
            'color' => 'color',
            'sort_order' => 'sort order',
            'tags' => 'tags',
            'metadata' => 'metadata',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check for similar skill names (fuzzy matching)
            if ($this->has('name')) {
                $similarSkills = Skill::where('name', 'LIKE', '%'.$this->name.'%')
                    ->where('name', '!=', $this->name)
                    ->limit(5)
                    ->pluck('name')
                    ->toArray()
                ;

                if (!empty($similarSkills)) {
                    $validator->errors()->add(
                        'name',
                        'Similar skills already exist: '.implode(', ', $similarSkills)
                        .'. Consider using an existing skill or choose a more specific name.'
                    );
                }
            }

            // Validate parent skill hierarchy
            if ($this->has('parent_skill_id')) {
                $parentSkill = Skill::find($this->parent_skill_id);
                if ($parentSkill && $parentSkill->parent_skill_id) {
                    $validator->errors()->add('parent_skill_id', 'Cannot create nested skill hierarchy more than 2 levels deep.');
                }
            }

            // Validate synonyms don't conflict with existing skill names
            if ($this->has('synonyms') && is_array($this->synonyms)) {
                $existingSkills = Skill::whereIn('name', $this->synonyms)->pluck('name');
                if ($existingSkills->count() > 0) {
                    $validator->errors()->add(
                        'synonyms',
                        'Synonyms cannot match existing skill names: '.$existingSkills->implode(', ')
                    );
                }
            }

            // Validate metadata structure
            if ($this->has('metadata') && is_array($this->metadata)) {
                $maxMetadataSize = 10;
                if (count($this->metadata) > $maxMetadataSize) {
                    $validator->errors()->add('metadata', "Metadata cannot have more than {$maxMetadataSize} fields.");
                }

                foreach ($this->metadata as $key => $value) {
                    if (!is_string($key) || strlen($key) > 50) {
                        $validator->errors()->add('metadata', 'Metadata keys must be strings with maximum 50 characters.');
                    }
                }
            }

            // Validate tags
            if ($this->has('tags') && is_array($this->tags)) {
                $maxTags = 10;
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
                'message' => 'Skill creation validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and format the skill name
        if ($this->has('name')) {
            $this->merge([
                'name' => ucwords(trim($this->name)),
            ]);
        }

        // Generate slug from name
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => \Str::slug($this->name),
            ]);
        }

        // Set default values
        $defaults = [
            'is_active' => true,
            'is_featured' => false,
            'type' => 'technical',
            'level' => 'intermediate',
            'certification_required' => false,
            'demand_score' => 50.0,
            'market_trend' => 'stable',
        ];

        foreach ($defaults as $key => $default) {
            if (!$this->has($key)) {
                $this->merge([$key => $default]);
            }
        }

        // Convert boolean strings
        foreach (['is_active', 'is_featured', 'certification_required'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }

        // Clean and format synonyms
        if ($this->has('synonyms') && is_array($this->synonyms)) {
            $this->merge([
                'synonyms' => array_filter(array_map('trim', $this->synonyms)),
            ]);
        }

        // Clean and format tags
        if ($this->has('tags') && is_array($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', $this->tags)),
            ]);
        }

        // Clean external_id if provided
        if ($this->has('external_id')) {
            $this->merge([
                'external_id' => trim($this->external_id),
            ]);
        }

        // Add created_by field
        $this->merge([
            'created_by' => auth()->id(),
        ]);
    }
}
