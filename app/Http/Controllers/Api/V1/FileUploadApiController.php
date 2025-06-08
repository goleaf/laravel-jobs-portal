<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\FileUpload;
use App\Http\Resources\FileUploadResource;
use App\Http\Requests\Api\StoreFileUploadRequest;
use App\Http\Requests\Api\UpdateFileUploadRequest;

/**
 * Context7 API Controller for FileUpload
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class FileUploadApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FileUpload::query();
        
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
            'message' => 'FileUpload list retrieved successfully',
            'data' => FileUploadResource::collection($data->items()),
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
    public function store(StoreFileUploadRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = FileUpload::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'FileUpload created successfully',
                'data' => new FileUploadResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create fileupload',
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
            $item = FileUpload::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'FileUpload retrieved successfully',
                'data' => new FileUploadResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'FileUpload not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateFileUploadRequest $request, $id): JsonResponse
    {
        try {
            $item = FileUpload::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'FileUpload updated successfully',
                'data' => new FileUploadResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update fileupload',
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
            $item = FileUpload::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'FileUpload deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete fileupload',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}