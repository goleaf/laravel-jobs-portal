<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class RealTimeOperationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = $this->getWebSocketRules();
        $rules = array_merge($rules, $this->getNotificationRules());
        $rules = array_merge($rules, $this->getMessagingRules());
        $rules = array_merge($rules, $this->getLiveDataStreamingRules());
        $rules = array_merge($rules, $this->getCollaborationRules());
        $rules = array_merge($rules, $this->getEventProcessingRules());
        $rules = array_merge($rules, $this->getPerformanceOptimizationRules());
        $rules = array_merge($rules, $this->getSecurityRules());
        $rules = array_merge($rules, $this->getMonitoringRules());
        $rules = array_merge($rules, $this->getAdvancedFeaturesRules());

        return $rules;
    }

    private function getWebSocketRules(): array
    {
        return [
            // WebSocket Connection Management
            'connection_id' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\-_]+$/'],
            'socket_type' => ['nullable', 'string', Rule::in(['websocket', 'socket_io', 'sse', 'long_polling', 'webhook'])],
            'connection_timeout' => ['nullable', 'integer', 'min:5', 'max:300'], // seconds
            'heartbeat_interval' => ['nullable', 'integer', 'min:10', 'max:120'], // seconds
            'reconnection_attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
            'reconnection_delay' => ['nullable', 'integer', 'min:1000', 'max:30000'], // milliseconds
            'max_concurrent_connections' => ['nullable', 'integer', 'min:1', 'max:10000'],

            // Channel Management
            'channel_name' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9\-_\.]+$/'],
            'channel_type' => ['nullable', 'string', Rule::in(['public', 'private', 'presence', 'encrypted'])],
            'channel_capacity' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'channel_persistence' => ['nullable', 'boolean'],
            'channel_history_limit' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'auto_join_channels' => ['nullable', 'array'],
            'auto_join_channels.*' => ['string', 'max:100'],

            // Authentication & Authorization
            'socket_auth_required' => ['nullable', 'boolean'],
            'auth_token' => ['nullable', 'string', 'max:500'],
            'auth_signature' => ['nullable', 'string', 'max:255'],
            'permission_levels' => ['nullable', 'array'],
            'permission_levels.*' => ['string', Rule::in(['read', 'write', 'admin', 'moderator'])],
            'access_control_list' => ['nullable', 'array'],
            'ip_whitelist' => ['nullable', 'array'],
            'ip_whitelist.*' => ['ip'],

            // Message Broadcasting
            'broadcast_to_channels' => ['nullable', 'array'],
            'broadcast_to_channels.*' => ['string', 'max:100'],
            'broadcast_to_users' => ['nullable', 'array'],
            'broadcast_to_users.*' => ['integer', 'exists:users,id'],
            'broadcast_exclude_self' => ['nullable', 'boolean'],
            'broadcast_priority' => ['nullable', 'string', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'message_ttl' => ['nullable', 'integer', 'min:1', 'max:86400'], // seconds
        ];
    }

    private function getNotificationRules(): array
    {
        return [
            // Real-time Notifications
            'notification_type' => ['nullable', 'string', Rule::in(['job_match', 'application_status', 'message', 'system_alert', 'reminder', 'promotion', 'social_update'])],
            'notification_priority' => ['nullable', 'string', Rule::in(['low', 'normal', 'high', 'urgent', 'critical'])],
            'notification_category' => ['nullable', 'string', Rule::in(['personal', 'work', 'system', 'social', 'marketing'])],
            'recipient_ids' => ['nullable', 'array', 'max:1000'],
            'recipient_ids.*' => ['integer', 'exists:users,id'],
            'recipient_groups' => ['nullable', 'array'],
            'recipient_groups.*' => ['string', Rule::in(['all_users', 'job_seekers', 'employers', 'admins', 'premium_users'])],

            // Notification Content
            'notification_title' => ['nullable', 'string', 'max:200'],
            'notification_message' => ['nullable', 'string', 'max:1000'],
            'notification_data' => ['nullable', 'array'],
            'action_url' => ['nullable', 'url', 'max:500'],
            'action_label' => ['nullable', 'string', 'max:50'],
            'notification_image' => ['nullable', 'string', 'max:500'],
            'notification_icon' => ['nullable', 'string', 'max:100'],

            // Delivery Configuration
            'delivery_methods' => ['nullable', 'array'],
            'delivery_methods.*' => ['string', Rule::in(['push', 'email', 'sms', 'in_app', 'slack', 'webhook'])],
            'immediate_delivery' => ['nullable', 'boolean'],
            'scheduled_delivery' => ['nullable', 'date', 'after:now'],
            'batch_delivery' => ['nullable', 'boolean'],
            'batch_size' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'delivery_rate_limit' => ['nullable', 'integer', 'min:1', 'max:1000'], // per minute

            // Personalization
            'personalization_enabled' => ['nullable', 'boolean'],
            'user_preferences_respected' => ['nullable', 'boolean'],
            'timezone_aware' => ['nullable', 'boolean'],
            'language_localization' => ['nullable', 'boolean'],
            'content_customization' => ['nullable', 'array'],
            'a_b_test_variant' => ['nullable', 'string', 'max:50'],

            // Tracking & Analytics
            'track_delivery' => ['nullable', 'boolean'],
            'track_opens' => ['nullable', 'boolean'],
            'track_clicks' => ['nullable', 'boolean'],
            'track_conversions' => ['nullable', 'boolean'],
            'analytics_tags' => ['nullable', 'array'],
            'campaign_id' => ['nullable', 'string', 'max:100'],
        ];
    }

    private function getMessagingRules(): array
    {
        return [
            // Real-time Messaging
            'message_type' => ['nullable', 'string', Rule::in(['text', 'rich_text', 'image', 'file', 'video', 'audio', 'system', 'automated'])],
            'conversation_id' => ['nullable', 'string', 'max:255'],
            'thread_id' => ['nullable', 'string', 'max:255'],
            'parent_message_id' => ['nullable', 'string', 'max:255'],
            'message_content' => ['nullable', 'string', 'max:5000'],
            'message_metadata' => ['nullable', 'array'],

            // Participants Management
            'sender_id' => ['nullable', 'integer', 'exists:users,id'],
            'recipient_id' => ['nullable', 'integer', 'exists:users,id'],
            'participants' => ['nullable', 'array', 'max:50'],
            'participants.*' => ['integer', 'exists:users,id'],
            'conversation_type' => ['nullable', 'string', Rule::in(['direct', 'group', 'broadcast', 'channel'])],
            'max_participants' => ['nullable', 'integer', 'min:2', 'max:100'],

            // Message Features
            'message_encryption' => ['nullable', 'boolean'],
            'end_to_end_encryption' => ['nullable', 'boolean'],
            'message_expiry' => ['nullable', 'integer', 'min:60', 'max:604800'], // seconds
            'self_destruct' => ['nullable', 'boolean'],
            'read_receipts' => ['nullable', 'boolean'],
            'typing_indicators' => ['nullable', 'boolean'],
            'message_reactions' => ['nullable', 'boolean'],
            'message_threading' => ['nullable', 'boolean'],

            // File Sharing
            'file_attachments' => ['nullable', 'array', 'max:10'],
            'file_attachments.*.filename' => ['string', 'max:255'],
            'file_attachments.*.size' => ['integer', 'min:1', 'max:104857600'], // 100MB
            'file_attachments.*.type' => ['string', 'max:100'],
            'file_sharing_enabled' => ['nullable', 'boolean'],
            'max_file_size' => ['nullable', 'integer', 'min:1048576', 'max:104857600'], // 1MB to 100MB
            'allowed_file_types' => ['nullable', 'array'],
            'allowed_file_types.*' => ['string', 'max:10'],

            // Moderation
            'content_moderation' => ['nullable', 'boolean'],
            'profanity_filter' => ['nullable', 'boolean'],
            'spam_detection' => ['nullable', 'boolean'],
            'auto_moderation_actions' => ['nullable', 'array'],
            'auto_moderation_actions.*' => ['string', Rule::in(['warn', 'mute', 'block', 'report'])],
            'message_reporting' => ['nullable', 'boolean'],
            'admin_oversight' => ['nullable', 'boolean'],
        ];
    }

    private function getLiveDataStreamingRules(): array
    {
        return [
            // Data Streaming Configuration
            'stream_type' => ['nullable', 'string', Rule::in(['job_updates', 'application_status', 'market_data', 'analytics', 'system_metrics', 'user_activity'])],
            'stream_format' => ['nullable', 'string', Rule::in(['json', 'protobuf', 'avro', 'msgpack'])],
            'stream_compression' => ['nullable', 'string', Rule::in(['none', 'gzip', 'deflate', 'br'])],
            'batch_size' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'batch_timeout' => ['nullable', 'integer', 'min:100', 'max:30000'], // milliseconds
            'stream_buffer_size' => ['nullable', 'integer', 'min:1024', 'max:1048576'], // bytes

            // Data Filtering
            'filter_criteria' => ['nullable', 'array'],
            'geographic_filters' => ['nullable', 'array'],
            'geographic_filters.*.country' => ['string', 'size:2'],
            'geographic_filters.*.region' => ['string', 'max:100'],
            'industry_filters' => ['nullable', 'array'],
            'industry_filters.*' => ['string', 'max:100'],
            'skill_filters' => ['nullable', 'array'],
            'skill_filters.*' => ['integer', 'exists:skills,id'],
            'date_range_filters' => ['nullable', 'array'],
            'date_range_filters.start_date' => ['date'],
            'date_range_filters.end_date' => ['date', 'after:date_range_filters.start_date'],

            // Real-time Analytics
            'analytics_enabled' => ['nullable', 'boolean'],
            'metrics_collection' => ['nullable', 'array'],
            'metrics_collection.*' => ['string', Rule::in(['views', 'clicks', 'applications', 'matches', 'conversions'])],
            'real_time_aggregation' => ['nullable', 'boolean'],
            'sliding_window_size' => ['nullable', 'integer', 'min:60', 'max:86400'], // seconds
            'anomaly_detection' => ['nullable', 'boolean'],
            'trend_analysis' => ['nullable', 'boolean'],
            'predictive_insights' => ['nullable', 'boolean'],

            // Performance Optimization
            'data_caching' => ['nullable', 'boolean'],
            'cache_duration' => ['nullable', 'integer', 'min:60', 'max:3600'], // seconds
            'compression_level' => ['nullable', 'integer', 'min:1', 'max:9'],
            'stream_priority' => ['nullable', 'string', Rule::in(['low', 'normal', 'high'])],
            'bandwidth_throttling' => ['nullable', 'boolean'],
            'max_bandwidth_mbps' => ['nullable', 'numeric', 'min:0.1', 'max:1000'],
        ];
    }

    private function getCollaborationRules(): array
    {
        return [
            // Real-time Collaboration
            'collaboration_type' => ['nullable', 'string', Rule::in(['document_editing', 'interview_scheduling', 'team_review', 'shared_workspace'])],
            'session_id' => ['nullable', 'string', 'max:255'],
            'workspace_id' => ['nullable', 'string', 'max:255'],
            'document_id' => ['nullable', 'string', 'max:255'],
            'collaboration_mode' => ['nullable', 'string', Rule::in(['real_time', 'turn_based', 'async'])],

            // Participant Management
            'collaborators' => ['nullable', 'array', 'max:20'],
            'collaborators.*' => ['integer', 'exists:users,id'],
            'permission_matrix' => ['nullable', 'array'],
            'permission_matrix.*.user_id' => ['integer', 'exists:users,id'],
            'permission_matrix.*.permissions' => ['array'],
            'permission_matrix.*.permissions.*' => ['string', Rule::in(['read', 'write', 'comment', 'admin'])],
            'guest_access_enabled' => ['nullable', 'boolean'],
            'public_collaboration' => ['nullable', 'boolean'],

            // Live Editing Features
            'concurrent_editing' => ['nullable', 'boolean'],
            'operational_transform' => ['nullable', 'boolean'],
            'conflict_resolution' => ['nullable', 'string', Rule::in(['last_write_wins', 'manual_resolution', 'automatic_merge'])],
            'version_control' => ['nullable', 'boolean'],
            'change_tracking' => ['nullable', 'boolean'],
            'live_cursors' => ['nullable', 'boolean'],
            'user_presence_indicators' => ['nullable', 'boolean'],

            // Communication Integration
            'voice_chat_enabled' => ['nullable', 'boolean'],
            'video_chat_enabled' => ['nullable', 'boolean'],
            'screen_sharing' => ['nullable', 'boolean'],
            'whiteboard_enabled' => ['nullable', 'boolean'],
            'annotation_tools' => ['nullable', 'boolean'],
            'comment_system' => ['nullable', 'boolean'],
            'mention_notifications' => ['nullable', 'boolean'],

            // Session Management
            'session_timeout' => ['nullable', 'integer', 'min:300', 'max:28800'], // 5 minutes to 8 hours
            'auto_save_interval' => ['nullable', 'integer', 'min:10', 'max:300'], // seconds
            'session_recording' => ['nullable', 'boolean'],
            'activity_logging' => ['nullable', 'boolean'],
            'session_replay' => ['nullable', 'boolean'],
        ];
    }

    private function getEventProcessingRules(): array
    {
        return [
            // Event Processing
            'event_type' => ['nullable', 'string', Rule::in(['user_action', 'system_event', 'business_event', 'analytics_event', 'error_event'])],
            'event_source' => ['nullable', 'string', 'max:100'],
            'event_timestamp' => ['nullable', 'date'],
            'event_payload' => ['nullable', 'array'],
            'event_correlation_id' => ['nullable', 'string', 'max:255'],
            'event_sequence_number' => ['nullable', 'integer', 'min:1'],

            // Event Routing
            'routing_rules' => ['nullable', 'array'],
            'routing_rules.*.condition' => ['string', 'max:500'],
            'routing_rules.*.action' => ['string', 'max:200'],
            'routing_rules.*.priority' => ['integer', 'min:1', 'max:10'],
            'dead_letter_queue' => ['nullable', 'boolean'],
            'retry_policy' => ['nullable', 'array'],
            'retry_policy.max_attempts' => ['integer', 'min:1', 'max:10'],
            'retry_policy.backoff_strategy' => ['string', Rule::in(['fixed', 'exponential', 'linear'])],

            // Complex Event Processing
            'event_correlation' => ['nullable', 'boolean'],
            'pattern_matching' => ['nullable', 'boolean'],
            'temporal_windows' => ['nullable', 'array'],
            'temporal_windows.*.duration' => ['integer', 'min:1', 'max:3600'],
            'temporal_windows.*.type' => ['string', Rule::in(['tumbling', 'sliding', 'session'])],
            'aggregation_functions' => ['nullable', 'array'],
            'aggregation_functions.*' => ['string', Rule::in(['count', 'sum', 'avg', 'min', 'max', 'distinct'])],

            // Event Enrichment
            'data_enrichment' => ['nullable', 'boolean'],
            'external_data_sources' => ['nullable', 'array'],
            'enrichment_rules' => ['nullable', 'array'],
            'contextual_data' => ['nullable', 'array'],
            'user_context_enrichment' => ['nullable', 'boolean'],
            'geographic_enrichment' => ['nullable', 'boolean'],
            'behavioral_enrichment' => ['nullable', 'boolean'],
        ];
    }

    private function getPerformanceOptimizationRules(): array
    {
        return [
            // Performance Configuration
            'load_balancing' => ['nullable', 'boolean'],
            'horizontal_scaling' => ['nullable', 'boolean'],
            'auto_scaling_enabled' => ['nullable', 'boolean'],
            'scaling_metrics' => ['nullable', 'array'],
            'scaling_metrics.*' => ['string', Rule::in(['cpu_usage', 'memory_usage', 'connection_count', 'message_rate'])],
            'min_instances' => ['nullable', 'integer', 'min:1', 'max:100'],
            'max_instances' => ['nullable', 'integer', 'min:1', 'max:1000'],

            // Caching Strategy
            'distributed_caching' => ['nullable', 'boolean'],
            'cache_strategy' => ['nullable', 'string', Rule::in(['write_through', 'write_behind', 'cache_aside'])],
            'cache_ttl' => ['nullable', 'integer', 'min:60', 'max:86400'],
            'cache_invalidation' => ['nullable', 'string', Rule::in(['time_based', 'event_based', 'manual'])],
            'cache_partitioning' => ['nullable', 'boolean'],
            'cache_replication' => ['nullable', 'boolean'],

            // Message Queue Optimization
            'queue_type' => ['nullable', 'string', Rule::in(['redis', 'rabbitmq', 'apache_kafka', 'amazon_sqs'])],
            'queue_partitioning' => ['nullable', 'boolean'],
            'message_ordering' => ['nullable', 'boolean'],
            'duplicate_detection' => ['nullable', 'boolean'],
            'message_compression' => ['nullable', 'boolean'],
            'batch_processing' => ['nullable', 'boolean'],

            // Connection Optimization
            'connection_pooling' => ['nullable', 'boolean'],
            'keep_alive_enabled' => ['nullable', 'boolean'],
            'connection_multiplexing' => ['nullable', 'boolean'],
            'tcp_no_delay' => ['nullable', 'boolean'],
            'socket_buffer_size' => ['nullable', 'integer', 'min:8192', 'max:1048576'],
        ];
    }

    private function getSecurityRules(): array
    {
        return [
            // Security Configuration
            'security_level' => ['nullable', 'string', Rule::in(['basic', 'standard', 'high', 'maximum'])],
            'encryption_enabled' => ['nullable', 'boolean'],
            'encryption_algorithm' => ['nullable', 'string', Rule::in(['aes_256', 'chacha20', 'aes_128'])],
            'message_signing' => ['nullable', 'boolean'],
            'signature_algorithm' => ['nullable', 'string', Rule::in(['hmac_sha256', 'rsa_pss', 'ecdsa'])],

            // Access Control
            'rate_limiting' => ['nullable', 'boolean'],
            'rate_limit_per_minute' => ['nullable', 'integer', 'min:10', 'max:10000'],
            'ddos_protection' => ['nullable', 'boolean'],
            'geo_blocking' => ['nullable', 'boolean'],
            'blocked_countries' => ['nullable', 'array'],
            'blocked_countries.*' => ['string', 'size:2'],
            'user_agent_filtering' => ['nullable', 'boolean'],

            // Threat Detection
            'anomaly_detection' => ['nullable', 'boolean'],
            'behavioral_analysis' => ['nullable', 'boolean'],
            'intrusion_detection' => ['nullable', 'boolean'],
            'automated_blocking' => ['nullable', 'boolean'],
            'threat_intelligence' => ['nullable', 'boolean'],
            'security_alerts' => ['nullable', 'boolean'],

            // Compliance
            'audit_logging' => ['nullable', 'boolean'],
            'compliance_mode' => ['nullable', 'string', Rule::in(['gdpr', 'hipaa', 'sox', 'pci_dss'])],
            'data_residency' => ['nullable', 'string', 'max:100'],
            'retention_policy' => ['nullable', 'integer', 'min:30', 'max:2555'], // days
        ];
    }

    private function getMonitoringRules(): array
    {
        return [
            // Monitoring Configuration
            'monitoring_enabled' => ['nullable', 'boolean'],
            'metrics_collection' => ['nullable', 'boolean'],
            'custom_metrics' => ['nullable', 'array'],
            'health_checks' => ['nullable', 'boolean'],
            'uptime_monitoring' => ['nullable', 'boolean'],
            'performance_monitoring' => ['nullable', 'boolean'],

            // Alerting
            'alert_thresholds' => ['nullable', 'array'],
            'alert_thresholds.*.metric' => ['string', 'max:100'],
            'alert_thresholds.*.threshold' => ['numeric'],
            'alert_thresholds.*.operator' => ['string', Rule::in(['gt', 'lt', 'eq', 'gte', 'lte'])],
            'notification_channels' => ['nullable', 'array'],
            'notification_channels.*' => ['string', Rule::in(['email', 'slack', 'webhook', 'sms'])],
            'escalation_rules' => ['nullable', 'array'],

            // Logging
            'structured_logging' => ['nullable', 'boolean'],
            'log_level' => ['nullable', 'string', Rule::in(['debug', 'info', 'warning', 'error', 'critical'])],
            'log_sampling_rate' => ['nullable', 'numeric', 'min:0.01', 'max:1.0'],
            'log_aggregation' => ['nullable', 'boolean'],
            'log_retention_days' => ['nullable', 'integer', 'min:7', 'max:365'],
        ];
    }

    private function getAdvancedFeaturesRules(): array
    {
        return [
            // AI/ML Integration
            'ai_powered_features' => ['nullable', 'boolean'],
            'sentiment_analysis' => ['nullable', 'boolean'],
            'content_personalization' => ['nullable', 'boolean'],
            'predictive_notifications' => ['nullable', 'boolean'],
            'intelligent_routing' => ['nullable', 'boolean'],
            'auto_response_generation' => ['nullable', 'boolean'],

            // Advanced Analytics
            'real_time_analytics' => ['nullable', 'boolean'],
            'user_journey_tracking' => ['nullable', 'boolean'],
            'conversion_funnel_analysis' => ['nullable', 'boolean'],
            'cohort_analysis' => ['nullable', 'boolean'],
            'behavioral_segmentation' => ['nullable', 'boolean'],
            'predictive_modeling' => ['nullable', 'boolean'],

            // Integration Capabilities
            'third_party_integrations' => ['nullable', 'array'],
            'webhook_endpoints' => ['nullable', 'array'],
            'webhook_endpoints.*' => ['url', 'max:500'],
            'api_gateway_integration' => ['nullable', 'boolean'],
            'microservices_communication' => ['nullable', 'boolean'],
            'event_driven_architecture' => ['nullable', 'boolean'],

            // Future-ready Features
            'edge_computing' => ['nullable', 'boolean'],
            'serverless_functions' => ['nullable', 'boolean'],
            'blockchain_integration' => ['nullable', 'boolean'],
            'quantum_ready_encryption' => ['nullable', 'boolean'],
            'iot_device_support' => ['nullable', 'boolean'],
            'voice_interface' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // WebSocket Messages
            'connection_timeout.min' => __('validation.realtime.connection_timeout_too_short'),
            'max_concurrent_connections.max' => __('validation.realtime.too_many_connections'),
            'channel_name.regex' => __('validation.realtime.invalid_channel_name'),
            'channel_capacity.max' => __('validation.realtime.channel_capacity_exceeded'),

            // Notification Messages
            'recipient_ids.max' => __('validation.realtime.too_many_recipients'),
            'notification_message.max' => __('validation.realtime.message_too_long'),
            'delivery_rate_limit.max' => __('validation.realtime.delivery_rate_too_high'),

            // Messaging Messages
            'message_content.max' => __('validation.realtime.message_content_too_long'),
            'participants.max' => __('validation.realtime.too_many_participants'),
            'file_attachments.max' => __('validation.realtime.too_many_attachments'),

            // Performance Messages
            'batch_size.max' => __('validation.realtime.batch_size_too_large'),
            'max_bandwidth_mbps.max' => __('validation.realtime.bandwidth_limit_exceeded'),
            'socket_buffer_size.max' => __('validation.realtime.buffer_size_too_large'),
        ];
    }

    protected function passedValidation(): void
    {
        $this->validateRealTimeConfiguration();
        $this->optimizeRealTimePerformance();
        $this->logRealTimeActivity();
    }

    private function validateRealTimeConfiguration(): void
    {
        // Validate connection limits
        if ($this->has(['max_concurrent_connections', 'channel_capacity'])) {
            $totalCapacity = $this->max_concurrent_connections * ($this->channel_capacity ?? 1);
            if ($totalCapacity > 1000000) {
                throw new \InvalidArgumentException(__('validation.realtime.total_capacity_exceeded'));
            }
        }

        // Validate message size limits
        if ($this->has('file_attachments')) {
            $totalSize = 0;
            foreach ($this->file_attachments as $file) {
                $totalSize += $file['size'] ?? 0;
            }

            if ($totalSize > 104857600) { // 100MB total
                throw new \InvalidArgumentException(__('validation.realtime.total_file_size_exceeded'));
            }
        }

        // Validate rate limiting configuration
        if ($this->has(['delivery_rate_limit', 'recipient_ids'])) {
            $recipientCount = count($this->recipient_ids);
            $rateLimit = $this->delivery_rate_limit;

            if ($recipientCount > $rateLimit) {
                throw new \InvalidArgumentException(__('validation.realtime.recipients_exceed_rate_limit'));
            }
        }
    }

    private function optimizeRealTimePerformance(): void
    {
        // Optimize connection settings based on load
        if ($this->has('max_concurrent_connections')) {
            $connections = $this->max_concurrent_connections;
            $optimizedSettings = $this->calculateOptimalSettings($connections);

            $this->merge([
                'optimized_heartbeat_interval' => $optimizedSettings['heartbeat'],
                'optimized_batch_size' => $optimizedSettings['batch_size'],
                'recommended_cache_ttl' => $optimizedSettings['cache_ttl'],
            ]);
        }

        // Cache real-time configuration
        if ($this->has('connection_id')) {
            Cache::remember("realtime_config_{$this->connection_id}", 3600, function () {
                return $this->validated();
            });
        }
    }

    private function calculateOptimalSettings(int $connections): array
    {
        return [
            'heartbeat' => min(30, max(10, $connections / 100)),
            'batch_size' => min(1000, max(10, $connections / 10)),
            'cache_ttl' => min(3600, max(300, $connections / 5)),
        ];
    }

    private function logRealTimeActivity(): void
    {
        \Log::info('Real-time Operations Request', [
            'operation_type' => $this->getOperationType(),
            'connection_id' => $this->connection_id ?? 'new',
            'channel_name' => $this->channel_name ?? 'default',
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'timestamp' => now(),
            'performance_optimized' => $this->has('optimized_heartbeat_interval'),
        ]);
    }

    private function getOperationType(): string
    {
        if ($this->has('notification_type')) {
            return 'notification_delivery';
        }
        if ($this->has('message_type')) {
            return 'real_time_messaging';
        }
        if ($this->has('stream_type')) {
            return 'data_streaming';
        }
        if ($this->has('collaboration_type')) {
            return 'live_collaboration';
        }
        if ($this->has('connection_id')) {
            return 'websocket_management';
        }

        return 'general_realtime_operation';
    }
}
