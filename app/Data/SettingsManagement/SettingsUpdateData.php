<?php

namespace App\Data\SettingsManagement;

use LumoSolutions\Actionable\Attributes\ArrayOf;
use LumoSolutions\Actionable\Attributes\DateFormat;
use LumoSolutions\Actionable\Attributes\FieldName;
use LumoSolutions\Actionable\Attributes\Ignore;
use LumoSolutions\Actionable\Traits\ArrayConvertible;

/**
 * Settings Update Data Transfer Object
 *
 * Specialized DTO for tracking settings updates with change history,
 * validation, and rollback capabilities.
 */
class SettingsUpdateData
{
    use ArrayConvertible;

    public function __construct(
        #[FieldName('model_type')]
        public string $modelType,
        #[FieldName('model_id')]
        public int|string $modelId,
        #[ArrayOf('mixed')]
        #[FieldName('new_settings')]
        public array $newSettings,
        #[ArrayOf('mixed')]
        #[FieldName('previous_settings')]
        public ?array $previousSettings = null,
        #[ArrayOf('string')]
        #[FieldName('changed_keys')]
        public ?array $changedKeys = null,
        #[FieldName('update_strategy')]
        public string $updateStrategy = 'merge', // merge, replace, append, remove

        #[FieldName('user_id')]
        public ?int $userId = null,
        #[DateFormat('Y-m-d H:i:s')]
        #[FieldName('updated_at')]
        public ?\DateTime $updatedAt = null,
        #[FieldName('update_reason')]
        public ?string $updateReason = null,
        #[FieldName('source')]
        public string $source = 'api', // api, admin, system, migration

        #[FieldName('validation_enabled')]
        public bool $validationEnabled = true,
        #[FieldName('backup_enabled')]
        public bool $backupEnabled = true,
        #[ArrayOf('string')]
        #[FieldName('affected_features')]
        public ?array $affectedFeatures = null,
        #[ArrayOf('mixed')]
        #[FieldName('validation_context')]
        public ?array $validationContext = null,
        #[Ignore]
        public ?array $changeAnalysis = null, // Internal change analysis

        #[Ignore]
        public ?array $rollbackData = null, // For rollback operations
    ) {
        $this->updatedAt = $this->updatedAt ?? new \DateTime;
        $this->analyzeChanges();
    }

    /**
     * Create from existing settings and new values
     */
    public static function fromChanges(
        string $modelType,
        int|string $modelId,
        array $currentSettings,
        array $newSettings,
        ?int $userId = null,
        ?string $updateReason = null
    ): self {
        $instance = new self(
            modelType: $modelType,
            modelId: $modelId,
            newSettings: $newSettings,
            previousSettings: $currentSettings,
            userId: $userId,
            updateReason: $updateReason
        );

        $instance->detectChanges();

        return $instance;
    }

    /**
     * Create for partial update
     */
    public static function forPartialUpdate(
        string $modelType,
        int|string $modelId,
        array $partialSettings,
        array $settingsKeys,
        ?int $userId = null
    ): self {
        return new self(
            modelType: $modelType,
            modelId: $modelId,
            newSettings: $partialSettings,
            changedKeys: $settingsKeys,
            updateStrategy: 'merge',
            userId: $userId,
            source: 'partial_update'
        );
    }

    /**
     * Create for bulk settings update
     */
    public static function forBulkUpdate(
        string $modelType,
        array $modelIds,
        array $newSettings,
        ?int $userId = null
    ): array {
        return array_map(
            fn ($id) => new self(
                modelType: $modelType,
                modelId: $id,
                newSettings: $newSettings,
                updateStrategy: 'merge',
                userId: $userId,
                source: 'bulk_update'
            ),
            $modelIds
        );
    }

    /**
     * Detect changes between previous and new settings
     */
    public function detectChanges(): void
    {
        if (! $this->previousSettings) {
            $this->changedKeys = array_keys($this->flattenArray($this->newSettings));

            return;
        }

        $previousFlat = $this->flattenArray($this->previousSettings);
        $newFlat = $this->flattenArray($this->newSettings);

        $this->changedKeys = [];

        // Find changed and new keys
        foreach ($newFlat as $key => $value) {
            if (! array_key_exists($key, $previousFlat) || $previousFlat[$key] !== $value) {
                $this->changedKeys[] = $key;
            }
        }

        // Find removed keys (when using replace strategy)
        if ($this->updateStrategy === 'replace') {
            foreach ($previousFlat as $key => $value) {
                if (! array_key_exists($key, $newFlat)) {
                    $this->changedKeys[] = $key;
                }
            }
        }

        $this->changedKeys = array_unique($this->changedKeys);
    }

    /**
     * Analyze the impact of changes
     */
    public function analyzeChanges(): void
    {
        if (! $this->changedKeys) {
            return;
        }

        $this->changeAnalysis = [
            'total_changes' => count($this->changedKeys),
            'change_categories' => $this->categorizeChanges(),
            'impact_level' => $this->assessImpactLevel(),
            'requires_restart' => $this->requiresApplicationRestart(),
            'affects_cache' => $this->affectsCache(),
            'security_sensitive' => $this->isSecuritySensitive(),
        ];
    }

    /**
     * Get changes by category
     */
    public function categorizeChanges(): array
    {
        $categories = [
            'display' => [],
            'privacy' => [],
            'notifications' => [],
            'workflow' => [],
            'analytics' => [],
            'other' => [],
        ];

        foreach ($this->changedKeys as $key) {
            $category = $this->determineSettingCategory($key);
            $categories[$category][] = $key;
        }

        return array_filter($categories); // Remove empty categories
    }

    /**
     * Assess the impact level of changes
     */
    public function assessImpactLevel(): string
    {
        if (! $this->changedKeys) {
            return 'none';
        }

        $highImpactPatterns = [
            'workflow.',
            'security.',
            'privacy.public_visibility',
            'notifications.system_alerts',
        ];

        foreach ($this->changedKeys as $key) {
            foreach ($highImpactPatterns as $pattern) {
                if (str_starts_with($key, $pattern)) {
                    return 'high';
                }
            }
        }

        return count($this->changedKeys) > 10 ? 'medium' : 'low';
    }

    /**
     * Check if changes require application restart
     */
    public function requiresApplicationRestart(): bool
    {
        $restartPatterns = [
            'system.',
            'cache.driver',
            'database.',
            'queue.driver',
        ];

        foreach ($this->changedKeys as $key) {
            foreach ($restartPatterns as $pattern) {
                if (str_starts_with($key, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if changes affect cache
     */
    public function affectsCache(): bool
    {
        $cachePatterns = [
            'display.',
            'analytics.',
            'matching.',
            'workflow.auto_',
        ];

        foreach ($this->changedKeys as $key) {
            foreach ($cachePatterns as $pattern) {
                if (str_starts_with($key, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if changes are security sensitive
     */
    public function isSecuritySensitive(): bool
    {
        $securityPatterns = [
            'privacy.',
            'security.',
            'access.',
            'permissions.',
            'verification.',
        ];

        foreach ($this->changedKeys as $key) {
            foreach ($securityPatterns as $pattern) {
                if (str_starts_with($key, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get rollback data for these changes
     */
    public function getRollbackData(): array
    {
        if (! $this->previousSettings) {
            return [];
        }

        $this->rollbackData = [
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'rollback_settings' => $this->previousSettings,
            'rollback_keys' => $this->changedKeys,
            'original_update_time' => $this->updatedAt,
            'rollback_reason' => 'Manual rollback requested',
            'user_id' => $this->userId,
        ];

        return $this->rollbackData;
    }

    /**
     * Create a summary of changes for logging
     */
    public function getChangeSummary(): string
    {
        if (! $this->changedKeys) {
            return 'No changes detected';
        }

        $summary = sprintf(
            'Updated %d setting(s) for %s #%s',
            count($this->changedKeys),
            class_basename($this->modelType),
            $this->modelId
        );

        if ($this->updateReason) {
            $summary .= " - Reason: {$this->updateReason}";
        }

        if ($this->changeAnalysis) {
            $summary .= sprintf(
                ' - Impact: %s, Categories: %s',
                $this->changeAnalysis['impact_level'],
                implode(', ', array_keys($this->changeAnalysis['change_categories']))
            );
        }

        return $summary;
    }

    /**
     * Determine setting category from key
     */
    private function determineSettingCategory(string $key): string
    {
        $categoryMapping = [
            'display' => ['display.', 'show_', 'hide_', 'format'],
            'privacy' => ['privacy.', 'visibility', 'anonymous', 'public'],
            'notifications' => ['notifications.', 'alerts', 'email', 'sms'],
            'workflow' => ['workflow.', 'auto_', 'approval', 'process'],
            'analytics' => ['analytics.', 'tracking', 'metrics', 'score'],
        ];

        foreach ($categoryMapping as $category => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($key, $pattern)) {
                    return $category;
                }
            }
        }

        return 'other';
    }

    /**
     * Helper method to flatten array with dot notation
     */
    private function flattenArray(array $array, string $prefix = ''): array
    {
        $flattened = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $flattened = array_merge($flattened, $this->flattenArray($value, $newKey));
            } else {
                $flattened[$newKey] = $value;
            }
        }

        return $flattened;
    }

    /**
     * Add version information to the update data
     */
    public function withVersion(array $versionInfo): self
    {
        $data = $this->toArray();
        $data['version'] = $versionInfo;

        return new self(
            $data['model_type'],
            $data['model_id'],
            $data['previous_settings'],
            $data['new_settings'],
            $data['changed_keys'],
            $data['change_count'],
            $data['change_types'],
            $data['impact_analysis'],
            $data['strategy'],
            $data['user_id'],
            $data['audit_reason'],
            $data['performance'],
            $data['cache_info'],
            $data['metadata'],
            $versionInfo
        );
    }
}
