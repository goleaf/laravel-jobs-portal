<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Universal Skill Resource
 * Implements MCP best practices for API responses
 */
class SkillResource extends JsonResource
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
            'description' => $this->description,
            'created_at' => $this->created_at,
            
            // Universal Pattern: Conditional relationships (prevents N+1)

            // Universal Pattern: Role-based conditional fields

            // Universal Pattern: Consistent links
            'links' => [
                'self' => route('skills.show', $this->id),
            ],
        ];
    }

    /**
     * Universal Pattern: Add metadata to the response
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'resource_type' => 'skill'
            ],
        ];
    }
}