<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->resource['user']['id'],
                'name' => $this->resource['user']['name'],
                'email' => $this->resource['user']['email'],
                'created_at' => $this->resource['user']['created_at'] ?? null,
                'updated_at' => $this->resource['user']['updated_at'] ?? null,
            ],
            'token' => [
                'access_token' => $this->resource['token'],
                'token_type' => 'Bearer',
                'abilities' => $this->resource['abilities'] ?? [],
                'expires_at' => null, // Sanctum tokens don't expire by default
            ],
            'authentication' => [
                'authenticated' => true,
                'login_time' => now()->toISOString(),
                'device_name' => $this->resource['device_name'] ?? 'Unknown Device',
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
            'message' => __('auth.login_successful'),
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'endpoint' => 'auth/login',
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