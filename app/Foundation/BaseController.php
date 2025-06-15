<?php

namespace App\Foundation;

use App\Foundation\Contracts\ApplicationServiceInterface;
use App\Foundation\Contracts\Command;
use App\Foundation\Contracts\Query;
use App\Http\Controllers\UniversalBaseController;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Enhanced Base Controller.
 *
 * Extends UniversalBaseController with clean architecture patterns:
 * - Service layer integration
 * - Command/Query pattern support
 * - Enhanced response patterns
 * - Better error handling
 * - Rate limiting integration
 */
abstract class BaseController extends UniversalBaseController
{
    /**
     * Execute command through application service.
     */
    protected function executeCommand(
        ApplicationServiceInterface $service,
        Command $command,
        string $successMessage = 'Operation completed successfully',
        int $successStatus = 200
    ): JsonResponse {
        try {
            $result = $service->executeCommand($command);

            return $this->successResponse(
                $result,
                $successMessage,
                $successStatus,
                $this->getCommandMetadata($command, $service)
            );
        } catch (\Exception $e) {
            return $this->handleCommandException($e, $command);
        }
    }

    /**
     * Execute query through application service.
     */
    protected function executeQuery(
        ApplicationServiceInterface $service,
        Query $query,
        string $successMessage = 'Data retrieved successfully'
    ): JsonResponse {
        try {
            $result = $service->executeQuery($query);

            return $this->successResponse(
                $result,
                $successMessage,
                200,
                $this->getQueryMetadata($query, $service)
            );
        } catch (\Exception $e) {
            return $this->handleQueryException($e, $query);
        }
    }

    /**
     * Enhanced success response with resource support.
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        // Convert Laravel resources to arrays
        if ($data instanceof JsonResource || $data instanceof ResourceCollection) {
            $data = $data->resolve();
        }

        return $this->jsonResponse(
            is_array($data) ? $data : ['result' => $data],
            $message,
            $status,
            array_merge([
                'request_id' => $this->getRequestId(),
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
            ], $meta)
        );
    }

    /**
     * Enhanced error response with context.
     */
    protected function errorResponse(
        string $message = 'Error occurred',
        int $status = 400,
        array $errors = [],
        ?string $code = null,
        array $context = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'request_id' => $this->getRequestId(),
                'timestamp' => now()->toISOString(),
                'status_code' => $status,
            ],
        ];

        if ($code) {
            $response['error_code'] = $code;
        }

        if (config('app.debug') && !empty($context)) {
            $response['debug'] = $context;
        }

        return response()->json($response, $status);
    }

    /**
     * Created response (201).
     */
    protected function createdResponse(
        mixed $data = null,
        string $message = 'Resource created successfully',
        array $meta = []
    ): JsonResponse {
        return $this->successResponse($data, $message, 201, $meta);
    }

    /**
     * No content response (204).
     */
    protected function noContentResponse(string $message = 'Operation completed'): JsonResponse
    {
        return $this->successResponse(null, $message, 204);
    }

    /**
     * Execute action with rate limiting.
     *
     * @throws ThrottleRequestsException
     */
    protected function executeWithRateLimit(
        string $action,
        callable $callback,
        int $maxAttempts = 60,
        int $decayMinutes = 1
    ): mixed {
        $key = $this->getRateLimitKey($action);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            throw new ThrottleRequestsException(
                "Too many attempts for {$action}. Try again in {$seconds} seconds."
            );
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $callback();
    }

    /**
     * Get rate limit key for current request.
     */
    private function getRateLimitKey(string $action): string
    {
        $request = request();
        $identifier = $request->user() ? $request->user()->id : $request->ip();

        return "rate_limit_{$action}_{$identifier}";
    }

    /**
     * Get unique request ID.
     */
    private function getRequestId(): string
    {
        return request()->header('X-Request-ID', \Str::uuid());
    }

    /**
     * Handle command execution exception.
     */
    private function handleCommandException(\Exception $exception, Command $command): JsonResponse
    {
        logger()->error('Command execution failed', [
            'command' => get_class($command),
            'command_id' => $command->getId(),
            'user_id' => $command->getUser()?->id,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $status = $this->getExceptionStatus($exception);

        return $this->errorResponse(
            $exception->getMessage(),
            $status,
            [],
            get_class($exception),
            ['command_id' => $command->getId()]
        );
    }

    /**
     * Handle query execution exception.
     */
    private function handleQueryException(\Exception $exception, Query $query): JsonResponse
    {
        logger()->error('Query execution failed', [
            'query' => get_class($query),
            'query_id' => $query->getId(),
            'exception' => $exception->getMessage(),
        ]);

        $status = $this->getExceptionStatus($exception);

        return $this->errorResponse(
            'Failed to retrieve data',
            $status,
            [],
            get_class($exception),
            ['query_id' => $query->getId()]
        );
    }

    /**
     * Get command metadata for response.
     */
    private function getCommandMetadata(Command $command, ApplicationServiceInterface $service): array
    {
        return [
            'command_id' => $command->getId(),
            'service_metrics' => $service->getMetrics(),
        ];
    }

    /**
     * Get query metadata for response.
     */
    private function getQueryMetadata(Query $query, ApplicationServiceInterface $service): array
    {
        return [
            'query_id' => $query->getId(),
            'cached' => $query->isCacheable(),
            'service_metrics' => $service->getMetrics(),
        ];
    }

    /**
     * Get HTTP status code from exception.
     */
    private function getExceptionStatus(\Exception $exception): int
    {
        return match (true) {
            $exception instanceof ValidationException => 422,
            $exception instanceof AuthenticationException => 401,
            $exception instanceof AuthorizationException => 403,
            $exception instanceof ModelNotFoundException => 404,
            $exception instanceof ThrottleRequestsException => 429,
            default => 500
        };
    }
}
