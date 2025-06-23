<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobTypeResource extends JsonResource
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
            'description' => $this->description,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'color' => $this->color,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,

            // SEO fields (conditional)
            'meta_title' => $this->when($request->user()?->can('view-seo', $this->resource), $this->meta_title),
            'meta_description' => $this->when($request->user()?->can('view-seo', $this->resource), $this->meta_description),

            // Statistics (conditional)
            'usage_count' => $this->when($request->has('include_stats'), $this->usage_count),
            'formatted_usage_stats' => $this->when($request->has('include_stats'), $this->formatted_usage_stats),

            // Relationships (conditional)
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),
            'active_jobs' => JobResource::collection($this->whenLoaded('activeJobs')),
            'jobs_count' => $this->when($request->has('include_counts'), $this->jobs_count),
            'active_jobs_count' => $this->when($request->has('include_counts'), $this->jobs()->where('is_active', true)->count()),

            // Related data
            'related_types' => $this->when($request->has('include_related'), function () {
                return JobTypeResource::collection($this->getRelatedTypes(5));
            }),

            // Helper attributes
            'is_high_demand' => $this->when($request->has('include_analysis'), $this->isHighDemand()),
            'is_full_time' => $this->when($request->has('include_analysis'), $this->isFullTime()),
            'is_part_time' => $this->when($request->has('include_analysis'), $this->isPartTime()),
            'is_remote' => $this->when($request->has('include_analysis'), $this->isRemote()),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),

            // Links
            'links' => [
                'self' => route('api.job-types.show', $this->id),
                'jobs' => route('api.jobs.index', ['job_type_id' => $this->id]),
                'edit' => $this->when($request->user()?->can('update', $this->resource), route('job-types.edit', $this->id)),
                'delete' => $this->when($request->user()?->can('delete', $this->resource), route('api.job-types.destroy', $this->id)),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
                'locale' => app()->getLocale(),
            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->header('X-Resource-Type', 'JobType');
        $response->header('X-Resource-Version', '1.0');
    }
}
