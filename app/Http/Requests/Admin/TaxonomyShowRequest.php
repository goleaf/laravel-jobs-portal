<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\MasterData\MasterDataRequest;

class TaxonomyShowRequest extends MasterDataRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:taxonomies,id'],
            'include_usage_stats' => ['sometimes', 'boolean'],
            'include_terms' => ['sometimes', 'boolean'],
            'terms_limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => __('validation.admin.taxonomy.id_required'),
            'id.exists' => __('validation.admin.taxonomy.taxonomy_not_found'),
            'terms_limit.max' => __('validation.admin.taxonomy.terms_limit_too_large'),
        ];
    }
}
