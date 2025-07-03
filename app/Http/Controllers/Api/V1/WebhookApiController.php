<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebhookResource;
use App\Models\Webhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for Webhook
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class WebhookApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Webhook::query();

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
            'message' => 'Webhook list retrieved successfully',
            'data' => WebhookResource::collection($data->items()),
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
    public function store(StoreWebhookRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Webhook::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Webhook created successfully',
                'data' => new WebhookResource($item),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create webhook',
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
            $item = Webhook::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Webhook retrieved successfully',
                'data' => new WebhookResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param  mixed  $id
     */
    public function update(UpdateWebhookRequest $request, $id): JsonResponse
    {
        try {
            $item = Webhook::findOrFail($id);
            $data = $request->validated();
            $item->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Webhook updated successfully',
                'data' => new WebhookResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update webhook',
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
            $item = Webhook::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Webhook deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete webhook',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
