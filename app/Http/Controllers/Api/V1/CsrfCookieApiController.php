<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CsrfCookieResource;
use App\Models\CsrfCookie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for CsrfCookie
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class CsrfCookieApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CsrfCookie::query();

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
            'message' => 'CsrfCookie list retrieved successfully',
            'data' => CsrfCookieResource::collection($data->items()),
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
    public function store(StoreCsrfCookieRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = CsrfCookie::create($data);

            return response()->json([
                'success' => true,
                'message' => 'CsrfCookie created successfully',
                'data' => new CsrfCookieResource($item),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create csrfcookie',
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
            $item = CsrfCookie::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'CsrfCookie retrieved successfully',
                'data' => new CsrfCookieResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'CsrfCookie not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param  mixed  $id
     */
    public function update(UpdateCsrfCookieRequest $request, $id): JsonResponse
    {
        try {
            $item = CsrfCookie::findOrFail($id);
            $data = $request->validated();
            $item->update($data);

            return response()->json([
                'success' => true,
                'message' => 'CsrfCookie updated successfully',
                'data' => new CsrfCookieResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update csrfcookie',
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
            $item = CsrfCookie::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'CsrfCookie deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete csrfcookie',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
