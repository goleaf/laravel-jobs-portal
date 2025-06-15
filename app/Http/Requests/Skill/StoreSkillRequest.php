<?php

namespace App\Http\Requests\Skill;

use App\Models\Skill;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Enhanced Enhanced Form Request for Store Skill
 * Implements Laravel 12 best practices with Enhanced MCP patterns
 * Following proven MasterData pattern.
 */
class StoreSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Enhanced Pattern: Role-based authorization
        return auth()->check() && (
            auth()->user()->hasRole('Admin')
            || auth()->user()->hasRole('Employer')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     * Enhanced Pattern: Comprehensive skill validation with security.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Skill Basic Information
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:skills,name',
                'regex:/^[a-zA-Z0-9\s\.\-\+\#]+$/', // Allow alphanumeric, spaces, dots, hyphens, plus, hash
            ],
            'description' => ['nullable', 'string', 'max:1000'],

            // Skill Classification
            'category' => [
                'nullable',
                'string',
                'max:100',
                'in:technical,soft,language,management,design,marketing,finance,other',
            ],
            'level' => [
                'nullable',
                'string',
                'max:50',
                'in:beginner,intermediate,advanced,expert',
            ],

            // Status and Settings
            'is_active' => ['boolean'],
            'is_default' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && !auth()->user()->hasRole('Admin')) {
                        $fail(__('validation.admin_only_field'));
                    }
                },
            ],

            // Metadata
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],

            // Tags and Keywords
            'tags' => ['nullable', 'string', 'max:500'],
            'keywords' => ['nullable', 'string', 'max:500'],

            // Related Skills (array of skill IDs)
            'related_skills' => ['nullable', 'array', 'max:10'],
            'related_skills.*' => ['integer', 'exists:skills,id'],

            // Security
            'g-recaptcha-response' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (config('app.recaptcha_enabled', false) && empty($value)) {
                        $fail(__('validation.recaptcha_required'));
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Enhanced Pattern: Multilingual error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.skill_name_required'),
            'name.unique' => __('validation.skill_name_unique'),
            'name.max' => __('validation.skill_name_max'),
            'name.regex' => __('validation.skill_name_format'),
            'description.max' => __('validation.skill_description_max'),
            'category.in' => __('validation.skill_category_invalid'),
            'category.max' => __('validation.skill_category_max'),
            'level.in' => __('validation.skill_level_invalid'),
            'level.max' => __('validation.skill_level_max'),
            'is_default.admin_only' => __('validation.admin_only_field'),
            'created_by.exists' => __('validation.created_by_exists'),
            'sort_order.integer' => __('validation.sort_order_integer'),
            'sort_order.min' => __('validation.sort_order_min'),
            'sort_order.max' => __('validation.sort_order_max'),
            'tags.max' => __('validation.tags_max'),
            'keywords.max' => __('validation.keywords_max'),
            'related_skills.array' => __('validation.related_skills_array'),
            'related_skills.max' => __('validation.related_skills_max'),
            'related_skills.*.exists' => __('validation.related_skill_exists'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Enhanced Pattern: User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.skill_name'),
            'description' => __('validation.attributes.skill_description'),
            'category' => __('validation.attributes.skill_category'),
            'level' => __('validation.attributes.skill_level'),
            'is_active' => __('validation.attributes.active_status'),
            'is_default' => __('validation.attributes.default_status'),
            'created_by' => __('validation.attributes.created_by'),
            'sort_order' => __('validation.attributes.sort_order'),
            'tags' => __('validation.attributes.skill_tags'),
            'keywords' => __('validation.attributes.skill_keywords'),
            'related_skills' => __('validation.attributes.related_skills'),
        ];
    }

    /**
     * Configure the validator instance.
     * Enhanced Pattern: Enhanced validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasEnhancedValidationConflicts()) {
                $validator->errors()->add('name', __('validation.skill_conflict'));
            }

            if ($this->hasSuspiciousContent()) {
                $validator->errors()->add('name', __('validation.suspicious_content'));
            }

            if ($this->hasInvalidRelatedSkills()) {
                $validator->errors()->add('related_skills', __('validation.invalid_related_skills'));
            }

            if ($this->hasInvalidSkillFormat()) {
                $validator->errors()->add('name', __('validation.invalid_skill_format'));
            }

            if ($this->hasExcessiveKeywords()) {
                $validator->errors()->add('keywords', __('validation.excessive_keywords'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Enhanced Pattern: Data normalization.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'description' => trim($this->description ?? ''),
            'category' => strtolower(trim($this->category ?? '')),
            'level' => strtolower(trim($this->level ?? '')),
            'tags' => $this->normalizeTags($this->tags),
            'keywords' => $this->normalizeKeywords($this->keywords),
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'sort_order' => $this->sort_order ? (int) $this->sort_order : 0,
            'created_by' => auth()->id(),

            // Ensure related_skills is properly formatted
            'related_skills' => is_array($this->related_skills)
                ? array_filter(array_unique($this->related_skills))
                : [],
        ]);

        // Only allow is_default for admins
        if (auth()->user() && auth()->user()->hasRole('Admin')) {
            $this->merge([
                'is_default' => filter_var($this->is_default, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced Pattern: Enhanced error handling with security monitoring.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Enhanced validation failed for StoreSkillRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'Skill',
            'action' => 'Store',
            'skill_name' => $this->name,
            'skill_category' => $this->category,
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'suspicious_patterns' => $this->hasSuspiciousContent(),
            'invalid_format' => $this->hasInvalidSkillFormat(),
            'excessive_keywords' => $this->hasExcessiveKeywords(),
            'invalid_related' => $this->hasInvalidRelatedSkills(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Enhanced Pattern: Enhanced business logic validation.
     */
    private function hasEnhancedValidationConflicts(): bool
    {
        // Check for similar skill names (case-insensitive, ignoring spaces)
        if ($this->name) {
            $normalizedName = strtolower(str_replace(' ', '', $this->name));

            return Skill::whereRaw('LOWER(REPLACE(name, " ", "")) = ?', [$normalizedName])
                ->exists()
            ;
        }

        return false;
    }

    /**
     * Enhanced Pattern: Content security validation.
     */
    private function hasSuspiciousContent(): bool
    {
        $suspiciousPatterns = [
            'spam', 'scam', 'virus', 'malware', 'hack', 'exploit',
            'script', 'injection', 'xss', 'sql injection',
        ];

        $content = strtolower($this->name.' '.$this->description.' '.$this->tags);

        foreach ($suspiciousPatterns as $pattern) {
            if (false !== strpos($content, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enhanced Pattern: Related skills validation.
     */
    private function hasInvalidRelatedSkills(): bool
    {
        if (!$this->related_skills || !is_array($this->related_skills)) {
            return false;
        }

        // Check for self-reference (can't be related to itself)
        $skillName = strtolower($this->name);

        foreach ($this->related_skills as $relatedSkillId) {
            $relatedSkill = Skill::find($relatedSkillId);
            if ($relatedSkill && strtolower($relatedSkill->name) === $skillName) {
                return true;
            }
        }

        // Check for circular references
        if (count($this->related_skills) !== count(array_unique($this->related_skills))) {
            return true;
        }

        return false;
    }

    /**
     * Enhanced Pattern: Skill format validation.
     */
    private function hasInvalidSkillFormat(): bool
    {
        if (!$this->name) {
            return false;
        }

        // Check for common invalid formats
        $invalidPatterns = [
            '/^[0-9]+$/',                    // Numbers only
            '/^[^a-zA-Z]*$/',               // No letters
            '/(.)\1{4,}/',                  // Repeated characters (5+)
            '/^.{1,2}$/',                   // Too short (1-2 chars)
            '/[<>{}()[\]]//',               // Invalid characters
        ];

        foreach ($invalidPatterns as $pattern) {
            if (preg_match($pattern, $this->name)) {
                return true;
            }
        }

        // Check for minimum meaningful content
        $cleanName = preg_replace('/[^a-zA-Z]/', '', $this->name);
        if (strlen($cleanName) < 2) {
            return true;
        }

        return false;
    }

    /**
     * Enhanced Pattern: Keywords validation.
     */
    private function hasExcessiveKeywords(): bool
    {
        if (!$this->keywords) {
            return false;
        }

        $keywords = explode(',', $this->keywords);
        $keywords = array_map('trim', $keywords);
        $keywords = array_filter($keywords);

        // Too many keywords
        if (count($keywords) > 20) {
            return true;
        }

        // Keywords too long
        foreach ($keywords as $keyword) {
            if (strlen($keyword) > 50) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enhanced Pattern: Tags normalization.
     */
    private function normalizeTags(?string $tags): ?string
    {
        if (empty($tags)) {
            return null;
        }

        $tagsArray = explode(',', $tags);
        $tagsArray = array_map('trim', $tagsArray);
        $tagsArray = array_filter($tagsArray);
        $tagsArray = array_unique($tagsArray);
        $tagsArray = array_slice($tagsArray, 0, 10); // Limit to 10 tags

        return implode(', ', $tagsArray);
    }

    /**
     * Enhanced Pattern: Keywords normalization.
     */
    private function normalizeKeywords(?string $keywords): ?string
    {
        if (empty($keywords)) {
            return null;
        }

        $keywordsArray = explode(',', $keywords);
        $keywordsArray = array_map('trim', $keywordsArray);
        $keywordsArray = array_filter($keywordsArray);
        $keywordsArray = array_unique($keywordsArray);
        $keywordsArray = array_slice($keywordsArray, 0, 15); // Limit to 15 keywords

        return implode(', ', $keywordsArray);
    }
}
