<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestroyJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'deletion' => [
                'successful' => true,
                'deleted_at' => now()->toISOString(),
                'job_id' => $this->resource['job_id'] ?? null,
                'job_title' => $this->resource['job_title'] ?? null,
                'reason' => $this->resource['reason'] ?? null,
            ],
            'applications' => [
                'total_affected' => $this->resource['applications_affected'] ?? 0,
                'active_applications' => $this->resource['active_applications'] ?? 0,
                'applicants_notified' => $this->resource['applicants_notified'] ?? false,
                'notification_method' => $this->resource['notification_method'] ?? 'email',
                'applications_preserved' => true,
            ],
            'financial' => [
                'featured_refund_processed' => $this->resource['refund_processed'] ?? false,
                'refund_amount' => $this->resource['refund_amount'] ?? null,
                'refund_currency' => $this->resource['refund_currency'] ?? null,
                'refund_reference' => $this->resource['refund_reference'] ?? null,
            ],
            'cleanup' => [
                'search_index_updated' => $this->resource['search_updated'] ?? true,
                'cache_cleared' => $this->resource['cache_cleared'] ?? true,
                'related_data_handled' => $this->resource['cleanup_performed'] ?? true,
                'files_removed' => $this->resource['files_removed'] ?? [],
            ],
            'recovery' => [
                'recoverable_until' => now()->addDays(30)->toISOString(),
                'recovery_contact' => 'Contact support with job ID for recovery requests',
                'backup_reference' => $this->resource['backup_id'] ?? null,
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => __('messages.job_deleted'),
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'endpoint' => 'jobs/destroy',
                'action' => 'soft_delete',
            ],
        ];
    }

    /**
     * Customize the response for the resource.
     *
     * @param  mixed  $response
     */
    public function withResponse(Request $request, $response): void
    {
        $response->setStatusCode(200);
        $response->header('X-Job-Deleted', 'true');
        $response->header('X-Recovery-Until', now()->addDays(30)->toISOString());
    }
}
