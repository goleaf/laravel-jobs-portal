<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Universal Optimized Base Controller
 * Implements MCP best practices for Laravel 12 performance and security
 */
abstract class UniversalBaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Default cache duration in seconds (1 hour)
     */
    protected int $defaultCacheDuration = 3600;

    /**
     * Default pagination count
     */
    protected int $defaultPaginationCount = 15;

    /**
     * Universal Pattern: Flexible caching with stale-while-revalidate
     *
     * @param string $key Cache key
     * @param callable $callback Function to execute if not cached
     * @param int $freshMinutes Fresh time in minutes
     * @param int $staleMinutes Additional stale time in minutes
     * @return mixed
     */
    protected function cacheFlexible(string $key, callable $callback, int $freshMinutes = 60, int $staleMinutes = 120): mixed
    {
        return Cache::flexible($key, [$freshMinutes * 60, $staleMinutes * 60], $callback);
    }

    /**
     * Universal Pattern: Standard cache with remember
     *
     * @param string $key Cache key
     * @param callable $callback Function to execute if not cached
     * @param int $minutes Cache duration in minutes
     * @return mixed
     */
    protected function cacheRemember(string $key, callable $callback, int $minutes = 60): mixed
    {
        return Cache::remember($key, $minutes * 60, $callback);
    }

    /**
     * Universal Pattern: Optimized cursor pagination for large datasets
     *
     * @param Builder $query Query builder instance
     * @param int $perPage Items per page
     * @param array $columns Columns to select
     * @return \Illuminate\Contracts\Pagination\CursorPaginator
     */
    protected function paginateWithCursor(Builder $query, int $perPage = null, array $columns = ['*'])
    {
        $perPage = $perPage ?? $this->defaultPaginationCount;
        
        return $query->select($columns)->cursorPaginate($perPage);
    }

    /**
     * Universal Pattern: Standard pagination with caching
     *
     * @param Builder $query Query builder instance  
     * @param int $perPage Items per page
     * @param string $cacheKey Cache key for pagination
     * @param array $columns Columns to select
     * @return LengthAwarePaginator
     */
    protected function paginateWithCache(Builder $query, int $perPage = null, string $cacheKey = null, array $columns = ['*']): LengthAwarePaginator
    {
        $perPage = $perPage ?? $this->defaultPaginationCount;
        
        if ($cacheKey) {
            return $this->cacheRemember($cacheKey, function () use ($query, $perPage, $columns) {
                return $query->select($columns)->paginate($perPage);
            }, 10); // Short cache for paginated data
        }
        
        return $query->select($columns)->paginate($perPage);
    }

    /**
     * Universal Pattern: Chunk processing for large datasets
     *
     * @param Builder $query Query builder instance
     * @param callable $callback Function to process each chunk
     * @param int $chunkSize Size of each chunk
     * @return bool
     */
    protected function processInChunks(Builder $query, callable $callback, int $chunkSize = 100): bool
    {
        return $query->chunkById($chunkSize, $callback);
    }

    /**
     * Universal Pattern: Optimized query with eager loading
     *
     * @param Builder $query Query builder instance
     * @param array $relations Relations to eager load
     * @param array $counts Relations to count
     * @return Builder
     */
    protected function optimizeQuery(Builder $query, array $relations = [], array $counts = []): Builder
    {
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        if (!empty($counts)) {
            $query->withCount($counts);
        }
        
        return $query;
    }

    /**
     * Universal Pattern: Safe transaction execution
     *
     * @param callable $callback Transaction callback
     * @param int $attempts Number of retry attempts
     * @return mixed
     * @throws \Exception
     */
    protected function executeTransaction(callable $callback, int $attempts = 1): mixed
    {
        return DB::transaction($callback, $attempts);
    }

    /**
     * Universal Pattern: Pessimistic locking for critical operations
     *
     * @param Builder $query Query builder instance
     * @return Builder
     */
    protected function lockForUpdate(Builder $query): Builder
    {
        return $query->lockForUpdate();
    }

    /**
     * Universal Pattern: Shared locking for read operations
     *
     * @param Builder $query Query builder instance
     * @return Builder
     */
    protected function sharedLock(Builder $query): Builder
    {
        return $query->sharedLock();
    }

    /**
     * Universal Pattern: Generate cache key with request parameters
     *
     * @param Request $request Request instance
     * @param string $prefix Cache key prefix
     * @param array $additionalParams Additional parameters for cache key
     * @return string
     */
    protected function generateCacheKey(FormRequest $request, string $prefix, array $additionalParams = []): string
    {
        $params = array_merge(
            $request->only(['page', 'per_page', 'sort', 'filter', 'search']),
            $additionalParams
        );
        
        $key = $prefix . '_' . md5(serialize($params));
        
        if ($request->user()) {
            $key .= '_user_' . $request->user()->id;
        }
        
        return $key;
    }

    /**
     * Universal Pattern: Rate limited action
     *
     * @param Request $request Request instance
     * @param string $action Action name for rate limiting
     * @param callable $callback Action to execute
     * @param int $maxAttempts Maximum attempts
     * @param int $decayMinutes Decay time in minutes
     * @return mixed
     */
    protected function rateLimitedAction(StoreRequest $request, string $action, callable $callback, int $maxAttempts = 60, int $decayMinutes = 1): mixed
    {
        $key = $this->generateRateLimitKey($request, $action);
        
        if (\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = \RateLimiter::availableIn($key);
            throw new \Illuminate\Http\Exceptions\ThrottleRequestsException(
                "Too many attempts for $action. Try again in $seconds seconds."
            );
        }
        
        \RateLimiter::hit($key, $decayMinutes * 60);
        
        return $callback();
    }

    /**
     * Universal Pattern: Generate rate limit key
     *
     * @param Request $request Request instance
     * @param string $action Action name
     * @return string
     */
    protected function generateRateLimitKey(StoreRequest $request, string $action): string
    {
        $identifier = $request->user() ? $request->user()->id : $request->ip();
        return "rate_limit_{$action}_{$identifier}";
    }

    /**
     * Universal Pattern: JSON response with consistent format
     *
     * @param array $data Response data
     * @param string $message Response message
     * @param int $status HTTP status code
     * @param array $meta Additional metadata
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(array $data = [], string $message = 'Success', int $status = 200, array $meta = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0')
            ], $meta)
        ], $status);
    }

    /**
     * Universal Pattern: Error response with consistent format
     *
     * @param string $message Error message
     * @param int $status HTTP status code
     * @param array $errors Validation errors
     * @param array $debug Debug information (only in debug mode)
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message = 'Error', int $status = 400, array $errors = [], array $debug = []): \Illuminate\Http\JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'status_code' => $status
            ]
        ];
        
        if (config('app.debug') && !empty($debug)) {
            $response['debug'] = $debug;
        }
        
        return response()->json($response, $status);
    }

    /**
     * Universal Pattern: Cached model retrieval
     *
     * @param string $model Model class name
     * @param mixed $id Model ID
     * @param array $relations Relations to eager load
     * @param int $cacheMinutes Cache duration in minutes
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function findCached(string $model, mixed $id, array $relations = [], int $cacheMinutes = 60)
    {
        $cacheKey = "model_{$model}_{$id}_" . md5(serialize($relations));
        
        return $this->cacheRemember($cacheKey, function () use ($model, $id, $relations) {
            $query = $model::query();
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            return $query->find($id);
        }, $cacheMinutes);
    }

    /**
     * Universal Pattern: Clear model cache
     *
     * @param string $model Model class name
     * @param mixed $id Model ID
     * @return void
     */
    protected function clearModelCache(string $model, mixed $id): void
    {
        $pattern = "model_{$model}_{$id}_*";
        Cache::flush(); // In production, use more targeted cache clearing
    }
} 