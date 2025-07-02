<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class BulkActionSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', \App\Models\Skill::class) || Gate::allows('delete', \App\Models\Skill::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'action' => 'required|string|in:activate,deactivate,delete',
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'integer|exists:skills,id',
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
            'action.required' => __('validation.required', ['attribute' => 'action']),
            'action.in' => __('validation.in', ['attribute' => 'action']),
            'skill_ids.required' => __('validation.required', ['attribute' => 'skill IDs']),
            'skill_ids.array' => __('validation.array', ['attribute' => 'skill IDs']),
            'skill_ids.min' => __('validation.min.array', ['attribute' => 'skill IDs', 'min' => 1]),
            'skill_ids.*.integer' => __('validation.integer', ['attribute' => 'skill ID']),
            'skill_ids.*.exists' => __('validation.exists', ['attribute' => 'skill ID']),
        ];
    }
} 