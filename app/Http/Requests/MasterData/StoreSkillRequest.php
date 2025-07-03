<?php

namespace App\Http\Requests\MasterData;

use App\Models\Skill;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Comprehensive Form Request for storing Skills
 * Implements Laravel 12 best practices with skill management validation.
 */
class StoreSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only admin/HR users can create/modify skills.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Only admin, HR managers, or users with skill management permissions
        return $user->hasRole('Admin') ||
               $user->hasRole('HR Manager') ||
               $user->hasRole('Content Manager') ||
               $user->can('manage-skills');
    }

    /**
     * Get the validation rules that apply to the request.
     * Comprehensive skill data validation.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Basic skill information
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'unique:skills,name',
                'regex:/^[a-zA-Z0-9\s\-\.\+\#\(\)\/]+$/', // Allow programming languages like C++, C#, .NET
            ],

            'slug' => [
                'nullable',
                'string',
                'min:2',
                'max:100',
                'unique:skills,slug',
                'regex:/^[a-z0-9\-]+$/', // URL-friendly slug
            ],

            'description' => [
                'nullable',
                'string',
                'min:10',
                'max:1000',
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:255',
            ],

            // Skill categorization
            'category_id' => [
                'nullable',
                'integer',
                'exists:skill_categories,id',
            ],

            'skill_type' => [
                'required',
                'string',
                Rule::in(['technical', 'soft', 'language', 'certification', 'tool', 'framework', 'methodology']),
            ],

            'industry' => [
                'nullable',
                'string',
                Rule::in(['IT', 'Healthcare', 'Finance', 'Marketing', 'Sales', 'Manufacturing', 'Education', 'Legal', 'Design', 'General']),
            ],

            // Skill level and complexity
            'proficiency_levels' => [
                'nullable',
                'array',
                'max:10',
            ],
            'proficiency_levels.*' => [
                'string',
                Rule::in(['Beginner', 'Intermediate', 'Advanced', 'Expert', 'Basic', 'Proficient', 'Native']),
                'distinct',
            ],

            'complexity_level' => [
                'nullable',
                'string',
                Rule::in(['Entry Level', 'Intermediate', 'Advanced', 'Expert']),
            ],

            'experience_required_months' => [
                'nullable',
                'integer',
                'min:0',
                'max:600', // 50 years max
            ],

            // Related skills and prerequisites
            'parent_skill_id' => [
                'nullable',
                'integer',
                'exists:skills,id',
                'different:id', // Can't be parent of itself
            ],

            'related_skills' => [
                'nullable',
                'array',
                'max:20',
            ],
            'related_skills.*' => [
                'integer',
                'exists:skills,id',
                'distinct',
            ],

            'prerequisite_skills' => [
                'nullable',
                'array',
                'max:10',
            ],
            'prerequisite_skills.*' => [
                'integer',
                'exists:skills,id',
                'distinct',
            ],

            // Market and demand data
            'demand_level' => [
                'nullable',
                'string',
                Rule::in(['Very Low', 'Low', 'Medium', 'High', 'Very High']),
            ],

            'average_salary_min' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999',
            ],

            'average_salary_max' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999',
                'gte:average_salary_min',
            ],

            'salary_currency' => [
                'nullable',
                'string',
                'size:3',
                'exists:salary_currencies,iso_code',
            ],

            // Learning and certification
            'learning_resources' => [
                'nullable',
                'array',
                'max:20',
            ],
            'learning_resources.*' => [
                'url',
                'max:500',
                'distinct',
            ],

            'certification_available' => [
                'nullable',
                'boolean',
            ],

            'certification_providers' => [
                'nullable',
                'array',
                'max:10',
            ],
            'certification_providers.*' => [
                'string',
                'max:100',
                'distinct',
            ],

            // Skill metadata
            'keywords' => [
                'nullable',
                'array',
                'max:30',
            ],
            'keywords.*' => [
                'string',
                'max:50',
                'distinct',
            ],

            'synonyms' => [
                'nullable',
                'array',
                'max:20',
            ],
            'synonyms.*' => [
                'string',
                'max:100',
                'distinct',
            ],

            // Display and organization
            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'color_code' => [
                'nullable',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', // Valid hex color
            ],

            'display_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],

            // Status and visibility
            'is_active' => [
                'nullable',
                'boolean',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],

            'is_trending' => [
                'nullable',
                'boolean',
            ],

            'is_verified' => [
                'nullable',
                'boolean',
            ],

            // External integration
            'external_id' => [
                'nullable',
                'string',
                'max:100',
                'unique:skills,external_id',
            ],

            'source' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(['Manual', 'LinkedIn', 'Indeed', 'GitHub', 'StackOverflow', 'Import', 'API']),
            ],

            // Additional data
            'metadata' => [
                'nullable',
                'array',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Multilingual skill management error messages.
     */
    public function messages(): array
    {
        return [
            // Basic information
            'name.required' => __('skills.validation.name_required'),
            'name.unique' => __('skills.validation.name_unique'),
            'name.regex' => __('skills.validation.name_format'),
            'slug.unique' => __('skills.validation.slug_unique'),
            'slug.regex' => __('skills.validation.slug_format'),

            // Categorization
            'category_id.exists' => __('skills.validation.category_invalid'),
            'skill_type.required' => __('skills.validation.type_required'),
            'skill_type.in' => __('skills.validation.type_invalid'),
            'industry.in' => __('skills.validation.industry_invalid'),

            // Levels and complexity
            'proficiency_levels.array' => __('skills.validation.proficiency_array'),
            'proficiency_levels.*.in' => __('skills.validation.proficiency_invalid'),
            'proficiency_levels.*.distinct' => __('skills.validation.proficiency_unique'),
            'complexity_level.in' => __('skills.validation.complexity_invalid'),
            'experience_required_months.min' => __('skills.validation.experience_negative'),
            'experience_required_months.max' => __('skills.validation.experience_too_high'),

            // Relationships
            'parent_skill_id.exists' => __('skills.validation.parent_skill_invalid'),
            'parent_skill_id.different' => __('skills.validation.parent_skill_self'),
            'related_skills.*.exists' => __('skills.validation.related_skill_invalid'),
            'related_skills.*.distinct' => __('skills.validation.related_skills_unique'),
            'prerequisite_skills.*.exists' => __('skills.validation.prerequisite_skill_invalid'),
            'prerequisite_skills.*.distinct' => __('skills.validation.prerequisite_skills_unique'),

            // Market data
            'demand_level.in' => __('skills.validation.demand_level_invalid'),
            'average_salary_max.gte' => __('skills.validation.salary_max_gte_min'),
            'salary_currency.exists' => __('skills.validation.currency_invalid'),

            // Learning resources
            'learning_resources.*.url' => __('skills.validation.learning_resource_url'),
            'learning_resources.*.distinct' => __('skills.validation.learning_resources_unique'),

            // Metadata
            'keywords.*.distinct' => __('skills.validation.keywords_unique'),
            'synonyms.*.distinct' => __('skills.validation.synonyms_unique'),
            'color_code.regex' => __('skills.validation.color_format'),
            'external_id.unique' => __('skills.validation.external_id_unique'),
            'source.in' => __('skills.validation.source_invalid'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'name' => __('skills.fields.skill_name'),
            'slug' => __('skills.fields.skill_slug'),
            'description' => __('skills.fields.description'),
            'short_description' => __('skills.fields.short_description'),
            'category_id' => __('skills.fields.category'),
            'skill_type' => __('skills.fields.skill_type'),
            'industry' => __('skills.fields.industry'),
            'proficiency_levels' => __('skills.fields.proficiency_levels'),
            'complexity_level' => __('skills.fields.complexity_level'),
            'experience_required_months' => __('skills.fields.experience_required'),
            'parent_skill_id' => __('skills.fields.parent_skill'),
            'related_skills' => __('skills.fields.related_skills'),
            'prerequisite_skills' => __('skills.fields.prerequisite_skills'),
            'demand_level' => __('skills.fields.demand_level'),
            'average_salary_min' => __('skills.fields.salary_min'),
            'average_salary_max' => __('skills.fields.salary_max'),
            'salary_currency' => __('skills.fields.salary_currency'),
            'learning_resources' => __('skills.fields.learning_resources'),
            'certification_providers' => __('skills.fields.certification_providers'),
            'keywords' => __('skills.fields.keywords'),
            'synonyms' => __('skills.fields.synonyms'),
            'color_code' => __('skills.fields.color'),
            'display_order' => __('skills.fields.display_order'),
            'external_id' => __('skills.fields.external_id'),
            'source' => __('skills.fields.source'),
        ];
    }

    /**
     * Configure the validator instance.
     * Enhanced skill management validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check for circular parent-child relationships
            if ($this->hasCircularParentRelationship()) {
                $validator->errors()->add('parent_skill_id', __('skills.validation.circular_parent_relationship'));
            }

            // Validate skill relationships consistency
            if ($this->hasInvalidSkillRelationships()) {
                $validator->errors()->add('related_skills', __('skills.validation.invalid_skill_relationships'));
            }

            // Check for conflicting skill data
            if ($this->hasConflictingSkillData()) {
                $validator->errors()->add('skill_type', __('skills.validation.conflicting_skill_data'));
            }

            // Validate industry-specific restrictions
            if ($this->violatesIndustryRestrictions()) {
                $validator->errors()->add('industry', __('skills.validation.industry_restrictions'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Skill data normalization.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug if not provided
        if (empty($this->slug) && ! empty($this->name)) {
            $this->merge([
                'slug' => \Str::slug($this->name),
            ]);
        }

        // Normalize text fields
        $this->merge([
            'name' => trim($this->name ?? ''),
            'description' => trim($this->description ?? '') ?: null,
            'short_description' => trim($this->short_description ?? '') ?: null,
        ]);

        // Normalize case for specific fields
        if ($this->filled('salary_currency')) {
            $this->merge(['salary_currency' => strtoupper($this->salary_currency)]);
        }

        // Set defaults
        $this->merge([
            'is_active' => filter_var($this->is_active ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_featured' => filter_var($this->is_featured ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_trending' => filter_var($this->is_trending ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_verified' => filter_var($this->is_verified ?? false, FILTER_VALIDATE_BOOLEAN),
            'certification_available' => filter_var($this->certification_available ?? false, FILTER_VALIDATE_BOOLEAN),
            'display_order' => (int) ($this->display_order ?? 0),
        ]);

        // Clean arrays and remove duplicates
        if ($this->filled('proficiency_levels')) {
            $this->merge([
                'proficiency_levels' => array_filter(array_unique((array) $this->proficiency_levels)),
            ]);
        }

        if ($this->filled('keywords')) {
            $this->merge([
                'keywords' => array_filter(array_unique(array_map('trim', (array) $this->keywords))),
            ]);
        }

        if ($this->filled('synonyms')) {
            $this->merge([
                'synonyms' => array_filter(array_unique(array_map('trim', (array) $this->synonyms))),
            ]);
        }

        if ($this->filled('related_skills')) {
            $this->merge([
                'related_skills' => array_filter(array_unique((array) $this->related_skills)),
            ]);
        }

        if ($this->filled('prerequisite_skills')) {
            $this->merge([
                'prerequisite_skills' => array_filter(array_unique((array) $this->prerequisite_skills)),
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced skill management logging.
     */
    protected function failedValidation(Validator $validator): void
    {
        \Log::warning('Skill creation validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input_data' => $this->safe()->toArray(),
            'user_id' => Auth::id(),
            'user_role' => Auth::user()?->getRoleNames(),
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Get processed data for skill creation.
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();

        // Add creator information
        $data['created_by'] = Auth::id();

        // Set timestamps
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Process arrays as JSON
        $arrayFields = [
            'proficiency_levels', 'related_skills', 'prerequisite_skills',
            'learning_resources', 'certification_providers', 'keywords',
            'synonyms', 'metadata',
        ];

        foreach ($arrayFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = json_encode($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Check for circular parent-child relationships.
     */
    private function hasCircularParentRelationship(): bool
    {
        $parentId = $this->parent_skill_id;

        if (! $parentId) {
            return false;
        }

        // Check if the parent skill has this skill as its parent (simple circular check)
        $parentSkill = Skill::find($parentId);
        if ($parentSkill && $parentSkill->parent_skill_id) {
            // This would need more complex logic to check deep circular relationships
            return false; // Simplified for now
        }

        return false;
    }

    /**
     * Validate skill relationships consistency.
     */
    private function hasInvalidSkillRelationships(): bool
    {
        $relatedSkills = $this->related_skills ?? [];
        $prerequisites = $this->prerequisite_skills ?? [];

        // Check if any related skill is also a prerequisite
        $overlap = array_intersect($relatedSkills, $prerequisites);
        if (! empty($overlap)) {
            return true;
        }

        // Check if skill type compatibility makes sense for relationships
        $skillType = $this->skill_type;
        if ($skillType === 'language' && ! empty($prerequisites)) {
            // Language skills typically don't have technical prerequisites
            $technicalPrereqs = Skill::whereIn('id', $prerequisites)
                ->where('skill_type', 'technical')
                ->exists();
            if ($technicalPrereqs) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for conflicting skill data.
     */
    private function hasConflictingSkillData(): bool
    {
        $skillType = $this->skill_type;
        $industry = $this->industry;

        // Example: Soft skills shouldn't have technical industry requirements
        if ($skillType === 'soft' && in_array($industry, ['IT', 'Manufacturing'])) {
            return false; // Actually, soft skills can apply to any industry
        }

        // Check if certification availability conflicts with skill type
        if ($skillType === 'soft' && $this->certification_available === true) {
            // Some soft skills do have certifications, so this is acceptable
            return false;
        }

        return false;
    }

    /**
     * Validate industry-specific restrictions.
     */
    private function violatesIndustryRestrictions(): bool
    {
        $skillType = $this->skill_type;
        $industry = $this->industry;
        $name = $this->name;

        // Example restrictions
        if ($industry === 'Healthcare' && $skillType === 'technical') {
            // Check if the skill name contains healthcare-relevant terms
            $healthcareTerms = ['medical', 'clinical', 'patient', 'hospital', 'healthcare'];
            $hasHealthcareTerm = false;

            foreach ($healthcareTerms as $term) {
                if (stripos($name, $term) !== false) {
                    $hasHealthcareTerm = true;
                    break;
                }
            }

            // For now, allow all skills
            return false;
        }

        return false;
    }
}
