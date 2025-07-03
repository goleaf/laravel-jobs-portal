<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for EmailTemplate
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class EmailTemplateApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = EmailTemplate::query();

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
            'message' => 'EmailTemplate list retrieved successfully',
            'data' => EmailTemplateResource::collection($data->items()),
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
    public function store(StoreEmailTemplateRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = EmailTemplate::create($data);

            return response()->json([
                'success' => true,
                'message' => 'EmailTemplate created successfully',
                'data' => new EmailTemplateResource($item),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create emailtemplate',
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
            $item = EmailTemplate::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'EmailTemplate retrieved successfully',
                'data' => new EmailTemplateResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'EmailTemplate not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param  mixed  $id
     */
    public function update(UpdateEmailTemplateRequest $request, $id): JsonResponse
    {
        try {
            $item = EmailTemplate::findOrFail($id);
            $data = $request->validated();
            $item->update($data);

            return response()->json([
                'success' => true,
                'message' => 'EmailTemplate updated successfully',
                'data' => new EmailTemplateResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update emailtemplate',
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
            $item = EmailTemplate::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'EmailTemplate deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete emailtemplate',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
