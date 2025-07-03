<?php

declare(strict_types=1);

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class BroadcastMessageRequest
 * Enterprise-grade validation for Enhanced broadcast message operations
 */
class BroadcastMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'message' => [
                'required',
                'string',
                'min:1',
                'max:1000',
            ],
            'message_type' => [
                'required',
                'string',
                'in:notification,alert,system_message,announcement,warning,error',
            ],
            'target_channels' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],
            'target_channels.*' => [
                'string',
                'in:user_dashboard,company_dashboard,admin_dashboard,global,notifications',
            ],
            'priority' => [
                'sometimes',
                'string',
                'in:low,medium,high,critical',
            ],
            'expires_at' => [
                'sometimes',
                'date',
                'after:now',
            ],
            'target_users' => [
                'sometimes',
                'array',
                'max:1000',
            ],
            'target_users.*' => [
                'integer',
                'min:1',
            ],
            'target_companies' => [
                'sometimes',
                'array',
                'max:100',
            ],
            'target_companies.*' => [
                'integer',
                'min:1',
            ],
            'metadata' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'metadata.title' => [
                'sometimes',
                'string',
                'max:200',
            ],
            'metadata.action_url' => [
                'sometimes',
                'url',
                'max:255',
            ],
            'metadata.icon' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'require_acknowledgment' => [
                'sometimes',
                'boolean',
            ],
            'persist_message' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => __('validation.custom.broadcast.message_required'),
            'message_type.required' => __('validation.custom.broadcast.type_required'),
            'message_type.in' => __('validation.custom.broadcast.type_invalid'),
            'target_channels.required' => __('validation.custom.broadcast.channels_required'),
            'target_channels.min' => __('validation.custom.broadcast.channels_min'),
            'target_channels.*.in' => __('validation.custom.broadcast.channel_invalid'),
            'priority.in' => __('validation.custom.broadcast.priority_invalid'),
            'expires_at.after' => __('validation.custom.broadcast.expiry_past'),
            'target_users.max' => __('validation.custom.broadcast.users_limit'),
            'target_companies.max' => __('validation.custom.broadcast.companies_limit'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('message')) {
            $this->merge(['message' => trim($this->message)]);
        }

        if (! $this->has('priority')) {
            $this->merge(['priority' => 'medium']);
        }

        if ($this->has('target_channels') && is_string($this->input('target_channels'))) {
            $this->merge(['target_channels' => explode(',', $this->input('target_channels'))]);
        }
    }
}
