<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SaveFavouriteJobRequest
 * Enterprise-grade validation for saving favorite jobs
 */
class SaveFavouriteJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public access for job favorites
    }

    public function rules(): array
    {
        return [
            'job_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('jobs', 'id')->where('status', 'open'),
            ],
            'action' => [
                'sometimes',
                'string',
                'in:add,remove,toggle',
            ],
            'collection_id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('favorite_collections', 'id'),
            ],
            'notes' => [
                'sometimes',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.required' => __('validation.custom.favorite.job_required'),
            'job_id.exists' => __('validation.custom.favorite.job_not_found'),
            'action.in' => __('validation.custom.favorite.action_invalid'),
            'collection_id.exists' => __('validation.custom.favorite.collection_not_found'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('action')) {
            $this->merge(['action' => 'toggle']);
        }

        if ($this->has('notes')) {
            $this->merge(['notes' => trim($this->notes)]);
        }
    }
}
