<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Universal Job Resource
 * Implements MCP best practices for API responses.
 */
class JobResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'salary_from' => $this->salary_from,
            'salary_to' => $this->salary_to,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Universal Pattern: Conditional relationships (prevents N+1)
            'company' => $this->whenLoaded('company'),
            'category' => $this->whenLoaded('category'),
            'skills' => $this->whenLoaded('skills'),
            'applications_count' => $this->whenLoaded('applications_count'),

            // Universal Pattern: Role-based conditional fields
            'internal_notes' => $this->when($request->user() && $request->user()->isEmployerOrAdmin(), $this->internal_notes),

            // Universal Pattern: Consistent links
            'links' => [
                'self' => route('jobs.show', $this->id),
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
                'resource_type' => 'job',
            ],
        ];
    }
}
