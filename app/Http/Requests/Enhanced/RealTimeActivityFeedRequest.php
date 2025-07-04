<?php

declare(strict_types=1);

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RealTimeActivityFeedRequest
 * Enterprise-grade validation for Enhanced RealTime activity feed operations
 */
class RealTimeActivityFeedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'feed_type' => [
                'sometimes',
                'string',
                'in:user,company,global,admin,notifications',
            ],
            'activity_types' => [
                'sometimes',
                'array',
                'max:15',
            ],
            'activity_types.*' => [
                'string',
                'in:job_posted,application_received,profile_updated,login,logout,data_export,system_alert',
            ],
            'from_date' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],
            'to_date' => [
                'sometimes',
                'date',
                'after_or_equal:from_date',
                'before_or_equal:today',
            ],
            'limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'offset' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'real_time' => [
                'sometimes',
                'boolean',
            ],
            'include_metadata' => [
                'sometimes',
                'boolean',
            ],
            'priority_filter' => [
                'sometimes',
                'string',
                'in:low,medium,high,critical',
            ],
            'user_context' => [
                'sometimes',
                'array',
            ],
            'user_context.user_id' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'user_context.company_id' => [
                'sometimes',
                'integer',
                'min:1',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'feed_type' => 'user',
            'limit' => 50,
            'offset' => 0,
            'real_time' => true,
            'include_metadata' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'feed_type.in' => __('validation.custom.activity.feed_type_invalid'),
            'activity_types.max' => __('validation.custom.activity.types_limit'),
            'activity_types.*.in' => __('validation.custom.activity.type_invalid'),
            'to_date.after_or_equal' => __('validation.custom.activity.date_range_invalid'),
            'limit.max' => __('validation.custom.activity.limit_exceeded'),
            'priority_filter.in' => __('validation.custom.activity.priority_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('activity_types') && is_string($this->input('activity_types'))) {
            $this->merge(['activity_types' => explode(',', $this->input('activity_types'))]);
        }
    }
}
