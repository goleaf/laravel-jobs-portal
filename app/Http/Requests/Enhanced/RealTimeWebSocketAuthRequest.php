<?php

declare(strict_types=1);

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RealTimeWebSocketAuthRequest
 * Enterprise-grade validation for Enhanced RealTime WebSocket authentication operations
 */
class RealTimeWebSocketAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            'channel' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\.]+$/',
                'in:user_dashboard,company_dashboard,admin_dashboard,notifications,real_time_updates',
            ],
            'user_type' => [
                'sometimes',
                'string',
                'in:candidate,employer,admin',
            ],
            'user_id' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'session_token' => [
                'sometimes',
                'string',
                'min:32',
                'max:255',
                'regex:/^[a-zA-Z0-9\-_\.]+$/',
            ],
            'connection_id' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_]+$/',
            ],
            'permissions' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'permissions.*' => [
                'string',
                'in:read,write,admin,dashboard_view,notifications_view,real_time_updates',
            ],
            'client_info' => [
                'sometimes',
                'array',
            ],
            'client_info.browser' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'client_info.platform' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'client_info.version' => [
                'sometimes',
                'string',
                'max:20',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'channel.required' => __('validation.custom.realtime.channel_required'),
            'channel.in' => __('validation.custom.realtime.channel_invalid'),
            'user_type.in' => __('validation.custom.realtime.user_type_invalid'),
            'session_token.min' => __('validation.custom.realtime.token_too_short'),
            'permissions.max' => __('validation.custom.realtime.permissions_limit'),
            'permissions.*.in' => __('validation.custom.realtime.permission_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('channel')) {
            $this->merge(['channel' => strtolower(trim($this->channel))]);
        }

        if ($this->has('user_type')) {
            $this->merge(['user_type' => strtolower(trim($this->user_type))]);
        }
    }
}
