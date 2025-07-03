<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class AdminOperationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = $this->getUserManagementRules();
        $rules = array_merge($rules, $this->getSystemSettingsRules());
        $rules = array_merge($rules, $this->getContentModerationRules());
        $rules = array_merge($rules, $this->getAnalyticsRules());
        $rules = array_merge($rules, $this->getSecurityRules());
        $rules = array_merge($rules, $this->getAuditRules());
        $rules = array_merge($rules, $this->getMaintenanceRules());
        $rules = array_merge($rules, $this->getConfigurationRules());

        return $rules;
    }

    private function getUserManagementRules(): array
    {
        return [
            // User Management
            'user_ids' => ['nullable', 'array', 'max:5000'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'role_management' => ['nullable', 'boolean'],
            'permission_assignment' => ['nullable', 'boolean'],
            'bulk_user_operations' => ['nullable', 'boolean'],
            'user_impersonation' => ['nullable', 'boolean'],
            'force_password_reset' => ['nullable', 'boolean'],
            'account_suspension' => ['nullable', 'boolean'],
            'account_deletion' => ['nullable', 'boolean'],

            // Role Management
            'role_name' => ['nullable', 'string', 'max:100'],
            'role_description' => ['nullable', 'string', 'max:500'],
            'role_permissions' => ['nullable', 'array'],
            'role_permissions.*' => ['string', 'exists:permissions,name'],
            'role_hierarchy' => ['nullable', 'integer', 'min:1', 'max:10'],
            'role_inheritance' => ['nullable', 'boolean'],

            // User Activity Management
            'session_management' => ['nullable', 'boolean'],
            'force_logout' => ['nullable', 'boolean'],
            'concurrent_session_limit' => ['nullable', 'integer', 'min:1', 'max:10'],
            'login_attempt_tracking' => ['nullable', 'boolean'],
            'failed_login_threshold' => ['nullable', 'integer', 'min:3', 'max:20'],
            'account_lockout_duration' => ['nullable', 'integer', 'min:5', 'max:1440'], // minutes

            // Data Privacy Management
            'gdpr_compliance_tools' => ['nullable', 'boolean'],
            'data_export_request' => ['nullable', 'boolean'],
            'data_deletion_request' => ['nullable', 'boolean'],
            'consent_management' => ['nullable', 'boolean'],
            'privacy_settings_override' => ['nullable', 'boolean'],
            'data_anonymization' => ['nullable', 'boolean'],
        ];
    }

    private function getSystemSettingsRules(): array
    {
        return [
            // Global Settings
            'system_maintenance_mode' => ['nullable', 'boolean'],
            'registration_enabled' => ['nullable', 'boolean'],
            'email_verification_required' => ['nullable', 'boolean'],
            'admin_approval_required' => ['nullable', 'boolean'],
            'captcha_enabled' => ['nullable', 'boolean'],
            'rate_limiting_enabled' => ['nullable', 'boolean'],
            'debug_mode' => ['nullable', 'boolean'],

            // Email Configuration
            'smtp_settings' => ['nullable', 'array'],
            'email_queue_enabled' => ['nullable', 'boolean'],
            'notification_settings' => ['nullable', 'array'],
            'automated_emails' => ['nullable', 'boolean'],
            'email_templates_management' => ['nullable', 'boolean'],

            // File Upload Settings
            'max_upload_size_mb' => ['nullable', 'integer', 'min:1', 'max:100'],
            'allowed_file_types' => ['nullable', 'array'],
            'allowed_file_types.*' => ['string', 'max:10'],
            'virus_scanning_enabled' => ['nullable', 'boolean'],
            'file_quarantine' => ['nullable', 'boolean'],
            'cdn_integration' => ['nullable', 'boolean'],

            // Performance Settings
            'cache_enabled' => ['nullable', 'boolean'],
            'cache_duration_hours' => ['nullable', 'integer', 'min:1', 'max:168'],
            'database_optimization' => ['nullable', 'boolean'],
            'query_caching' => ['nullable', 'boolean'],
            'compression_enabled' => ['nullable', 'boolean'],
            'minification_enabled' => ['nullable', 'boolean'],
        ];
    }

    private function getContentModerationRules(): array
    {
        return [
            // Content Management
            'content_approval_workflow' => ['nullable', 'boolean'],
            'automated_content_scanning' => ['nullable', 'boolean'],
            'profanity_filter' => ['nullable', 'boolean'],
            'spam_detection' => ['nullable', 'boolean'],
            'content_flagging_system' => ['nullable', 'boolean'],
            'user_reporting_system' => ['nullable', 'boolean'],

            // Job Posting Moderation
            'job_auto_approval' => ['nullable', 'boolean'],
            'job_content_validation' => ['nullable', 'boolean'],
            'salary_range_validation' => ['nullable', 'boolean'],
            'company_verification' => ['nullable', 'boolean'],
            'duplicate_job_detection' => ['nullable', 'boolean'],

            // Profile Moderation
            'profile_photo_approval' => ['nullable', 'boolean'],
            'resume_content_scanning' => ['nullable', 'boolean'],
            'skill_verification' => ['nullable', 'boolean'],
            'experience_validation' => ['nullable', 'boolean'],
            'education_verification' => ['nullable', 'boolean'],

            // Communication Moderation
            'message_content_filtering' => ['nullable', 'boolean'],
            'automated_response_detection' => ['nullable', 'boolean'],
            'harassment_prevention' => ['nullable', 'boolean'],
            'inappropriate_contact_blocking' => ['nullable', 'boolean'],
        ];
    }

    private function getAnalyticsRules(): array
    {
        return [
            // System Analytics
            'user_activity_tracking' => ['nullable', 'boolean'],
            'performance_monitoring' => ['nullable', 'boolean'],
            'error_tracking' => ['nullable', 'boolean'],
            'api_usage_analytics' => ['nullable', 'boolean'],
            'database_performance_stats' => ['nullable', 'boolean'],
            'security_event_monitoring' => ['nullable', 'boolean'],

            // Business Analytics
            'conversion_tracking' => ['nullable', 'boolean'],
            'revenue_analytics' => ['nullable', 'boolean'],
            'user_engagement_metrics' => ['nullable', 'boolean'],
            'job_matching_effectiveness' => ['nullable', 'boolean'],
            'success_rate_analysis' => ['nullable', 'boolean'],

            // Reporting Configuration
            'automated_reports' => ['nullable', 'boolean'],
            'report_frequency' => ['nullable', 'string', Rule::in(['daily', 'weekly', 'monthly', 'quarterly'])],
            'custom_dashboards' => ['nullable', 'boolean'],
            'data_export_scheduling' => ['nullable', 'boolean'],
            'alert_thresholds' => ['nullable', 'array'],

            // Data Retention
            'log_retention_days' => ['nullable', 'integer', 'min:30', 'max:2555'], // 7 years max
            'analytics_data_retention' => ['nullable', 'integer', 'min:90', 'max:1095'], // 3 years max
            'inactive_user_cleanup' => ['nullable', 'integer', 'min:180', 'max:1095'],
        ];
    }

    private function getSecurityRules(): array
    {
        return [
            // Authentication Security
            'two_factor_enforcement' => ['nullable', 'boolean'],
            'password_complexity_rules' => ['nullable', 'array'],
            'session_timeout_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
            'ip_whitelist_enabled' => ['nullable', 'boolean'],
            'geo_blocking_enabled' => ['nullable', 'boolean'],
            'suspicious_activity_detection' => ['nullable', 'boolean'],

            // Data Security
            'encryption_at_rest' => ['nullable', 'boolean'],
            'encryption_in_transit' => ['nullable', 'boolean'],
            'data_masking' => ['nullable', 'boolean'],
            'secure_file_storage' => ['nullable', 'boolean'],
            'backup_encryption' => ['nullable', 'boolean'],

            // API Security
            'api_rate_limiting' => ['nullable', 'integer', 'min:100', 'max:10000'],
            'api_key_rotation' => ['nullable', 'boolean'],
            'webhook_security' => ['nullable', 'boolean'],
            'cors_configuration' => ['nullable', 'array'],
            'request_signing' => ['nullable', 'boolean'],

            // Vulnerability Management
            'security_scanning' => ['nullable', 'boolean'],
            'penetration_testing_reports' => ['nullable', 'boolean'],
            'vulnerability_alerts' => ['nullable', 'boolean'],
            'security_patch_management' => ['nullable', 'boolean'],
        ];
    }

    private function getAuditRules(): array
    {
        return [
            // Audit Configuration
            'audit_logging_enabled' => ['nullable', 'boolean'],
            'detailed_audit_trail' => ['nullable', 'boolean'],
            'user_action_logging' => ['nullable', 'boolean'],
            'admin_action_logging' => ['nullable', 'boolean'],
            'data_change_tracking' => ['nullable', 'boolean'],
            'access_logging' => ['nullable', 'boolean'],

            // Compliance Reporting
            'compliance_reports' => ['nullable', 'boolean'],
            'regulatory_reporting' => ['nullable', 'boolean'],
            'data_breach_procedures' => ['nullable', 'boolean'],
            'incident_response_plan' => ['nullable', 'boolean'],
            'forensic_data_collection' => ['nullable', 'boolean'],

            // Log Management
            'log_aggregation' => ['nullable', 'boolean'],
            'log_analysis_tools' => ['nullable', 'boolean'],
            'real_time_monitoring' => ['nullable', 'boolean'],
            'alert_notifications' => ['nullable', 'boolean'],
            'log_archival' => ['nullable', 'boolean'],
        ];
    }

    private function getMaintenanceRules(): array
    {
        return [
            // System Maintenance
            'scheduled_maintenance' => ['nullable', 'boolean'],
            'maintenance_window_start' => ['nullable', 'date_format:H:i'],
            'maintenance_window_end' => ['nullable', 'date_format:H:i'],
            'maintenance_notification' => ['nullable', 'boolean'],
            'graceful_shutdown' => ['nullable', 'boolean'],

            // Database Maintenance
            'database_optimization_schedule' => ['nullable', 'string', Rule::in(['daily', 'weekly', 'monthly'])],
            'index_rebuilding' => ['nullable', 'boolean'],
            'statistics_update' => ['nullable', 'boolean'],
            'cleanup_old_data' => ['nullable', 'boolean'],
            'backup_scheduling' => ['nullable', 'array'],

            // Performance Optimization
            'cache_warming' => ['nullable', 'boolean'],
            'image_optimization' => ['nullable', 'boolean'],
            'cdn_cache_purging' => ['nullable', 'boolean'],
            'unused_file_cleanup' => ['nullable', 'boolean'],
            'session_cleanup' => ['nullable', 'boolean'],
        ];
    }

    private function getConfigurationRules(): array
    {
        return [
            // Application Configuration
            'app_name' => ['nullable', 'string', 'max:255'],
            'app_url' => ['nullable', 'url', 'max:255'],
            'app_timezone' => ['nullable', 'string', 'timezone'],
            'default_language' => ['nullable', 'string', 'size:2'],
            'supported_languages' => ['nullable', 'array'],
            'supported_languages.*' => ['string', 'size:2'],

            // Feature Toggles
            'feature_flags' => ['nullable', 'array'],
            'beta_features_enabled' => ['nullable', 'boolean'],
            'experimental_features' => ['nullable', 'boolean'],
            'a_b_testing_enabled' => ['nullable', 'boolean'],
            'rollback_capabilities' => ['nullable', 'boolean'],

            // Integration Configuration
            'third_party_integrations' => ['nullable', 'array'],
            'api_integrations' => ['nullable', 'array'],
            'webhook_configurations' => ['nullable', 'array'],
            'payment_gateway_settings' => ['nullable', 'array'],
            'social_login_providers' => ['nullable', 'array'],

            // Advanced Configuration
            'custom_css_enabled' => ['nullable', 'boolean'],
            'custom_javascript_enabled' => ['nullable', 'boolean'],
            'white_label_customization' => ['nullable', 'boolean'],
            'multi_tenant_support' => ['nullable', 'boolean'],
            'plugin_system_enabled' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // User Management Messages
            'user_ids.max' => __('validation.admin_operations.user_ids_limit_exceeded'),
            'failed_login_threshold.min' => __('validation.admin_operations.failed_login_threshold_too_low'),
            'account_lockout_duration.max' => __('validation.admin_operations.lockout_duration_too_long'),

            // System Settings Messages
            'max_upload_size_mb.max' => __('validation.admin_operations.upload_size_too_large'),
            'cache_duration_hours.max' => __('validation.admin_operations.cache_duration_too_long'),

            // Analytics Messages
            'log_retention_days.min' => __('validation.admin_operations.log_retention_too_short'),
            'analytics_data_retention.max' => __('validation.admin_operations.analytics_retention_too_long'),

            // Security Messages
            'session_timeout_minutes.min' => __('validation.admin_operations.session_timeout_too_short'),
            'api_rate_limiting.min' => __('validation.admin_operations.api_rate_limit_too_low'),
        ];
    }

    protected function passedValidation(): void
    {
        $this->validateAdminConfiguration();
        $this->optimizeAdminPerformance();
        $this->logAdminActivity();
    }

    private function validateAdminConfiguration(): void
    {
        // Validate maintenance window
        if ($this->has(['maintenance_window_start', 'maintenance_window_end'])) {
            $start = strtotime($this->maintenance_window_start);
            $end = strtotime($this->maintenance_window_end);

            if ($end <= $start) {
                throw new \InvalidArgumentException(__('validation.admin_operations.maintenance_window_invalid'));
            }
        }

        // Validate security settings
        if ($this->has('session_timeout_minutes') && $this->has('account_lockout_duration')) {
            if ($this->account_lockout_duration < $this->session_timeout_minutes) {
                throw new \InvalidArgumentException(__('validation.admin_operations.lockout_shorter_than_session'));
            }
        }
    }

    private function optimizeAdminPerformance(): void
    {
        // Cache admin configuration
        if ($this->has('cache_enabled') && $this->cache_enabled) {
            Cache::remember('admin_config', 3600, function () {
                return $this->validated();
            });
        }
    }

    private function logAdminActivity(): void
    {
        \Log::info('Admin Operations Request', [
            'admin_action' => $this->getAdminActionType(),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'timestamp' => now(),
            'critical_operation' => $this->isCriticalOperation(),
        ]);
    }

    private function getAdminActionType(): string
    {
        if ($this->has('user_ids')) {
            return 'user_management';
        }
        if ($this->has('system_maintenance_mode')) {
            return 'system_settings';
        }
        if ($this->has('audit_logging_enabled')) {
            return 'security_audit';
        }
        if ($this->has('scheduled_maintenance')) {
            return 'maintenance';
        }

        return 'general_admin_operation';
    }

    private function isCriticalOperation(): bool
    {
        $criticalFields = [
            'account_deletion', 'system_maintenance_mode', 'debug_mode',
            'two_factor_enforcement', 'encryption_at_rest', 'data_deletion_request',
        ];

        foreach ($criticalFields as $field) {
            if ($this->has($field) && $this->input($field) === true) {
                return true;
            }
        }

        return false;
    }
}
