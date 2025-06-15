<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestroyCompanyResource extends JsonResource
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
                'company_id' => $this->resource['company_id'] ?? null,
                'company_name' => $this->resource['company_name'] ?? null,
                'reason' => $this->resource['reason'] ?? null,
            ],
            'data_handling' => [
                'jobs_transferred' => $this->resource['jobs_transferred'] ?? false,
                'transfer_target' => $this->resource['transfer_target'] ?? null,
                'jobs_affected' => $this->resource['jobs_affected'] ?? 0,
                'applications_preserved' => $this->resource['applications_preserved'] ?? true,
                'employees_notified' => $this->resource['employees_notified'] ?? false,
            ],
            'cleanup' => [
                'related_data_removed' => $this->resource['cleanup_performed'] ?? true,
                'files_removed' => $this->resource['files_removed'] ?? [],
                'cache_cleared' => $this->resource['cache_cleared'] ?? true,
                'search_index_updated' => $this->resource['search_updated'] ?? true,
            ],
            'compliance' => [
                'gdpr_compliant' => true,
                'data_retention_policy' => 'Data retained for 30 days for recovery purposes',
                'audit_log_created' => $this->resource['audit_logged'] ?? true,
            ],
            'recovery' => [
                'recoverable_until' => now()->addDays(30)->toISOString(),
                'recovery_contact' => 'Contact support with company ID for recovery requests',
                'backup_reference' => $this->resource['backup_id'] ?? null,
            ],
            'notifications' => [
                'admin_notified' => $this->resource['admin_notified'] ?? true,
                'employees_notified' => $this->resource['employees_notified'] ?? false,
                'customers_notified' => $this->resource['customers_notified'] ?? false,
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
            'message' => __('messages.company_deleted'),
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'endpoint' => 'companies/destroy',
                'action' => 'soft_delete',
                'compliance' => [
                    'gdpr' => true,
                    'audit_trail' => true,
                    'data_retention' => '30_days',
                ],
            ],
            'warnings' => [
                'data_recovery' => __('messages.company_deletion_recovery_warning'),
                'employee_access' => __('messages.company_deletion_employee_warning'),
            ],
        ];
    }

    /**
     * Customize the response for the resource.
     *
     * @param mixed $response
     */
    public function withResponse(Request $request, $response): void
    {
        $response->setStatusCode(200);
        $response->header('X-Company-Deleted', 'true');
        $response->header('X-Recovery-Until', now()->addDays(30)->toISOString());
    }
}
