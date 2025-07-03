<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\MasterData\MasterDataRequest;

class TaxonomyDestroyRequest extends MasterDataRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:taxonomies,id'],
            'confirm_deletion' => ['required', 'boolean', 'accepted'],
            'deletion_reason' => ['required', 'string', 'min:10', 'max:500'],
            'force_deletion' => ['sometimes', 'boolean'],
            'cascade_delete_terms' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => __('validation.admin.taxonomy.id_required'),
            'id.exists' => __('validation.admin.taxonomy.taxonomy_not_found'),
            'confirm_deletion.required' => __('validation.admin.taxonomy.deletion_confirmation_required'),
            'confirm_deletion.accepted' => __('validation.admin.taxonomy.deletion_must_be_confirmed'),
            'deletion_reason.required' => __('validation.admin.taxonomy.deletion_reason_required'),
            'deletion_reason.min' => __('validation.admin.taxonomy.deletion_reason_too_short'),
        ];
    }
}
