<?php

declare(strict_types=1);

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SystemHealthRequest
 * Enterprise-grade validation for Enhanced system health monitoring operations
 */
class SystemHealthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'check_types' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'check_types.*' => [
                'string',
                'in:database,redis,storage,queue,websocket,memory,cpu,disk,network',
            ],
            'include_metrics' => [
                'sometimes',
                'boolean',
            ],
            'detailed_report' => [
                'sometimes',
                'boolean',
            ],
            'time_range' => [
                'sometimes',
                'string',
                'in:1h,6h,24h,7d,30d',
            ],
            'alert_threshold' => [
                'sometimes',
                'string',
                'in:low,medium,high,critical',
            ],
            'format' => [
                'sometimes',
                'string',
                'in:json,xml,csv,html',
            ],
            'include_historical' => [
                'sometimes',
                'boolean',
            ],
            'cache_response' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'include_metrics' => true,
            'detailed_report' => false,
            'time_range' => '1h',
            'alert_threshold' => 'medium',
            'format' => 'json',
            'include_historical' => false,
            'cache_response' => true,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'check_types.max' => __('validation.custom.health.check_types_limit'),
            'check_types.*.in' => __('validation.custom.health.check_type_invalid'),
            'time_range.in' => __('validation.custom.health.time_range_invalid'),
            'alert_threshold.in' => __('validation.custom.health.threshold_invalid'),
            'format.in' => __('validation.custom.health.format_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('check_types') && is_string($this->input('check_types'))) {
            $this->merge(['check_types' => explode(',', $this->input('check_types'))]);
        }
    }
}
