<?php

namespace App\Http\Requests\Skill;

use App\Models\Skill;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Skill::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:skills,name',
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'is_active' => 'sometimes|boolean',
            'is_default' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.skill.name.required'),
            'name.string' => __('validation.skill.name.string'),
            'name.max' => __('validation.skill.name.max'),
            'name.unique' => __('validation.skill.name.unique'),
            'description.string' => __('validation.skill.description.string'),
            'description.max' => __('validation.skill.description.max'),
            'category.string' => __('validation.skill.category.string'),
            'category.max' => __('validation.skill.category.max'),
            'level.in' => __('validation.skill.level.in'),
            'is_active.boolean' => __('validation.skill.is_active.boolean'),
            'is_default.boolean' => __('validation.skill.is_default.boolean'),
            'is_featured.boolean' => __('validation.skill.is_featured.boolean'),
            'sort_order.integer' => __('validation.skill.sort_order.integer'),
            'sort_order.min' => __('validation.skill.sort_order.min'),
            'sort_order.max' => __('validation.skill.sort_order.max'),
            'icon.string' => __('validation.skill.icon.string'),
            'icon.max' => __('validation.skill.icon.max'),
            'color.string' => __('validation.skill.color.string'),
            'color.regex' => __('validation.skill.color.regex'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('attributes.skill.name'),
            'description' => __('attributes.skill.description'),
            'category' => __('attributes.skill.category'),
            'level' => __('attributes.skill.level'),
            'is_active' => __('attributes.skill.is_active'),
            'is_default' => __('attributes.skill.is_default'),
            'is_featured' => __('attributes.skill.is_featured'),
            'sort_order' => __('attributes.skill.sort_order'),
            'icon' => __('attributes.skill.icon'),
            'color' => __('attributes.skill.color'),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  mixed  $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check for similar skill names to prevent duplicates
            if ($this->input('name')) {
                $similarSkills = Skill::where('name', 'like', '%'.$this->input('name').'%')
                    ->where('name', '!=', $this->input('name'))
                    ->limit(3)
                    ->pluck('name')
                    ->toArray();

                if (! empty($similarSkills)) {
                    $validator->errors()->add('name', __('validation.skill.similar_exists', [
                        'skills' => implode(', ', $similarSkills),
                    ]));
                }
            }

            // Validate skill name doesn't contain inappropriate content
            if ($this->input('name')) {
                $inappropriateWords = ['test', 'dummy', 'fake', 'sample'];
                $name = strtolower($this->input('name'));

                foreach ($inappropriateWords as $word) {
                    if (str_contains($name, $word)) {
                        $validator->errors()->add('name', __('validation.skill.inappropriate_content'));

                        break;
                    }
                }
            }

            // Validate icon format if provided
            if ($this->input('icon')) {
                $validIconPrefixes = ['fa-', 'fas ', 'far ', 'fab ', 'fal ', 'fad '];
                $icon = $this->input('icon');
                $isValidIcon = false;

                foreach ($validIconPrefixes as $prefix) {
                    if (str_starts_with($icon, $prefix)) {
                        $isValidIcon = true;

                        break;
                    }
                }

                if (! $isValidIcon) {
                    $validator->errors()->add('icon', __('validation.skill.icon_format_invalid'));
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'is_default' => $this->boolean('is_default', false),
            'is_featured' => $this->boolean('is_featured', false),
            'sort_order' => $this->input('sort_order', 0),
        ]);

        // Clean and format skill name
        if ($this->has('name')) {
            $name = trim($this->input('name'));
            $name = ucwords(strtolower($name)); // Proper case
            $this->merge(['name' => $name]);
        }

        // Clean category
        if ($this->has('category')) {
            $category = trim($this->input('category'));
            $category = ucwords(strtolower($category));
            $this->merge(['category' => $category]);
        }

        // Ensure color has # prefix
        if ($this->has('color') && $this->input('color')) {
            $color = $this->input('color');
            if (! str_starts_with($color, '#')) {
                $color = '#'.$color;
            }
            $this->merge(['color' => strtoupper($color)]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log skill creation attempt for security
        \Log::info('Skill creation attempted', [
            'user_id' => $this->user()->id,
            'skill_name' => $this->input('name'),
            'category' => $this->input('category'),
            'ip' => $this->ip(),
        ]);
    }
}
