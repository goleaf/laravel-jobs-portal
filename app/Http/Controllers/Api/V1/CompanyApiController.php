<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Company;
use App\Http\Resources\CompanyResource;

/**
 * Context7 API Controller for Company
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class CompanyApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Company::query();
        
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
            'message' => 'Company list retrieved successfully',
            'data' => CompanyResource::collection($data->items()),
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
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Company::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Company created successfully',
                'data' => new CompanyResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create company',
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
            $item = Company::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Company retrieved successfully',
                'data' => new CompanyResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateCompanyRequest $request, $id): JsonResponse
    {
        try {
            $item = Company::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully',
                'data' => new CompanyResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update company',
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
            $item = Company::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete company',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}