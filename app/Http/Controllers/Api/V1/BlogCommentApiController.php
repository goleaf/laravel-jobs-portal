<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\BlogComment;
use App\Http\Resources\BlogCommentResource;
use App\Http\Requests\Api\StoreBlogCommentRequest;
use App\Http\Requests\Api\UpdateBlogCommentRequest;

/**
 * Context7 API Controller for BlogComment
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class BlogCommentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BlogComment::query();
        
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
            'message' => 'BlogComment list retrieved successfully',
            'data' => BlogCommentResource::collection($data->items()),
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
    public function store(StoreBlogCommentRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = BlogComment::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'BlogComment created successfully',
                'data' => new BlogCommentResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blogcomment',
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
            $item = BlogComment::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'BlogComment retrieved successfully',
                'data' => new BlogCommentResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'BlogComment not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateBlogCommentRequest $request, $id): JsonResponse
    {
        try {
            $item = BlogComment::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'BlogComment updated successfully',
                'data' => new BlogCommentResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blogcomment',
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
            $item = BlogComment::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'BlogComment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blogcomment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}