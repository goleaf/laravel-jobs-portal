<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * App Base Controller
 * 
 * Extended base controller for the Job Portal application with API response methods,
 * caching capabilities, and Context7 enterprise patterns.
 * 
 * Features:
 * - Standardized JSON API responses
 * - Advanced caching strategies
 * - Performance monitoring
 * - Error handling and logging
 * - Context7 job portal patterns
 */
class AppBaseController extends Controller
{
    /**
     * Default cache TTL in seconds (1 hour)
     */
    protected int $cacheTTL = 3600;

    /**
     * Enable/disable query logging for performance monitoring
     */
    protected bool $logQueries = false;

    /**
     * Success response with data
     */
    public function sendResponse($result, string $message = 'Success', int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'request_id' => request()->header('X-Request-ID', uniqid())
            ]
        ];

        return response()->json($response, $code);
    }

    /**
     * Error response
     */
    public function sendError(string $error, $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
            'errors' => $errorMessages,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'request_id' => request()->header('X-Request-ID', uniqid())
            ]
        ];

        // Log error for monitoring
        Log::warning('API Error Response', [
            'message' => $error,
            'errors' => $errorMessages,
            'code' => $code,
            'url' => request()->url(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
            'ip' => request()->ip()
        ]);

        return response()->json($response, $code);
    }

    /**
     * Success response without data
     */
    public function sendSuccess(string $message = 'Operation completed successfully', int $code = 200): JsonResponse
    {
        return $this->sendResponse(null, $message, $code);
    }

    /**
     * Paginated response with meta information
     */
    protected function sendPaginatedResponse(LengthAwarePaginator $paginator, string $message = 'Data retrieved successfully'): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages()
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'request_id' => request()->header('X-Request-ID', uniqid())
            ]
        ];

        return response()->json($response, 200);
    }

    /**
     * Handle API pagination with filters and search
     */
    protected function sendPaginated($query, Request $request, $resource = null)
    {
        $params = $this->getPaginationParams($request);
        
        // Apply search if provided
        if ($request->filled('search')) {
            $query = $this->applySearch($query, $request->get('search'));
        }

        // Apply job portal specific filters
        $query = $this->applyJobPortalFilters($query, $request);

        // Apply sorting
        $sortBy = $params['sort'] ?? 'id';
        $sortDirection = $params['direction'] ?? 'desc';
        $query = $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $results = $query->paginate($params['per_page']);

        return $this->sendPaginatedResponse($results);
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
     * Apply standard filters for job portal entities
     */
    protected function applyJobPortalFilters($query, Request $request)
    {
        // Apply common filters first
        $query = $this->applyCommonFilters($query, $request);

        // Job portal specific filters
        if ($request->filled('category_id')) {
            $query->where('job_category_id', $request->get('category_id'));
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->get('company_id'));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->get('location') . '%');
        }

        if ($request->filled('salary_min')) {
            $query->where('salary_from', '>=', $request->get('salary_min'));
        }

        if ($request->filled('salary_max')) {
            $query->where('salary_to', '<=', $request->get('salary_max'));
        }

        if ($request->filled('experience_level')) {
            $query->where('experience_from', '<=', $request->get('experience_level'))
                  ->where('experience_to', '>=', $request->get('experience_level'));
        }

        if ($request->filled('job_type')) {
            $query->where('job_type_id', $request->get('job_type'));
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return $query;
    }

    /**
     * Validation error response
     */
    protected function sendValidationError($validator): JsonResponse
    {
        return $this->sendError('Validation Error', $validator->errors(), 422);
    }

    /**
     * Unauthorized response
     */
    protected function sendUnauthorized(string $message = 'Unauthorized access'): JsonResponse
    {
        return $this->sendError($message, [], 401);
    }

    /**
     * Forbidden response
     */
    protected function sendForbidden(string $message = 'Access forbidden'): JsonResponse
    {
        return $this->sendError($message, [], 403);
    }

    /**
     * Server error response
     */
    protected function sendServerError(string $message = 'Internal server error', $debug = null): JsonResponse
    {
        $errors = [];
        if (config('app.debug') && $debug) {
            $errors['debug'] = $debug;
        }

        return $this->sendError($message, $errors, 500);
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
     * Cache response data with key
     */
    protected function cacheResponse(string $key, $data, int $ttl = null): void
    {
        $ttl = $ttl ?? $this->cacheTTL;
        Cache::put($key, $data, $ttl);
    }

    /**
     * Get cached response data
     */
    protected function getCachedResponse(string $key)
    {
        return Cache::get($key);
    }

    /**
     * Get resource with caching
     */
    protected function getCachedResource(string $cacheKey, callable $callback, int $ttl = null)
    {
        return Cache::remember($cacheKey, $ttl ?? $this->cacheTTL, $callback);
    }

    /**
     * Build cache key with prefix
     */
    protected function buildCacheKey(string $base, ...$params): string
    {
        $key = config('app.name', 'jobportal') . ':' . $base;
        
        foreach ($params as $param) {
            $key .= ':' . (is_array($param) ? md5(serialize($param)) : $param);
        }
        
        return $key;
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
     * Execute query with performance monitoring
     */
    protected function executeWithMonitoring(callable $callback, string $operation = 'database_query')
    {
        $startTime = microtime(true);
        
        if ($this->logQueries) {
            DB::enableQueryLog();
        }

        try {
            $result = $callback();
            
            $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
            
            // Log performance metrics
            Log::info('Performance Metric', [
                'operation' => $operation,
                'execution_time_ms' => round($executionTime, 2),
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'queries_count' => $this->logQueries ? count(DB::getQueryLog()) : null,
                'controller' => static::class,
                'user_id' => auth()->id()
            ]);

            return $result;
            
        } catch (\Exception $e) {
            Log::error('Operation Failed', [
                'operation' => $operation,
                'error' => $e->getMessage(),
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'controller' => static::class,
                'user_id' => auth()->id()
            ]);
            
            throw $e;
        } finally {
            if ($this->logQueries) {
                DB::disableQueryLog();
            }
        }
    }

    /**
     * Handle resource creation with logging
     */
    protected function handleResourceCreation(Model $model, array $data, string $resourceName = 'Resource'): JsonResponse
    {
        try {
            DB::beginTransaction();

            $resource = $model->create($data);
            
            $this->logAction('create', $resource, $data);
            
            DB::commit();

            return $this->sendResponse(
                $resource->fresh(),
                "{$resourceName} created successfully",
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Failed to create {$resourceName}", [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            return $this->sendServerError("Failed to create {$resourceName}");
        }
    }

    /**
     * Handle resource update with logging
     */
    protected function handleResourceUpdate(Model $resource, array $data, string $resourceName = 'Resource'): JsonResponse
    {
        try {
            DB::beginTransaction();

            $originalData = $resource->toArray();
            $resource->update($data);
            
            $this->logAction('update', $resource, [
                'original' => $originalData,
                'updated' => $data
            ]);
            
            DB::commit();

            return $this->sendResponse(
                $resource->fresh(),
                "{$resourceName} updated successfully"
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Failed to update {$resourceName}", [
                'error' => $e->getMessage(),
                'resource_id' => $resource->id,
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            return $this->sendServerError("Failed to update {$resourceName}");
        }
    }

    /**
     * Handle resource deletion with logging
     */
    protected function handleResourceDeletion(Model $resource, string $resourceName = 'Resource'): JsonResponse
    {
        try {
            DB::beginTransaction();

            $resourceData = $resource->toArray();
            $resource->delete();
            
            $this->logAction('delete', $resource, $resourceData);
            
            DB::commit();

            return $this->sendSuccess("{$resourceName} deleted successfully");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Failed to delete {$resourceName}", [
                'error' => $e->getMessage(),
                'resource_id' => $resource->id,
                'user_id' => auth()->id()
            ]);

            return $this->sendServerError("Failed to delete {$resourceName}");
        }
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
        
        $this->logAction('file_upload', null, [
            'field' => $field,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $filePath,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return $filePath;
    }

    /**
     * Check if request is API request
     */
    protected function isApiRequest(Request $request = null): bool
    {
        $request = $request ?? request();
        return $request->is('api/*') || $request->wantsJson();
    }
} 