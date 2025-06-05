<?php

namespace App\Http\Resources\Context7;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Context7 JobApplication Resource
 * Implements MCP best practices for API responses
 */
class JobApplicationResource extends JsonResource
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
            'expected_salary' => $this->expected_salary,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Context7 Pattern: Conditional relationships (prevents N+1)
            'job' => $this->whenLoaded('job'),
            'candidate' => $this->whenLoaded('candidate'),
            'resume' => $this->whenLoaded('resume'),

            // Context7 Pattern: Role-based conditional fields
            'employer_notes' => $this->when($request->user() && $request->user()->isEmployer(), $this->employer_notes),

            // Context7 Pattern: Consistent links
            'links' => [
                'self' => route('jobapplications.show', $this->id),
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
                'resource_type' => 'jobapplication'
            ],
        ];
    }
}