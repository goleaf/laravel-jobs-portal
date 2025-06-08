<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Universal Company Resource
 * Implements MCP best practices for API responses
 */
class CompanyResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'website' => $this->website,
            'email' => $this->email,
            'location' => $this->location,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Universal Pattern: Conditional relationships (prevents N+1)
            'jobs' => $this->whenLoaded('jobs'),
            'user' => $this->whenLoaded('user'),

            // Universal Pattern: Role-based conditional fields
            'private_notes' => $this->when($request->user() && $request->user()->isAdmin(), $this->private_notes),

            // Universal Pattern: Consistent links
            'links' => [
                'self' => route('companys.show', $this->id),
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
                'resource_type' => 'company'
            ],
        ];
    }
}