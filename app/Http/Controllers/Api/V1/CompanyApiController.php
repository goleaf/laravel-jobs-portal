<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Universal\StoreRequest;
use App\Http\Requests\Api\Universal\UpdateRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for Company Management
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class CompanyApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Company::query();

            // Apply search filter
            if ($request->has('search') && ! empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('website', 'like', "%{$search}%");
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
            $data = $query->withCount('jobs')->paginate($perPage);

            // Transform data
            $companies = $data->getCollection()->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,
                    'phone' => $company->phone,
                    'website' => $company->website,
                    'address' => $company->address,
                    'city' => $company->city,
                    'state' => $company->state,
                    'country' => $company->country,
                    'location' => $company->location,
                    'logo' => $company->logo,
                    'is_active' => $company->is_active,
                    'jobs_count' => $company->jobs_count ?? 0,
                    'established_in' => $company->established_in,
                    'created_at' => $company->created_at?->toISOString(),
                    'updated_at' => $company->updated_at?->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Companies retrieved successfully',
                'data' => $companies,
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve companies',
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

            // Create company with validated data
            $company = Company::create([
                'name' => $data['name'] ?? 'New Company',
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'website' => $data['website'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'location' => $data['location'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'established_in' => $data['established_in'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Company created successfully',
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,
                    'is_active' => $company->is_active,
                    'created_at' => $company->created_at?->toISOString(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create company',
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
            $company = Company::withCount('jobs')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Company retrieved successfully',
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,
                    'phone' => $company->phone,
                    'website' => $company->website,
                    'address' => $company->address,
                    'city' => $company->city,
                    'state' => $company->state,
                    'country' => $company->country,
                    'location' => $company->location,
                    'logo' => $company->logo,
                    'is_active' => $company->is_active,
                    'jobs_count' => $company->jobs_count ?? 0,
                    'established_in' => $company->established_in,
                    'created_at' => $company->created_at?->toISOString(),
                    'updated_at' => $company->updated_at?->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found',
                'error' => config('app.debug') ? $e->getMessage() : 'Company not found',
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
            $company = Company::findOrFail($id);
            $data = $request->validated();

            // Update company with validated data
            $updateData = [];
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (isset($data['email'])) {
                $updateData['email'] = $data['email'];
            }
            if (isset($data['phone'])) {
                $updateData['phone'] = $data['phone'];
            }
            if (isset($data['website'])) {
                $updateData['website'] = $data['website'];
            }
            if (isset($data['is_active'])) {
                $updateData['is_active'] = $data['is_active'];
            }

            $company->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully',
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,
                    'is_active' => $company->is_active,
                    'updated_at' => $company->updated_at?->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update company',
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
            $company = Company::findOrFail($id);
            $companyName = $company->name;

            // Check if company has jobs
            $jobsCount = $company->jobs()->count();
            if ($jobsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete company '{$companyName}' because it has {$jobsCount} associated jobs",
                ], 422);
            }

            $company->delete();

            return response()->json([
                'success' => true,
                'message' => "Company '{$companyName}' deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete company',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
