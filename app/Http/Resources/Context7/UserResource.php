<?php

namespace App\Http\Resources\Context7;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Context7 User Resource
 * Implements MCP best practices for API responses
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
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
            
            // Context7 Pattern: Conditional relationships (prevents N+1)
            'candidate' => $this->whenLoaded('candidate'),
            'employer' => $this->whenLoaded('employer'),

            // Context7 Pattern: Role-based conditional fields
            'email_verified_at' => $this->when($request->user() && $request->user()->isAdmin(), $this->email_verified_at),

            // Context7 Pattern: Consistent links
            'links' => [
                'self' => route('users.show', $this->id),
            ],
        ];
    }

    /**
     * Context7 Pattern: Add metadata to the response
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'resource_type' => 'user'
            ],
        ];
    }
}