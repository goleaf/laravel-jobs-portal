<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobType\StoreJobTypeRequest;
use App\Http\Requests\JobType\UpdateJobTypeRequest;
use App\Http\Resources\JobTypeResource;
use App\Models\JobType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class JobTypeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Authentication middleware removed - all methods now public
        $this->middleware('throttle:api')->only(['store', 'update', 'destroy']);
        $this->middleware('can:view,App\Models\JobType')->only(['index', 'show']);
        $this->middleware('can:create,App\Models\JobType')->only(['store']);
        $this->middleware('can:update,job_type')->only(['update']);
        $this->middleware('can:delete,job_type')->only(['destroy']);
    }

    /**
     * Display a listing of job types.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $cacheKey = 'job_types_index_'.md5($request->getQueryString() ?? '');

        $jobTypes = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = JobType::query();

            // Apply filters
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            if ($request->filled('status')) {
                'active' === $request->status
                    ? $query->active()
                    : $query->inactive();
            }

            if ($request->filled('is_default')) {
                $request->boolean('is_default')
                    ? $query->default()
                    : $query->custom();
            }

            if ($request->filled('is_featured')) {
                $query->where('is_featured', $request->boolean('is_featured'));
            }

            if ($request->filled('type')) {
                match ($request->type) {
                    'full_time' => $query->fullTime(),
                    'part_time' => $query->partTime(),
                    'contract' => $query->contract(),
                    'temporary' => $query->temporary(),
                    'internship' => $query->internship(),
                    'freelance' => $query->freelance(),
                    'remote' => $query->remote(),
                    default => null,
                };
            }

            // Apply sorting
            match ($request->get('sort', 'name')) {
                'name' => $query->alphabetical(),
                'popularity' => $query->popular(),
                'recent' => $query->recent(),
                'trending' => $query->trending(),
                'usage' => $query->withCount('jobs')->orderByDesc('jobs_count'),
                default => $query->orderBy('sort_order')->orderBy('name'),
            };

            // Include relationships
            if ($request->has('include_jobs')) {
                $query->with(['jobs' => fn ($q) => $q->active()->latest()->limit(10)]);
            }

            if ($request->has('include_counts')) {
                $query->withCount(['jobs', 'jobs as active_jobs_count' => fn ($q) => $q->where('is_active', true)]);
            }

            return $query->paginate($request->get('per_page', 15));
        });

        return JobTypeResource::collection($jobTypes);
    }

    /**
     * Store a newly created job type.
     */
    public function store(StoreJobTypeRequest $request): JsonResource
    {
        $jobType = JobType::create($request->validated());

        // Clear related caches
        Cache::tags(['job_types'])->flush();

        // Log activity
        activity()
            ->performedOn($jobType)
            ->causedBy($request->user())
            ->log('Job type created')
        ;

        return new JobTypeResource($jobType);
    }

    /**
     * Display the specified job type.
     */
    public function show(Request $request, JobType $jobType): JobTypeResource
    {
        $cacheKey = "job_type_{$jobType->id}_show_".md5($request->getQueryString() ?? '');

        $jobType = Cache::remember($cacheKey, 600, function () use ($request, $jobType) {
            // Load relationships based on request
            $with = [];

            if ($request->has('include_jobs')) {
                $with['jobs'] = fn ($q) => $q->active()->latest()->limit($request->get('jobs_limit', 10));
            }

            if ($request->has('include_related')) {
                // Related types will be loaded in the resource
            }

            if (!empty($with)) {
                $jobType->load($with);
            }

            return $jobType;
        });

        return new JobTypeResource($jobType);
    }

    /**
     * Update the specified job type.
     */
    public function update(UpdateJobTypeRequest $request, JobType $jobType): JobTypeResource
    {
        $jobType->update($request->validated());

        // Clear related caches
        Cache::forget("job_type_{$jobType->id}");
        Cache::tags(['job_types', "job_type-{$jobType->id}"])->flush();

        // Log activity
        activity()
            ->performedOn($jobType)
            ->causedBy($request->user())
            ->log('Job type updated')
        ;

        return new JobTypeResource($jobType->fresh());
    }

    /**
     * Remove the specified job type.
     */
    public function destroy(Request $request, JobType $jobType): JsonResponse
    {
        // Check if job type is being used
        if ($jobType->jobs()->exists()) {
            return response()->json([
                'message' => __('job_type.errors.cannot_delete_in_use'),
                'errors' => [
                    'jobs_count' => $jobType->jobs()->count(),
                ],
            ], 422);
        }

        // Log activity before deletion
        activity()
            ->performedOn($jobType)
            ->causedBy($request->user())
            ->log('Job type deleted')
        ;

        $jobType->delete();

        // Clear related caches
        Cache::tags(['job_types', "job_type-{$jobType->id}"])->flush();

        return response()->json([
            'message' => __('job_type.messages.deleted_successfully'),
        ]);
    }

    /**
     * Get job type statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', JobType::class);

        $cacheKey = 'job_types_statistics';

        $stats = Cache::remember($cacheKey, 3600, function () {
            return [
                'total' => JobType::count(),
                'active' => JobType::active()->count(),
                'inactive' => JobType::inactive()->count(),
                'default' => JobType::default()->count(),
                'custom' => JobType::custom()->count(),
                'featured' => JobType::where('is_featured', true)->count(),
                'with_jobs' => JobType::withJobs()->count(),
                'high_demand' => JobType::highDemand()->count(),
                'popular' => JobType::popular(10)->get(['id', 'name', 'jobs_count']),
                'trending' => JobType::trending()->limit(5)->get(['id', 'name']),
                'recent' => JobType::recent(30)->count(),
            ];
        });

        return response()->json([
            'data' => $stats,
            'meta' => [
                'generated_at' => now()->toISOString(),
                'cache_ttl' => 3600,
            ],
        ]);
    }

    /**
     * Bulk update job types.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        Gate::authorize('update', JobType::class);

        $request->validate([
            'job_type_ids' => 'required|array|min:1',
            'job_type_ids.*' => 'exists:job_types,id',
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
        ]);

        $jobTypes = JobType::whereIn('id', $request->job_type_ids)->get();
        $updatedCount = 0;

        foreach ($jobTypes as $jobType) {
            match ($request->action) {
                'activate' => $jobType->update(['is_active' => true]),
                'deactivate' => $jobType->update(['is_active' => false]),
                'feature' => $jobType->update(['is_featured' => true]),
                'unfeature' => $jobType->update(['is_featured' => false]),
                'delete' => $jobType->jobs()->exists() ?: $jobType->delete(),
            };
            ++$updatedCount;
        }

        // Clear caches
        Cache::tags(['job_types'])->flush();

        // Log bulk activity
        activity()
            ->causedBy($request->user())
            ->log("Bulk {$request->action} applied to {$updatedCount} job types")
        ;

        return response()->json([
            'message' => __('job_type.messages.bulk_updated', ['count' => $updatedCount]),
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Search job types with advanced filters.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'filters' => 'array',
            'filters.status' => 'in:active,inactive',
            'filters.type' => 'in:default,custom,featured',
            'filters.demand' => 'in:high,medium,low',
        ]);

        $cacheKey = 'job_types_search_'.md5($request->getQueryString());

        $jobTypes = Cache::remember($cacheKey, 600, function () use ($request) {
            $query = JobType::search($request->q);

            // Apply additional filters
            if ($request->has('filters.status')) {
                'active' === $request->input('filters.status')
                    ? $query->active()
                    : $query->inactive();
            }

            if ($request->has('filters.type')) {
                match ($request->input('filters.type')) {
                    'default' => $query->default(),
                    'custom' => $query->custom(),
                    'featured' => $query->where('is_featured', true),
                };
            }

            if ($request->has('filters.demand')) {
                match ($request->input('filters.demand')) {
                    'high' => $query->highDemand(50),
                    'medium' => $query->minUsage(10)->withCount('jobs')->having('jobs_count', '<', 50),
                    'low' => $query->minUsage(1)->withCount('jobs')->having('jobs_count', '<', 10),
                };
            }

            return $query
                ->withCount('jobs')
                ->orderByDesc('jobs_count')
                ->paginate($request->get('per_page', 10))
            ;
        });

        return JobTypeResource::collection($jobTypes);
    }
}
