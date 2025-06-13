<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class AppBaseController extends Controller
{
    /**
     * Send success response with data
     */
    public function sendResponse($result, $message = 'Operation successful', $status = 200): JsonResponse
    {
        return $this->successResponse($result, $message, $status);
    }

    /**
     * Send error response
     */
    public function sendError($error, $errorMessages = [], $code = 400): JsonResponse
    {
        return $this->errorResponse($error, $code, $errorMessages);
    }

    /**
     * Send success response without data
     */
    public function sendSuccess($message = 'Operation completed successfully'): JsonResponse
    {
        return $this->successResponse(null, $message);
    }

    /**
     * Handle API pagination
     */
    protected function sendPaginated($query, Request $request, $resource = null)
    {
        $params = $this->getPaginationParams($request);
        $filters = $this->getFilterParams($request);

        // Apply search if provided
        if (!empty($filters['search'])) {
            $query = $this->applySearch($query, $filters['search']);
        }

        // Apply sorting
        $query = $query->orderBy($filters['sort_by'], $filters['sort_direction']);

        // Paginate results
        $results = $query->paginate($params['per_page']);

        // Transform with resource if provided
        if ($resource) {
            $results->getCollection()->transform(function ($item) use ($resource) {
                return new $resource($item);
            });
        }

        return $this->sendResponse([
            'data' => $results->items(),
            'pagination' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem(),
            ]
        ]);
    }

    /**
     * Apply search to query
     */
    protected function applySearch($query, string $search)
    {
        // This can be overridden in child controllers for specific search logic
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('title', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get authenticated user
     */
    protected function getAuthenticatedUser()
    {
        return Auth::user();
    }

    /**
     * Check if user has permission
     */
    protected function checkPermission(string $permission): bool
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return false;
        }

        // Check if user has the specific permission
        return $user->can($permission);
    }

    /**
     * Authorize action or fail
     */
    protected function authorizeOrFail(string $permission, string $message = 'Unauthorized action')
    {
        if (!$this->checkPermission($permission)) {
            abort(403, $message);
        }
    }

    /**
     * Cache key generator for consistent naming
     */
    protected function getCacheKey(string $prefix, ...$params): string
    {
        $userId = Auth::id() ?? 'guest';
        $paramString = implode('_', array_filter($params));
        
        return "app_{$prefix}_{$userId}_{$paramString}";
    }

    /**
     * Handle file upload with validation
     */
    protected function handleFileUpload(Request $request, string $field, string $path = 'uploads'): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        
        if (!$file->isValid()) {
            throw new \Exception("Invalid file upload for field: {$field}");
        }

        // Store file and return path
        $filePath = $file->store($path, 'public');
        
        $this->logUserAction('file_upload', [
            'field' => $field,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $filePath,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return $filePath;
    }

    /**
     * Validate request with custom rules
     */
    protected function validateRequest(Request $request, array $rules, array $messages = []): array
    {
        $validator = validator($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($this->isApiRequest($request)) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        return $validator->validated();
    }

    /**
     * Handle bulk operations
     */
    protected function handleBulkOperation(Request $request, callable $operation): JsonResponse
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('No valid IDs provided for bulk operation');
        }

        $successCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $operation($id);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID {$id}: " . $e->getMessage();
            }
        }

        $message = "Bulk operation completed. {$successCount} successful";
        if (!empty($errors)) {
            $message .= ", " . count($errors) . " failed";
        }

        return $this->sendResponse([
            'successful' => $successCount,
            'failed' => count($errors),
            'errors' => $errors,
        ], $message);
    }

    /**
     * Handle model not found
     */
    protected function handleNotFound(string $model = 'Resource'): JsonResponse
    {
        return $this->sendError("{$model} not found", [], 404);
    }

    /**
     * Handle server error
     */
    protected function handleServerError(\Exception $e, string $action = 'operation'): JsonResponse
    {
        Log::error("Server error during {$action}", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => Auth::id(),
            'request_data' => request()->all(),
        ]);

        if (config('app.debug')) {
            return $this->sendError("Server error: " . $e->getMessage(), [], 500);
        }

        return $this->sendError("An error occurred during {$action}", [], 500);
    }
} 