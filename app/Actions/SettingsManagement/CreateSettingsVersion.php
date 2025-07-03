<?php

namespace App\Actions\SettingsManagement;

use App\Models\SettingsVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

/**
 * Create Settings Version Action
 *
 * Creates comprehensive version history for settings changes with
 * audit trails, change analysis, and rollback capabilities.
 */
class CreateSettingsVersion
{
    use IsDispatchable;
    use IsRunnable;

    /**
     * Create a new settings version
     */
    public function handle(
        string $modelType,
        string|int $modelId,
        array $newSettings,
        ?array $previousSettings = null,
        string $changeType = 'update',
        ?int $userId = null,
        ?string $changeReason = null,
        string $source = 'api'
    ): SettingsVersion {
        try {
            // Get the next version number
            $versionNumber = SettingsVersion::getNextVersionNumber($modelType, $modelId);

            // Detect changed keys
            $changedKeys = $this->detectChangedKeys($previousSettings ?? [], $newSettings);

            // Create the version record
            $version = SettingsVersion::create([
                'model_type' => $modelType,
                'model_id' => $modelId,
                'version_number' => $versionNumber,
                'change_type' => $changeType,
                'change_reason' => $changeReason,
                'settings_data' => $newSettings,
                'previous_settings' => $previousSettings,
                'changed_keys' => $changedKeys,
                'user_id' => $userId,
                'source' => $source,
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'is_active' => true,
                'is_validated' => true,
            ]);

            // Generate and store change summary
            $changeSummary = $version->generateChangeSummary();
            $version->update(['change_summary' => $changeSummary]);

            // Log the version creation
            Log::info('Settings version created', [
                'version_id' => $version->version_id,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'version_number' => $versionNumber,
                'changed_keys_count' => count($changedKeys),
                'user_id' => $userId,
                'source' => $source,
            ]);

            // Trigger events
            event('settings.version.created', compact('version', 'modelType', 'modelId'));

            return $version;

        } catch (\Exception $e) {
            Log::error('Failed to create settings version', [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Create version from model instance
     */
    public static function fromModel(
        Model $model,
        array $newSettings,
        ?array $previousSettings = null,
        string $changeType = 'update',
        ?int $userId = null,
        ?string $changeReason = null
    ): SettingsVersion {
        return self::run(
            modelType: get_class($model),
            modelId: $model->getKey(),
            newSettings: $newSettings,
            previousSettings: $previousSettings,
            changeType: $changeType,
            userId: $userId,
            changeReason: $changeReason
        );
    }

    /**
     * Create rollback version
     */
    public static function createRollback(
        SettingsVersion $targetVersion,
        ?int $userId = null,
        ?string $reason = null
    ): SettingsVersion {
        return self::run(
            modelType: $targetVersion->model_type,
            modelId: $targetVersion->model_id,
            newSettings: $targetVersion->settings_data,
            previousSettings: null, // Will be filled from current settings
            changeType: 'rollback',
            userId: $userId,
            changeReason: $reason ?? "Rollback to version {$targetVersion->version_number}",
            source: 'rollback'
        );
    }

    /**
     * Detect which keys have changed between two settings arrays
     */
    private function detectChangedKeys(array $previous, array $new): array
    {
        $previousFlat = $this->flattenArray($previous);
        $newFlat = $this->flattenArray($new);

        $changedKeys = [];

        // Find changed and new keys
        foreach ($newFlat as $key => $value) {
            if (! array_key_exists($key, $previousFlat) || $previousFlat[$key] !== $value) {
                $changedKeys[] = $key;
            }
        }

        // Find removed keys
        foreach ($previousFlat as $key => $value) {
            if (! array_key_exists($key, $newFlat)) {
                $changedKeys[] = $key;
            }
        }

        return array_unique($changedKeys);
    }

    /**
     * Flatten array with dot notation
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
     * Get version history for a model
     */
    public static function getVersionHistory(
        string $modelType,
        string|int $modelId,
        int $limit = 10
    ): \Illuminate\Database\Eloquent\Collection {
        return SettingsVersion::forModel($modelType, $modelId)
            ->active()
            ->orderByVersion('desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Compare two versions
     */
    public static function compareVersions(
        SettingsVersion $version1,
        SettingsVersion $version2
    ): array {
        if ($version1->model_type !== $version2->model_type ||
            $version1->model_id !== $version2->model_id) {
            throw new \InvalidArgumentException('Cannot compare versions from different models');
        }

        $changes = [];
        $keys1 = array_keys($version1->settings_data ?? []);
        $keys2 = array_keys($version2->settings_data ?? []);
        $allKeys = array_unique(array_merge($keys1, $keys2));

        foreach ($allKeys as $key) {
            $value1 = data_get($version1->settings_data, $key);
            $value2 = data_get($version2->settings_data, $key);

            if ($value1 !== $value2) {
                $changes[$key] = [
                    'from' => $value1,
                    'to' => $value2,
                    'type' => $this->getChangeType($value1, $value2),
                ];
            }
        }

        return [
            'version_from' => $version1->version_number,
            'version_to' => $version2->version_number,
            'changes' => $changes,
            'changes_count' => count($changes),
            'created_at_from' => $version1->created_at,
            'created_at_to' => $version2->created_at,
        ];
    }

    /**
     * Determine the type of change
     */
    private function getChangeType($oldValue, $newValue): string
    {
        if ($oldValue === null && $newValue !== null) {
            return 'added';
        }
        if ($oldValue !== null && $newValue === null) {
            return 'removed';
        }
        if ($oldValue !== $newValue) {
            return 'modified';
        }

        return 'unchanged';
    }

    /**
     * Cleanup old versions based on retention policy
     */
    public static function cleanupOldVersions(
        ?string $modelType = null,
        int $keepLastVersions = 10,
        int $keepDays = 365
    ): int {
        $query = SettingsVersion::where('created_at', '<', now()->subDays($keepDays));

        if ($modelType) {
            $query->where('model_type', $modelType);
        }

        // Don't delete the most recent versions
        if ($keepLastVersions > 0) {
            $query->whereNotIn('id', function ($subQuery) use ($modelType, $keepLastVersions) {
                $subQuery->select('id')
                    ->from('settings_versions')
                    ->when($modelType, fn ($q) => $q->where('model_type', $modelType))
                    ->orderBy('created_at', 'desc')
                    ->limit($keepLastVersions);
            });
        }

        $deletedCount = $query->count();
        $query->update(['is_active' => false]);

        Log::info('Settings versions cleanup completed', [
            'deleted_count' => $deletedCount,
            'model_type' => $modelType,
            'retention_days' => $keepDays,
            'keep_last_versions' => $keepLastVersions,
        ]);

        return $deletedCount;
    }
}
