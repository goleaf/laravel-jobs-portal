<?php

namespace App\Http\Resources\Context7;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Context7 Skill Resource
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
            
            // Context7 Pattern: Conditional relationships (prevents N+1)

            // Context7 Pattern: Role-based conditional fields

            // Context7 Pattern: Consistent links
            'links' => [
                'self' => route('skills.show', $this->id),
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
                'resource_type' => 'skill'
            ],
        ];
    }
}