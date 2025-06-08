<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\CompanySize;
use App\Http\Resources\CompanySizeResource;

/**
 * Context7 API Controller for CompanySize
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class CompanySizeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CompanySize::query();
        
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
            'message' => 'CompanySize list retrieved successfully',
            'data' => CompanySizeResource::collection($data->items()),
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
    public function store(StoreCompanySizeRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = CompanySize::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'CompanySize created successfully',
                'data' => new CompanySizeResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create companysize',
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
            $item = CompanySize::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'CompanySize retrieved successfully',
                'data' => new CompanySizeResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'CompanySize not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateCompanySizeRequest $request, $id): JsonResponse
    {
        try {
            $item = CompanySize::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'CompanySize updated successfully',
                'data' => new CompanySizeResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update companysize',
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
            $item = CompanySize::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'CompanySize deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete companysize',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}