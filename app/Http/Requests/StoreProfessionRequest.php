<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfessionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                'unique:professions,code',
                'regex:/^[0-9]+$/', // Only numbers for codes
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:profession_categories,id',
            ],
            'isco_code' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^[0-9]+$/', // ISCO codes are numeric
            ],
            'skill_level' => [
                'required',
                'string',
                'in:High,Medium,Low',
            ],
            'is_active' => [
                'boolean',
            ],
            'is_featured' => [
                'boolean',
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
            ],
            'metadata' => [
                'nullable',
                'array',
            ],
            'metadata.difficulty_level' => [
                'nullable',
                'string',
                'in:High,Medium,Low',
            ],
            'metadata.in_high_demand' => [
                'nullable',
                'boolean',
            ],

            // Translation validation
            'translations' => [
                'required',
                'array',
                'min:1',
            ],
            'translations.*' => [
                'array',
                'required',
            ],
            'translations.*.name' => [
                'required',
                'string',
                'max:255',
            ],
            'translations.*.description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'translations.*.skills_required' => [
                'nullable',
                'array',
            ],
            'translations.*.skills_required.*' => [
                'string',
                'max:100',
            ],
            'translations.*.education_requirements' => [
                'nullable',
                'array',
            ],
            'translations.*.education_requirements.*' => [
                'string',
                'max:200',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'The profession code is required.',
            'code.unique' => 'This profession code already exists.',
            'code.regex' => 'The profession code must contain only numbers.',
            'category_id.required' => 'The profession category is required.',
            'category_id.exists' => 'The selected profession category does not exist.',
            'isco_code.regex' => 'The ISCO code must contain only numbers.',
            'skill_level.required' => 'The skill level is required.',
            'skill_level.in' => 'The skill level must be High, Medium, or Low.',
            'sort_order.required' => 'The sort order is required.',
            'sort_order.min' => 'The sort order must be 0 or greater.',
            'metadata.difficulty_level.in' => 'The difficulty level must be High, Medium, or Low.',

            'translations.required' => 'At least one translation is required.',
            'translations.min' => 'At least one translation is required.',
            'translations.*.name.required' => 'The profession name is required for each language.',
            'translations.*.name.max' => 'The profession name cannot exceed 255 characters.',
            'translations.*.description.max' => 'The profession description cannot exceed 2000 characters.',
            'translations.*.skills_required.*.max' => 'Each skill cannot exceed 100 characters.',
            'translations.*.education_requirements.*.max' => 'Each education requirement cannot exceed 200 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'code' => 'profession code',
            'category_id' => 'profession category',
            'isco_code' => 'ISCO code',
            'skill_level' => 'skill level',
            'is_active' => 'active status',
            'is_featured' => 'featured status',
            'sort_order' => 'sort order',
            'metadata.difficulty_level' => 'difficulty level',
            'metadata.in_high_demand' => 'high demand status',
            'translations.*.name' => 'profession name',
            'translations.*.description' => 'profession description',
            'translations.*.skills_required' => 'skills required',
            'translations.*.education_requirements' => 'education requirements',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that category exists and is active
            if ($this->category_id) {
                $category = \App\Models\ProfessionCategory::find($this->category_id);
                if ($category && ! $category->is_active) {
                    $validator->errors()->add(
                        'category_id',
                        'Cannot create profession in an inactive category.'
                    );
                }
            }

            // Validate translation locales
            if ($this->translations) {
                $supportedLocales = ['en', 'lt', 'ru', 'pl', 'de', 'fr', 'es', 'zh', 'ar', 'pt', 'tr', 'it', 'ja', 'hi'];

                foreach ($this->translations as $locale => $translation) {
                    if (! in_array($locale, $supportedLocales)) {
                        $validator->errors()->add(
                            "translations.{$locale}",
                            "The locale '{$locale}' is not supported."
                        );
                    }
                }

                // Require English translation
                if (! isset($this->translations['en'])) {
                    $validator->errors()->add(
                        'translations.en',
                        'English translation is required.'
                    );
                }

                // Validate skills_required array length
                foreach ($this->translations as $locale => $translation) {
                    if (isset($translation['skills_required']) && is_array($translation['skills_required'])) {
                        if (count($translation['skills_required']) > 20) {
                            $validator->errors()->add(
                                "translations.{$locale}.skills_required",
                                'Cannot have more than 20 skills.'
                            );
                        }
                    }

                    // Validate education_requirements array length
                    if (isset($translation['education_requirements']) && is_array($translation['education_requirements'])) {
                        if (count($translation['education_requirements']) > 10) {
                            $validator->errors()->add(
                                "translations.{$locale}.education_requirements",
                                'Cannot have more than 10 education requirements.'
                            );
                        }
                    }
                }
            }

            // Validate ISCO code format if provided
            if ($this->isco_code) {
                // ISCO codes should be 4 digits
                if (strlen($this->isco_code) !== 4) {
                    $validator->errors()->add(
                        'isco_code',
                        'ISCO code must be exactly 4 digits.'
                    );
                }
            }

            // Validate code format based on category
            if ($this->code && $this->category_id) {
                $category = \App\Models\ProfessionCategory::find($this->category_id);
                if ($category) {
                    // Code should start with category code
                    if (! str_starts_with($this->code, $category->code)) {
                        $validator->errors()->add(
                            'code',
                            "Profession code should start with category code '{$category->code}'."
                        );
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'is_featured' => $this->boolean('is_featured', false),
            'sort_order' => $this->integer('sort_order', 0),
        ]);

        // Set default metadata
        if (! $this->has('metadata')) {
            $this->merge([
                'metadata' => [
                    'difficulty_level' => $this->skill_level ?? 'Medium',
                    'in_high_demand' => false,
                ],
            ]);
        }

        // Clean up skills and education requirements arrays
        if ($this->translations && is_array($this->translations)) {
            $translations = $this->translations;

            foreach ($translations as $locale => &$translation) {
                // Clean skills_required array
                if (isset($translation['skills_required']) && is_array($translation['skills_required'])) {
                    $translation['skills_required'] = array_values(array_filter($translation['skills_required'], function ($skill) {
                        return ! empty(trim($skill));
                    }));
                }

                // Clean education_requirements array
                if (isset($translation['education_requirements']) && is_array($translation['education_requirements'])) {
                    $translation['education_requirements'] = array_values(array_filter($translation['education_requirements'], function ($req) {
                        return ! empty(trim($req));
                    }));
                }
            }

            $this->merge(['translations' => $translations]);
        }
    }
}
