<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Universal\StoreRequest;
use App\Http\Requests\Api\Universal\UpdateRequest;
use App\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for Candidate Management
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class CandidateApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Candidate::with(['user:id,email,email_verified_at']);

            // Apply search filter
            if ($request->has('search') && ! empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($user) use ($search) {
                            $user->where('email', 'like', "%{$search}%");
                        });
                });
            }

            // Apply verification status filter
            if ($request->has('verified') && $request->verified !== '') {
                if ($request->verified === 'true') {
                    $query->whereHas('user', function ($user) {
                        $user->whereNotNull('email_verified_at');
                    });
                } elseif ($request->verified === 'false') {
                    $query->whereHas('user', function ($user) {
                        $user->whereNull('email_verified_at');
                    });
                }
            }

            // Apply sorting
            $sortBy = $request->get('sort', 'created_at');
            $order = $request->get('order', 'desc');
            $query->orderBy($sortBy, $order);

            // Pagination
            $perPage = min($request->integer('per_page', 15), 100);
            $data = $query->withCount('applications')->paginate($perPage);

            // Transform data
            $candidates = $data->getCollection()->map(function ($candidate) {
                return [
                    'id' => $candidate->id,
                    'first_name' => $candidate->first_name,
                    'last_name' => $candidate->last_name,
                    'name' => trim($candidate->first_name.' '.$candidate->last_name),
                    'email' => $candidate->email ?? $candidate->user?->email,
                    'phone' => $candidate->phone,
                    'date_of_birth' => $candidate->date_of_birth,
                    'gender' => $candidate->gender,
                    'nationality' => $candidate->nationality,
                    'current_salary' => $candidate->current_salary,
                    'expected_salary' => $candidate->expected_salary,
                    'experience' => $candidate->experience,
                    'immediate_available' => $candidate->immediate_available,
                    'applications_count' => $candidate->applications_count ?? 0,
                    'is_verified' => $candidate->user?->email_verified_at !== null,
                    'created_at' => $candidate->created_at?->toISOString(),
                    'updated_at' => $candidate->updated_at?->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Candidates retrieved successfully',
                'data' => $candidates,
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve candidates',
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

            // Create candidate with validated data
            $candidate = Candidate::create([
                'first_name' => $data['first_name'] ?? $data['name'] ?? 'New',
                'last_name' => $data['last_name'] ?? 'Candidate',
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'current_salary' => $data['current_salary'] ?? null,
                'expected_salary' => $data['expected_salary'] ?? null,
                'experience' => $data['experience'] ?? null,
                'immediate_available' => $data['immediate_available'] ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Candidate created successfully',
                'data' => [
                    'id' => $candidate->id,
                    'name' => trim($candidate->first_name.' '.$candidate->last_name),
                    'email' => $candidate->email,
                    'phone' => $candidate->phone,
                    'created_at' => $candidate->created_at?->toISOString(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create candidate',
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
            $candidate = Candidate::with(['user:id,email,email_verified_at'])
                ->withCount('applications')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Candidate retrieved successfully',
                'data' => [
                    'id' => $candidate->id,
                    'first_name' => $candidate->first_name,
                    'last_name' => $candidate->last_name,
                    'name' => trim($candidate->first_name.' '.$candidate->last_name),
                    'email' => $candidate->email ?? $candidate->user?->email,
                    'phone' => $candidate->phone,
                    'date_of_birth' => $candidate->date_of_birth,
                    'gender' => $candidate->gender,
                    'nationality' => $candidate->nationality,
                    'current_salary' => $candidate->current_salary,
                    'expected_salary' => $candidate->expected_salary,
                    'experience' => $candidate->experience,
                    'immediate_available' => $candidate->immediate_available,
                    'applications_count' => $candidate->applications_count ?? 0,
                    'is_verified' => $candidate->user?->email_verified_at !== null,
                    'created_at' => $candidate->created_at?->toISOString(),
                    'updated_at' => $candidate->updated_at?->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Candidate not found',
                'error' => config('app.debug') ? $e->getMessage() : 'Candidate not found',
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
            $candidate = Candidate::findOrFail($id);
            $data = $request->validated();

            // Update candidate with validated data
            $updateData = [];
            if (isset($data['first_name'])) {
                $updateData['first_name'] = $data['first_name'];
            }
            if (isset($data['last_name'])) {
                $updateData['last_name'] = $data['last_name'];
            }
            if (isset($data['email'])) {
                $updateData['email'] = $data['email'];
            }
            if (isset($data['phone'])) {
                $updateData['phone'] = $data['phone'];
            }
            if (isset($data['expected_salary'])) {
                $updateData['expected_salary'] = $data['expected_salary'];
            }
            if (isset($data['immediate_available'])) {
                $updateData['immediate_available'] = $data['immediate_available'];
            }

            $candidate->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Candidate updated successfully',
                'data' => [
                    'id' => $candidate->id,
                    'name' => trim($candidate->first_name.' '.$candidate->last_name),
                    'email' => $candidate->email,
                    'phone' => $candidate->phone,
                    'updated_at' => $candidate->updated_at?->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update candidate',
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
            $candidate = Candidate::findOrFail($id);
            $candidateName = trim($candidate->first_name.' '.$candidate->last_name);

            // Check if candidate has applications
            $applicationsCount = $candidate->applications()->count();
            if ($applicationsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete candidate '{$candidateName}' because they have {$applicationsCount} job applications",
                ], 422);
            }

            $candidate->delete();

            return response()->json([
                'success' => true,
                'message' => "Candidate '{$candidateName}' deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete candidate',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
