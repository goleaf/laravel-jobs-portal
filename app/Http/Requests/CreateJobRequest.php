<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\Job::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'job_title' => 'required|string|max:255',
            'company_id' => 'required|integer|exists:companies,id',
            'category_id' => 'required|integer|exists:job_categories,id',
            'job_type_id' => 'required|integer|exists:job_types,id',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_period_id' => 'nullable|integer|exists:salary_periods,id',
            'experience_level_id' => 'required|integer|exists:experience_levels,id',
            'education_level_id' => 'required|integer|exists:education_levels,id',
            'application_deadline' => 'required|date|after:today',
            'is_remote' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'required|string|in:draft,active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'job_title.required' => __('validation.required', ['attribute' => 'job title']),
            'job_title.max' => __('validation.max.string', ['attribute' => 'job title', 'max' => 255]),
            'company_id.required' => __('validation.required', ['attribute' => 'company']),
            'company_id.exists' => __('validation.exists', ['attribute' => 'company']),
            'category_id.required' => __('validation.required', ['attribute' => 'category']),
            'category_id.exists' => __('validation.exists', ['attribute' => 'category']),
            'job_type_id.required' => __('validation.required', ['attribute' => 'job type']),
            'job_type_id.exists' => __('validation.exists', ['attribute' => 'job type']),
            'location.required' => __('validation.required', ['attribute' => 'location']),
            'location.max' => __('validation.max.string', ['attribute' => 'location', 'max' => 255]),
            'description.required' => __('validation.required', ['attribute' => 'description']),
            'requirements.required' => __('validation.required', ['attribute' => 'requirements']),
            'salary_min.numeric' => __('validation.numeric', ['attribute' => 'minimum salary']),
            'salary_min.min' => __('validation.min.numeric', ['attribute' => 'minimum salary', 'min' => 0]),
            'salary_max.numeric' => __('validation.numeric', ['attribute' => 'maximum salary']),
            'salary_max.min' => __('validation.min.numeric', ['attribute' => 'maximum salary', 'min' => 0]),
            'salary_max.gte' => __('validation.gte.numeric', ['attribute' => 'maximum salary', 'other' => 'minimum salary']),
            'salary_period_id.exists' => __('validation.exists', ['attribute' => 'salary period']),
            'experience_level_id.required' => __('validation.required', ['attribute' => 'experience level']),
            'experience_level_id.exists' => __('validation.exists', ['attribute' => 'experience level']),
            'education_level_id.required' => __('validation.required', ['attribute' => 'education level']),
            'education_level_id.exists' => __('validation.exists', ['attribute' => 'education level']),
            'application_deadline.required' => __('validation.required', ['attribute' => 'application deadline']),
            'application_deadline.date' => __('validation.date', ['attribute' => 'application deadline']),
            'application_deadline.after' => __('validation.after', ['attribute' => 'application deadline', 'date' => 'today']),
            'status.required' => __('validation.required', ['attribute' => 'status']),
            'status.in' => __('validation.in', ['attribute' => 'status']),
        ];
    }
}
