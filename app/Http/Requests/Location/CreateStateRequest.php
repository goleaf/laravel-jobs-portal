<?php

declare(strict_types=1);

namespace App\Http\Requests\Location;

use App\Models\State;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CreateStateRequest
 * Enterprise-grade validation for creating a new State
 */
class CreateStateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authentication-free system, adjust if authorization is needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('states', 'name')->where(function ($query) {
                    return $query->where('country_id', $this->input('country_id'));
                }),
            ],
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.state.name_required'),
            'name.unique' => __('validation.custom.state.name_unique'),
            'country_id.required' => __('validation.custom.state.country_required'),
            'country_id.exists' => __('validation.custom.state.country_exists'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->input('name'))]);
        }
    }
}
