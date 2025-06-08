<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'logout' => [
                'successful' => true,
                'logout_time' => now()->toISOString(),
                'revoked_token' => $this->resource['revoked_token'] ?? true,
            ],
            'session' => [
                'terminated' => true,
                'remaining_tokens' => $this->resource['remaining_tokens'] ?? 0,
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => __('auth.logout_successful'),
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'endpoint' => 'auth/logout',
            ],
        ];
    }

    /**
     * Customize the response for the resource.
     */
    public function withResponse(Request $request, $response): void
    {
        $response->setStatusCode(200);
    }
} 