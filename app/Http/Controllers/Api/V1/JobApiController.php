<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Universal\StoreRequest;
use App\Http\Requests\Api\Universal\UpdateRequest;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for Job Management
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class JobApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Job::with(['company:id,name', 'jobCategory:id,name', 'jobType:id,name']);

            // Apply search filter
            if ($request->has('search') && ! empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($company) use ($search) {
                            $company->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Apply status filter
            if ($request->has('status') && $request->status !== '') {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            // Apply sorting
            $sortBy = $request->get('sort', 'created_at');
            $order = $request->get('order', 'desc');
            $query->orderBy($sortBy, $order);

            // Pagination
            $perPage = min($request->integer('per_page', 15), 100);
            $data = $query->paginate($perPage);

            // Transform data
            $jobs = $data->getCollection()->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'description' => $job->description,
                    'company' => [
                        'id' => $job->company?->id,
                        'name' => $job->company?->name ?? 'N/A',
                    ],
                    'category' => [
                        'id' => $job->jobCategory?->id,
                        'name' => $job->jobCategory?->name ?? 'N/A',
                    ],
                    'type' => [
                        'id' => $job->jobType?->id,
                        'name' => $job->jobType?->name ?? 'Full-time',
                    ],
                    'location' => $job->location,
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to,
                    'experience' => $job->experience,
                    'is_active' => $job->is_active,
                    'is_featured' => $job->is_featured ?? false,
                    'applications_count' => $job->applications_count ?? 0,
                    'created_at' => $job->created_at?->toISOString(),
                    'updated_at' => $job->updated_at?->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Jobs retrieved successfully',
                'data' => $jobs,
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve jobs',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => [],
            ], 500);
        }
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Create job with validated data
            $job = Job::create([
                'title' => $data['name'] ?? $data['title'] ?? 'Untitled Job',
                'description' => $data['description'] ?? '',
                'company_id' => $data['company_id'] ?? null,
                'job_category_id' => $data['category_id'] ?? null,
                'job_type_id' => $data['type_id'] ?? null,
                'location' => $data['location'] ?? '',
                'salary_from' => $data['salary_from'] ?? null,
                'salary_to' => $data['salary_to'] ?? null,
                'experience' => $data['experience'] ?? '',
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
            ]);

            // Load relationships for response
            $job->load(['company:id,name', 'jobCategory:id,name', 'jobType:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Job created successfully',
                'data' => [
                    'id' => $job->id,
                    'title' => $job->title,
                    'description' => $job->description,
                    'company' => [
                        'id' => $job->company?->id,
                        'name' => $job->company?->name ?? 'N/A',
                    ],
                    'is_active' => $job->is_active,
                    'created_at' => $job->created_at?->toISOString(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create job',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     */
    public function show($id): JsonResponse
    {
        try {
            $job = Job::with(['company:id,name', 'jobCategory:id,name', 'jobType:id,name'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Job retrieved successfully',
                'data' => [
                    'id' => $job->id,
                    'title' => $job->title,
                    'description' => $job->description,
                    'company' => [
                        'id' => $job->company?->id,
                        'name' => $job->company?->name ?? 'N/A',
                    ],
                    'category' => [
                        'id' => $job->jobCategory?->id,
                        'name' => $job->jobCategory?->name ?? 'N/A',
                    ],
                    'type' => [
                        'id' => $job->jobType?->id,
                        'name' => $job->jobType?->name ?? 'Full-time',
                    ],
                    'location' => $job->location,
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to,
                    'experience' => $job->experience,
                    'is_active' => $job->is_active,
                    'is_featured' => $job->is_featured ?? false,
                    'created_at' => $job->created_at?->toISOString(),
                    'updated_at' => $job->updated_at?->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found',
                'error' => config('app.debug') ? $e->getMessage() : 'Job not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param  mixed  $id
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        try {
            $job = Job::findOrFail($id);
            $data = $request->validated();

            // Update job with validated data
            $updateData = [];
            if (isset($data['name']) || isset($data['title'])) {
                $updateData['title'] = $data['name'] ?? $data['title'];
            }
            if (isset($data['description'])) {
                $updateData['description'] = $data['description'];
            }
            if (isset($data['is_active'])) {
                $updateData['is_active'] = $data['is_active'];
            }
            if (isset($data['location'])) {
                $updateData['location'] = $data['location'];
            }

            $job->update($updateData);

            // Load relationships for response
            $job->load(['company:id,name', 'jobCategory:id,name', 'jobType:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Job updated successfully',
                'data' => [
                    'id' => $job->id,
                    'title' => $job->title,
                    'description' => $job->description,
                    'is_active' => $job->is_active,
                    'updated_at' => $job->updated_at?->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update job',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource.
     *
     * @param  mixed  $id
     */
    public function destroy($id): JsonResponse
    {
        try {
            $job = Job::findOrFail($id);
            $jobTitle = $job->title;

            $job->delete();

            return response()->json([
                'success' => true,
                'message' => "Job '{$jobTitle}' deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete job',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
