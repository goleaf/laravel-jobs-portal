<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfessionRequest extends FormRequest
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
        $professionId = $this->route('profession')->id;

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('professions', 'code')->ignore($professionId),
                'regex:/^[0-9]+$/', // Only numbers for codes
            ],
            'category_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:profession_categories,id',
            ],
            'isco_code' => [
                'sometimes',
                'nullable',
                'string',
                'max:10',
                'regex:/^[0-9]+$/', // ISCO codes are numeric
            ],
            'skill_level' => [
                'sometimes',
                'required',
                'string',
                'in:High,Medium,Low',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'is_featured' => [
                'sometimes',
                'boolean',
            ],
            'sort_order' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
            ],
            'metadata' => [
                'sometimes',
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
                'sometimes',
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
            'code.unique' => 'This profession code already exists.',
            'code.regex' => 'The profession code must contain only numbers.',
            'category_id.exists' => 'The selected profession category does not exist.',
            'isco_code.regex' => 'The ISCO code must contain only numbers.',
            'skill_level.in' => 'The skill level must be High, Medium, or Low.',
            'sort_order.min' => 'The sort order must be 0 or greater.',
            'metadata.difficulty_level.in' => 'The difficulty level must be High, Medium, or Low.',

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
            $professionId = $this->route('profession')->id;

            // Validate that category exists and is active
            if ($this->category_id) {
                $category = \App\Models\ProfessionCategory::find($this->category_id);
                if ($category && ! $category->is_active) {
                    $validator->errors()->add(
                        'category_id',
                        'Cannot move profession to an inactive category.'
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

            // Validate that profession can be deactivated (check job associations)
            if ($this->has('is_active') && ! $this->is_active) {
                $profession = \App\Models\Profession::find($professionId);

                if ($profession && $profession->hasJobs()) {
                    $activeJobs = $profession->getActiveJobCount();
                    if ($activeJobs > 0) {
                        $validator->errors()->add(
                            'is_active',
                            "Cannot deactivate profession that has {$activeJobs} active jobs."
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
        // Clean up metadata if provided
        if ($this->has('metadata') && is_array($this->metadata)) {
            $metadata = $this->metadata;

            // Remove empty values
            $metadata = array_filter($metadata, function ($value) {
                return ! is_null($value) && $value !== '';
            });

            $this->merge(['metadata' => $metadata]);
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
