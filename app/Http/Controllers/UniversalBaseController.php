<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UniversalBaseController extends AppBaseController
{
    /**
     * Default cache TTL in seconds (1 hour).
     */
    protected int $cacheTTL = 3600;

    /**
     * Universal response wrapper with metadata.
     *
     * @param mixed $data
     */
    protected function universalResponse($data, string $message = 'Success', array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'version' => '1.0.0',
                'request_id' => Str::uuid(),
            ], $meta),
        ];

        return response()->json($response);
    }

    /**
     * Universal error response with consistent structure.
     */
    protected function universalError(string $message, array $errors = [], int $code = 400, array $meta = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'error_code' => $code,
                'request_id' => Str::uuid(),
            ], $meta),
        ];

        return response()->json($response, $code);
    }

    /**
     * Universal cache key generation.
     */
    protected function universalCacheKey(string $type, ...$params): string
    {
        $userId = auth()->id() ?? 'guest';
        $locale = app()->getLocale();
        $paramString = implode('_', array_filter($params));

        return "universal_{$type}_{$locale}_{$userId}_{$paramString}";
    }

    /**
     * Universal model query with common filters.
     *
     * @param mixed $model
     */
    protected function universalQuery($model, Request $request)
    {
        $query = is_string($model) ? app($model)->newQuery() : $model->newQuery();

        // Apply universal filters
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('active')) {
            $active = filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $active);
        }

        if ($request->has('featured')) {
            $featured = filter_var($request->get('featured'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_featured', $featured);
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->get('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->get('to_date'));
        }

        return $query;
    }

    /**
     * Universal search functionality.
     *
     * @param mixed $query
     */
    protected function universalSearch($query, string $search, array $fields = ['name', 'title', 'description'])
    {
        return $query->where(function ($q) use ($search, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$search}%");
            }
        });
    }

    /**
     * Universal sorting.
     *
     * @param mixed $query
     */
    protected function universalSort($query, Request $request, string $defaultSort = 'created_at')
    {
        $sortBy = $request->get('sort_by', $defaultSort);
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        return $query->orderBy($sortBy, $sortDirection);
    }

    /**
     * Universal pagination with metadata.
     *
     * @param mixed      $query
     * @param null|mixed $transformer
     */
    protected function universalPaginate($query, Request $request, $transformer = null)
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        $page = max((int) $request->get('page', 1), 1);

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        // Apply transformer if provided
        if ($transformer) {
            $results->getCollection()->transform($transformer);
        }

        return $this->universalResponse($results->items(), 'Data retrieved successfully', [
            'pagination' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem(),
                'has_more_pages' => $results->hasMorePages(),
            ],
            'filters_applied' => $this->getAppliedFilters($request),
        ]);
    }

    /**
     * Get applied filters for metadata.
     */
    protected function getAppliedFilters(Request $request): array
    {
        $filters = [];

        if ($request->has('search')) {
            $filters['search'] = $request->get('search');
        }

        if ($request->has('status')) {
            $filters['status'] = $request->get('status');
        }

        if ($request->has('active')) {
            $filters['active'] = $request->get('active');
        }

        if ($request->has('featured')) {
            $filters['featured'] = $request->get('featured');
        }

        if ($request->has('from_date')) {
            $filters['from_date'] = $request->get('from_date');
        }

        if ($request->has('to_date')) {
            $filters['to_date'] = $request->get('to_date');
        }

        if ($request->has('sort_by')) {
            $filters['sort_by'] = $request->get('sort_by');
        }

        if ($request->has('sort_direction')) {
            $filters['sort_direction'] = $request->get('sort_direction');
        }

        return $filters;
    }

    /**
     * Universal model operations with logging.
     *
     * @param mixed $model
     */
    protected function universalCreate($model, array $data, string $action = 'create'): JsonResponse
    {
        try {
            $modelClass = is_string($model) ? $model : get_class($model);
            $instance = $modelClass::create($data);

            $this->logUserAction($action, [
                'model' => $modelClass,
                'id' => $instance->id,
                'data' => $data,
            ]);

            return $this->universalResponse($instance, ucfirst($action).' successful');
        } catch (\Exception $e) {
            Log::error("Universal {$action} failed", [
                'model' => is_string($model) ? $model : get_class($model),
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return $this->universalError("Failed to {$action} record", [], 500);
        }
    }

    /**
     * Universal model update with logging.
     *
     * @param mixed $instance
     */
    protected function universalUpdate($instance, array $data, string $action = 'update'): JsonResponse
    {
        try {
            $instance->update($data);

            $this->logUserAction($action, [
                'model' => get_class($instance),
                'id' => $instance->id,
                'data' => $data,
            ]);

            return $this->universalResponse($instance->fresh(), ucfirst($action).' successful');
        } catch (\Exception $e) {
            Log::error("Universal {$action} failed", [
                'model' => get_class($instance),
                'id' => $instance->id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return $this->universalError("Failed to {$action} record", [], 500);
        }
    }

    /**
     * Universal model deletion with logging.
     *
     * @param mixed $instance
     */
    protected function universalDelete($instance, string $action = 'delete'): JsonResponse
    {
        try {
            $modelClass = get_class($instance);
            $id = $instance->id;

            $instance->delete();

            $this->logUserAction($action, [
                'model' => $modelClass,
                'id' => $id,
            ]);

            return $this->universalResponse(null, ucfirst($action).' successful');
        } catch (\Exception $e) {
            Log::error("Universal {$action} failed", [
                'model' => get_class($instance),
                'id' => $instance->id,
                'error' => $e->getMessage(),
            ]);

            return $this->universalError("Failed to {$action} record", [], 500);
        }
    }

    /**
     * Universal bulk operations.
     */
    protected function universalBulkOperation(array $ids, callable $operation, string $action = 'bulk operation'): JsonResponse
    {
        if (empty($ids)) {
            return $this->universalError('No IDs provided for bulk operation');
        }

        $successful = 0;
        $failed = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $operation($id);
                ++$successful;
            } catch (\Exception $e) {
                ++$failed;
                $errors[] = "ID {$id}: ".$e->getMessage();
            }
        }

        $this->logUserAction($action, [
            'total_ids' => count($ids),
            'successful' => $successful,
            'failed' => $failed,
        ]);

        return $this->universalResponse([
            'successful' => $successful,
            'failed' => $failed,
            'errors' => $errors,
        ], "Bulk {$action} completed: {$successful} successful, {$failed} failed");
    }

    /**
     * Universal cache management.
     */
    protected function universalCache(string $key, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? $this->cacheTTL;
        $cacheKey = $this->universalCacheKey($key);

        return $this->cacheRemember($cacheKey, $ttl, $callback);
    }

    /**
     * Clear universal cache by pattern.
     */
    protected function clearUniversalCache(?string $pattern = null): void
    {
        if ($pattern) {
            $keys = Cache::store()->keys("universal_{$pattern}*");
            if (!empty($keys)) {
                Cache::store()->deleteMultiple($keys);
            }
        } else {
            Cache::store()->flush();
        }
    }
}
