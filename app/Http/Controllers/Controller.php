<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Base Controller Class.
 *
 * This is the base controller class that all other controllers should extend.
 * It provides common functionality and follows Laravel 12 best practices.
 *
 * Features:
 * - Request authorization capabilities
 * - Request validation functionality
 * - Enhanced enterprise patterns
 * - Modern Laravel 12 structure
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Default items per page for pagination.
     */
    protected int $perPage = 15;

    /**
     * Response structure for consistent API responses.
     *
     * @param null|mixed $data
     */
    protected function successResponse($data = null, string $message = 'Operation successful', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $status);
    }

    /**
     * Error response structure.
     *
     * @param null|mixed $errors
     */
    protected function errorResponse(string $message = 'Operation failed', int $status = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Cache wrapper for consistent caching.
     *
     * @param mixed $ttl
     * @param mixed $callback
     */
    protected function cacheRemember(string $key, $ttl, $callback)
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning("Cache operation failed for key: {$key}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Fallback to direct execution if cache fails
            return $callback();
        }
    }

    /**
     * Log user actions for audit trail.
     */
    protected function logUserAction(string $action, array $data = [], ?int $userId = null): void
    {
        $userId = $userId ?? auth()->id();

        Log::info("User action: {$action}", [
            'user_id' => $userId,
            'action' => $action,
            'data' => $data,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Validate and sanitize request data.
     */
    protected function sanitizeInput(array $data): array
    {
        return collect($data)->map(function ($value) {
            if (is_string($value)) {
                return trim(strip_tags($value));
            }

            return $value;
        })->toArray();
    }

    /**
     * Check if request is from API.
     */
    protected function isApiRequest(?Request $request = null): bool
    {
        $request = $request ?? request();

        return $request->is('api/*')
               || $request->expectsJson()
               || 'application/json' === $request->header('Accept');
    }

    /**
     * Handle pagination parameters.
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'per_page' => min($request->get('per_page', $this->perPage), 100),
            'page' => $request->get('page', 1),
            'sort' => $request->get('sort', 'id'),
            'direction' => $request->get('direction', 'desc'),
        ];
    }

    /**
     * Handle search and filter parameters.
     */
    protected function getFilterParams(Request $request): array
    {
        return [
            'search' => $request->get('search'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => in_array($request->get('sort_direction'), ['asc', 'desc'])
                ? $request->get('sort_direction')
                : 'desc',
            'status' => $request->get('status'),
            'category' => $request->get('category'),
        ];
    }

    /**
     * Apply common filters to query.
     *
     * @param mixed $query
     * @param mixed $request
     */
    protected function applyCommonFilters($query, $request)
    {
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $this->applySearchFilter($q, $searchTerm);
            });
        }

        // Date range filtering
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->get('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->get('end_date'));
        }

        // Status filtering
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        return $query;
    }

    /**
     * Override this method in child controllers to define search fields.
     *
     * @param mixed $query
     */
    protected function applySearchFilter($query, string $searchTerm)
    {
        // Default implementation - override in child controllers
        if (method_exists($query->getModel(), 'searchable')) {
            $query->search($searchTerm);
        }
    }

    /**
     * Get validation rules for the controller
     * Override in child controllers.
     */
    protected function getValidationRules(string $action = 'store'): array
    {
        return [];
    }

    /**
     * Get validation messages for the controller
     * Override in child controllers.
     */
    protected function getValidationMessages(): array
    {
        return [];
    }

    /**
     * Log controller action for audit trail.
     *
     * @param null|mixed $model
     */
    protected function logAction(string $action, $model = null, array $data = []): void
    {
        if (config('app.log_controller_actions', false)) {
            \Log::info("Controller Action: {$action}", [
                'controller' => static::class,
                'user_id' => auth()->id(),
                'model' => $model ? get_class($model) : null,
                'model_id' => $model?->id ?? null,
                'data' => $data,
                'timestamp' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Handle model not found exceptions gracefully.
     */
    protected function handleModelNotFound(string $modelName = 'Record'): array
    {
        return $this->errorResponse(
            message: "{$modelName} not found",
            status: 404
        );
    }

    /**
     * Handle authorization failures gracefully.
     */
    protected function handleUnauthorized(string $action = 'perform this action'): array
    {
        return $this->errorResponse(
            message: "You are not authorized to {$action}",
            status: 403
        );
    }

    /**
     * Handle validation failures gracefully.
     *
     * @param mixed $validator
     */
    protected function handleValidationFailure($validator): array
    {
        return $this->errorResponse(
            message: 'Validation failed',
            errors: $validator->errors(),
            status: 422
        );
    }
}
