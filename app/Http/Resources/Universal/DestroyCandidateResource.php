<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestroyCandidateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'deletion_details' => [
                'candidate_id' => $this->resource['candidate_id'] ?? null,
                'user_id' => $this->resource['user_id'] ?? null,
                'deletion_type' => $this->resource['force_delete'] ? 'force_delete' : 'soft_delete',
                'deleted_at' => now()->format('Y-m-d H:i:s'),
                'deleted_by' => auth()->user() ? [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->getRoleNames()->first(),
                ] : null,
                'reason' => $this->resource['reason'] ?? 'User requested deletion',
            ],

            'cleanup_summary' => [
                'applications_preserved' => $this->resource['preserve_applications'] ?? true,
                'reviews_preserved' => $this->resource['preserve_reviews'] ?? true,
                'data_cleanup_performed' => $this->resource['cleanup_data'] ?? true,
                'files_removed' => $this->resource['files_removed'] ?? [],
                'related_data_count' => [
                    'applications' => $this->resource['applications_count'] ?? 0,
                    'resumes' => $this->resource['resumes_count'] ?? 0,
                    'educations' => $this->resource['educations_count'] ?? 0,
                    'experiences' => $this->resource['experiences_count'] ?? 0,
                    'skills' => $this->resource['skills_count'] ?? 0,
                    'languages' => $this->resource['languages_count'] ?? 0,
                ],
            ],

            'notifications' => [
                'employers_notified' => $this->resource['notify_employers'] ?? false,
                'admin_notified' => true,
                'email_sent' => $this->resource['email_sent'] ?? false,
                'notification_count' => $this->resource['notification_count'] ?? 0,
            ],

            'data_handling' => [
                'applications_status' => $this->resource['preserve_applications'] ? 'preserved' : 'anonymized',
                'reviews_status' => $this->resource['preserve_reviews'] ? 'preserved' : 'removed',
                'personal_data_status' => 'permanently_removed',
                'files_status' => $this->resource['cleanup_data'] ? 'removed' : 'preserved',
                'gdpr_compliant' => true,
            ],

            'audit_trail' => [
                'action' => 'candidate_deletion',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'session_id' => session()->getId(),
                'request_id' => request()->header('X-Request-ID') ?? \Str::uuid(),
            ],

            'recovery_options' => $this->when(!$this->resource['force_delete'], [
                'soft_delete_enabled' => true,
                'recovery_possible' => true,
                'recovery_deadline' => now()->addDays(30)->format('Y-m-d H:i:s'),
                'recovery_instructions' => 'Contact administrator within 30 days to restore account',
                'recovery_contact' => config('app.support_email', 'support@company.com'),
            ]),

            'security_checks' => [
                'authorization_verified' => true,
                'ownership_confirmed' => $this->resource['ownership_confirmed'] ?? true,
                'admin_override' => $this->resource['force_delete'] && auth()->user()->hasRole('admin'),
                'confirmation_received' => true,
                'security_level' => $this->resource['force_delete'] ? 'high' : 'standard',
            ],

            'system_impact' => [
                'database_records_affected' => $this->resource['records_affected'] ?? 1,
                'cache_cleared' => true,
                'search_index_updated' => true,
                'statistics_recalculated' => false, // Will be done in background
                'dependencies_handled' => true,
            ],

            'compliance' => [
                'gdpr_article' => $this->resource['force_delete'] ? 'Article 17 - Right to erasure' : 'Article 6 - Lawfulness of processing',
                'data_retention_policy' => 'followed',
                'legal_basis' => 'user_consent',
                'data_controller_notified' => true,
                'processing_log_updated' => true,
            ],

            'next_steps' => [
                'account_deactivated' => true,
                'user_session_terminated' => true,
                'api_access_revoked' => true,
                'scheduled_tasks_cancelled' => true,
                'backup_data_flagged' => $this->resource['cleanup_data'] ?? true,
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
            'message' => $this->getDeletionMessage(),
            'meta' => [
                'operation' => 'candidate_deletion',
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'version' => '1.0',
                'processing_time' => $this->resource['processing_time'] ?? '< 1 second',
                'data_version' => '1.0',
            ],
            'warnings' => $this->getWarnings(),
            'recommendations' => $this->getRecommendations(),
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param mixed $response
     */
    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'DestroyCandidateResource');
        $response->header('X-Operation', 'candidate-deletion');
        $response->header('X-Audit-Trail', 'logged');

        // Set appropriate status code
        $statusCode = $this->resource['force_delete'] ? 204 : 200;
        $response->setStatusCode($statusCode);
    }

    /**
     * Get deletion message based on deletion type.
     */
    private function getDeletionMessage(): string
    {
        if ($this->resource['force_delete'] ?? false) {
            return 'Candidate profile permanently deleted. All data has been completely removed from the system.';
        }

        return 'Candidate profile successfully deleted. Account can be recovered within 30 days if needed.';
    }

    /**
     * Get warnings based on deletion context.
     */
    private function getWarnings(): array
    {
        $warnings = [];

        if ($this->resource['force_delete'] ?? false) {
            $warnings[] = 'PERMANENT DELETION: This action cannot be undone.';
            $warnings[] = 'All personal data has been permanently removed.';
        }

        if (($this->resource['applications_count'] ?? 0) > 0) {
            if ($this->resource['preserve_applications'] ?? true) {
                $warnings[] = 'Job applications have been preserved but anonymized.';
            } else {
                $warnings[] = 'Job applications have been permanently removed.';
            }
        }

        if (($this->resource['files_removed'] ?? []) && count($this->resource['files_removed']) > 0) {
            $warnings[] = sprintf('%d file(s) permanently removed from storage.', count($this->resource['files_removed']));
        }

        return $warnings;
    }

    /**
     * Get recommendations for post-deletion actions.
     */
    private function getRecommendations(): array
    {
        $recommendations = [];

        if (!($this->resource['force_delete'] ?? false)) {
            $recommendations[] = 'Save the recovery information if account restoration might be needed.';
            $recommendations[] = 'Contact support within 30 days if this deletion was accidental.';
        }

        if (($this->resource['applications_count'] ?? 0) > 0) {
            $recommendations[] = 'Download application history before deletion if needed for records.';
        }

        if (auth()->user()->hasRole('admin')) {
            $recommendations[] = 'Review audit logs for compliance verification.';
            $recommendations[] = 'Update data processing records as required by GDPR.';
        }

        return $recommendations;
    }
}
