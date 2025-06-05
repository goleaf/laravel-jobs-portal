<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCandidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone can register as candidate
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'string', 'max:20'],
            'dob' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:0,1'], // 0=Male, 1=Female
            'marital_status_id' => ['nullable', 'exists:marital_statuses,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'career_level_id' => ['nullable', 'exists:career_levels,id'],
            'functional_area_id' => ['nullable', 'exists:functional_areas,id'],
            'current_salary' => ['nullable', 'numeric', 'min:0'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'salary_currency_id' => ['nullable', 'exists:salary_currencies,id'],
            'experience' => ['nullable', 'integer', 'min:0', 'max:50'],
            'is_immediate_available' => ['boolean'],
            'is_active' => ['boolean'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => __('validation_custom.candidate.first_name_required'),
            'last_name.required' => __('validation_custom.candidate.last_name_required'),
            'email.required' => __('validation_custom.candidate.email_required'),
            'phone.required' => __('validation_custom.candidate.phone_required'),
            'password.required' => __('validation_custom.user.password_required'),
            'password.min' => __('validation_custom.user.password_min'),
            'password.confirmed' => __('validation_custom.user.password_confirmed'),
            'email.unique' => __('validation_custom.user.email_unique'),
            'experience.numeric' => __('validation_custom.candidate.experience_numeric'),
            'profile_image.image' => __('validation_custom.file.avatar_mimes'),
            'profile_image.max' => __('validation_custom.file.avatar_max'),
            'resume.mimes' => __('validation_custom.file.resume_mimes'),
            'resume.max' => __('validation_custom.file.resume_max'),
            'dob.before' => 'Date of birth must be before today.',
            'gender.in' => 'Please select a valid gender option.',
            'experience.max' => 'Experience cannot exceed 50 years.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'dob' => 'date of birth',
            'marital_status_id' => 'marital status',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'career_level_id' => 'career level',
            'functional_area_id' => 'functional area',
            'salary_currency_id' => 'salary currency',
            'profile_image' => 'profile image',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_immediate_available' => $this->boolean('is_immediate_available', false),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
