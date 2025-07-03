<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SitemapResource;
use App\Models\Sitemap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for Sitemap
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class SitemapApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Sitemap::query();

        // Apply filters
        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        // Pagination
        $perPage = min($request->integer('per_page', 15), 100);
        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Sitemap list retrieved successfully',
            'data' => SitemapResource::collection($data->items()),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreSitemapRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Sitemap::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Sitemap created successfully',
                'data' => new SitemapResource($item),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sitemap',
                'error' => $e->getMessage(),
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
            $item = Sitemap::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Sitemap retrieved successfully',
                'data' => new SitemapResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sitemap not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param  mixed  $id
     */
    public function update(UpdateSitemapRequest $request, $id): JsonResponse
    {
        try {
            $item = Sitemap::findOrFail($id);
            $data = $request->validated();
            $item->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Sitemap updated successfully',
                'data' => new SitemapResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sitemap',
                'error' => $e->getMessage(),
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
            $item = Sitemap::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sitemap deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sitemap',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
