<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\SalaryPeriod;
use App\Http\Resources\SalaryPeriodResource;

/**
 * Enhanced API Controller for SalaryPeriod
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class SalaryPeriodApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = SalaryPeriod::query();
        
        // Apply filters
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }
        
        // Pagination
        $perPage = min($request->integer('per_page', 15), 100);
        $data = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => 'SalaryPeriod list retrieved successfully',
            'data' => SalaryPeriodResource::collection($data->items()),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreSalaryPeriodRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = SalaryPeriod::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'SalaryPeriod created successfully',
                'data' => new SalaryPeriodResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create salaryperiod',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $item = SalaryPeriod::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'SalaryPeriod retrieved successfully',
                'data' => new SalaryPeriodResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SalaryPeriod not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateSalaryPeriodRequest $request, $id): JsonResponse
    {
        try {
            $item = SalaryPeriod::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'SalaryPeriod updated successfully',
                'data' => new SalaryPeriodResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update salaryperiod',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $item = SalaryPeriod::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'SalaryPeriod deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete salaryperiod',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}