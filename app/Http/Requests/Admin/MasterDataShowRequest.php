<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\MasterData\MasterDataRequest;
use Illuminate\Validation\Rule;

class MasterDataShowRequest extends MasterDataRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'min:1'],
            'category' => ['required', 'string', Rule::in([
                'countries', 'states', 'cities', 'skills', 'industries',
                'company_sizes', 'functional_areas', 'career_levels',
            ])],
            'include_usage_stats' => ['sometimes', 'boolean'],
            'include_relationships' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => __('validation.admin.master_data.id_required'),
            'category.required' => __('validation.admin.master_data.category_required'),
            'category.in' => __('validation.admin.master_data.invalid_category'),
        ];
    }
}
