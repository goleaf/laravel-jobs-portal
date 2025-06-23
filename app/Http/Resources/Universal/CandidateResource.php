<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Universal Candidate Resource
 * Implements MCP best practices for API responses.
 */
class CandidateResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'experience_level' => $this->experience_level,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Universal Pattern: Conditional relationships (prevents N+1)
            'user' => $this->whenLoaded('user'),
            'resumes' => $this->whenLoaded('resumes'),
            'applications' => $this->whenLoaded('applications'),

            // Universal Pattern: Role-based conditional fields
            'salary_expectation' => $this->when($request->user() && $request->user()->isEmployerOrAdmin(), $this->salary_expectation),

            // Universal Pattern: Consistent links
            'links' => [
                'self' => route('candidates.show', $this->id),
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
                'resource_type' => 'candidate',
            ],
        ];
    }
}
