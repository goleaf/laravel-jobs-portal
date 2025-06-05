<?php

namespace App\Http\Resources\Context7;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Context7 Job Resource
 * Implements MCP best practices for API responses
 */
class JobResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'salary_from' => $this->salary_from,
            'salary_to' => $this->salary_to,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Context7 Pattern: Conditional relationships (prevents N+1)
            'company' => $this->whenLoaded('company'),
            'category' => $this->whenLoaded('category'),
            'skills' => $this->whenLoaded('skills'),
            'applications_count' => $this->whenLoaded('applications_count'),

            // Context7 Pattern: Role-based conditional fields
            'internal_notes' => $this->when($request->user() && $request->user()->isEmployerOrAdmin(), $this->internal_notes),

            // Context7 Pattern: Consistent links
            'links' => [
                'self' => route('jobs.show', $this->id),
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
                'resource_type' => 'job'
            ],
        ];
    }
}