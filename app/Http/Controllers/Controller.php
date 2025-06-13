<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Response structure for consistent API responses
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
     * Error response structure
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
     * Cache wrapper for consistent caching
     */
    protected function cacheRemember(string $key, $ttl, $callback)
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning("Cache operation failed for key: {$key}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback to direct execution if cache fails
            return $callback();
        }
    }

    /**
     * Log user actions for audit trail
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
     * Validate and sanitize request data
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
     * Check if request is from API
     */
    protected function isApiRequest(Request $request = null): bool
    {
        $request = $request ?? request();
        
        return $request->is('api/*') || 
               $request->expectsJson() || 
               $request->header('Accept') === 'application/json';
    }

    /**
     * Handle pagination parameters
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'per_page' => min((int) $request->get('per_page', 15), 100),
            'page' => max((int) $request->get('page', 1), 1),
        ];
    }

    /**
     * Handle search and filter parameters
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
} 