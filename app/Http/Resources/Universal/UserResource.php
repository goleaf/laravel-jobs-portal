<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Universal User Resource
 * Implements MCP best practices for API responses.
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role_name' => $this->role_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Universal Pattern: Conditional relationships (prevents N+1)
            'candidate' => $this->whenLoaded('candidate'),
            'employer' => $this->whenLoaded('employer'),

            // Universal Pattern: Role-based conditional fields
            'email_verified_at' => $this->when($request->user() && $request->user()->isAdmin(), $this->email_verified_at),

            // Universal Pattern: Consistent links
            'links' => [
                'self' => route('users.show', $this->id),
            ],
        ];
    }

    /**
     * Universal Pattern: Add metadata to the response.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'resource_type' => 'user',
            ],
        ];
    }
}
