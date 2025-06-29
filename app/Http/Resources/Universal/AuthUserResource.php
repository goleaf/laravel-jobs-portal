<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
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
                'id' => $this->id,
                'name' => $this->name,
                'first_name' => $this->first_name ?? null,
                'last_name' => $this->last_name ?? null,
                'email' => $this->email,
                'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toISOString() : null,
                'phone' => $this->phone ?? null,
                'avatar' => $this->avatar ?? null,
                'is_active' => $this->is_active ?? true,
                'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
                'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
            ],
            'token_abilities' => $this->currentAccessToken()?->abilities ?? [],
            'authentication' => [
                'token_name' => $this->currentAccessToken()?->name ?? 'Unknown',
                'token_created_at' => $this->currentAccessToken() && $this->currentAccessToken()->created_at ? $this->currentAccessToken()->created_at->toISOString() : null,
                'last_used_at' => $this->currentAccessToken() && $this->currentAccessToken()->last_used_at ? $this->currentAccessToken()->last_used_at->toISOString() : null,
            ],
            'profile' => [
                'is_complete' => $this->isProfileComplete(),
                'completion_percentage' => $this->getProfileCompletionPercentage(),
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
            'message' => __('auth.user_retrieved'),
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'endpoint' => 'auth/user',
            ],
        ];
    }

    /**
     * Customize the response for the resource.
     *
     * @param mixed $response
     */
    public function withResponse(Request $request, $response): void
    {
        $response->setStatusCode(200);
    }

    /**
     * Check if user profile is complete (helper method).
     */
    private function isProfileComplete(): bool
    {
        $requiredFields = ['name', 'email'];

        foreach ($requiredFields as $field) {
            if (empty($this->{$field})) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get profile completion percentage (helper method).
     */
    private function getProfileCompletionPercentage(): int
    {
        $fields = ['name', 'email', 'phone', 'first_name', 'last_name'];
        $completedFields = 0;

        foreach ($fields as $field) {
            if (!empty($this->{$field})) {
                ++$completedFields;
            }
        }

        return (int) round(($completedFields / count($fields)) * 100);
    }
}
