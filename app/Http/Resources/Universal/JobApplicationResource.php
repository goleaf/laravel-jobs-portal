<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Universal JobApplication Resource
 * Implements MCP best practices for API responses.
 */
class JobApplicationResource extends JsonResource
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
            'expected_salary' => $this->expected_salary,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Universal Pattern: Conditional relationships (prevents N+1)
            'job' => $this->whenLoaded('job'),
            'candidate' => $this->whenLoaded('candidate'),
            'resume' => $this->whenLoaded('resume'),

            // Universal Pattern: Role-based conditional fields
            'employer_notes' => $this->when($request->user() && $request->user()->isEmployer(), $this->employer_notes),

            // Universal Pattern: Consistent links
            'links' => [
                'self' => route('jobapplications.show', $this->id),
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
                'resource_type' => 'jobapplication',
            ],
        ];
    }
}
