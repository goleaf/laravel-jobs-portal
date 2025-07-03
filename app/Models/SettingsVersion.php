<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Settings Version Model
 *
 * Tracks all changes to model settings with complete audit trails,
 * enabling rollback, comparison, and compliance reporting.
 */
class SettingsVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'version_id',
        'model_type',
        'model_id',
        'version_number',
        'change_type',
        'change_reason',
        'change_summary',
        'settings_data',
        'previous_settings',
        'changed_keys',
        'user_id',
        'source',
        'user_agent',
        'ip_address',
        'is_active',
        'is_validated',
        'validation_errors',
        'size_bytes',
        'expires_at',
        'checksum',
    ];

    protected $casts = [
        'settings_data' => 'array',
        'previous_settings' => 'array',
        'changed_keys' => 'array',
        'change_summary' => 'array',
        'validation_errors' => 'array',
        'is_active' => 'boolean',
        'is_validated' => 'boolean',
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public $timestamps = false; // We only use created_at

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->version_id)) {
                $model->version_id = (string) Str::uuid();
            }

            if (empty($model->created_at)) {
                $model->created_at = now();
            }

            // Calculate size and checksum
            $model->size_bytes = strlen(json_encode($model->settings_data));
            $model->checksum = hash('sha256', json_encode($model->settings_data));
        });
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForModel($query, string $modelType, string|int $modelId)
    {
        return $query->where('model_type', $modelType)
            ->where('model_id', $modelId);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByChangeType($query, string $changeType)
    {
        return $query->where('change_type', $changeType);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeLatestVersion($query)
    {
        return $query->orderBy('version_number', 'desc')->limit(1);
    }

    public function scopeOrderByVersion($query, string $direction = 'desc')
    {
        return $query->orderBy('version_number', $direction);
    }

    /**
     * Helper Methods
     */
    public function getModelClassName(): string
    {
        return class_basename($this->model_type);
    }

    public function getPreviousVersion(): ?self
    {
        return self::forModel($this->model_type, $this->model_id)
            ->where('version_number', '<', $this->version_number)
            ->orderBy('version_number', 'desc')
            ->first();
    }

    public function getNextVersion(): ?self
    {
        return self::forModel($this->model_type, $this->model_id)
            ->where('version_number', '>', $this->version_number)
            ->orderBy('version_number', 'asc')
            ->first();
    }

    public function isLatestVersion(): bool
    {
        $latestVersion = self::forModel($this->model_type, $this->model_id)
            ->orderBy('version_number', 'desc')
            ->first();

        return $latestVersion && $latestVersion->id === $this->id;
    }

    public function getChangedKeysCount(): int
    {
        return count($this->changed_keys ?? []);
    }

    public function getSizeInKB(): float
    {
        return round(($this->size_bytes ?? 0) / 1024, 2);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function verifyChecksum(): bool
    {
        $currentChecksum = hash('sha256', json_encode($this->settings_data));

        return $currentChecksum === $this->checksum;
    }

    /**
     * Generate change summary
     */
    public function generateChangeSummary(): array
    {
        if (! $this->previous_settings || ! $this->settings_data) {
            return [
                'type' => 'initial',
                'message' => 'Initial settings version',
                'changes_count' => 0,
            ];
        }

        $summary = [
            'type' => 'update',
            'changes_count' => count($this->changed_keys ?? []),
            'categories' => $this->categorizeChanges(),
            'impact_level' => $this->assessImpactLevel(),
        ];

        return $summary;
    }

    /**
     * Categorize changes by type
     */
    private function categorizeChanges(): array
    {
        $categories = [
            'profile' => [],
            'preferences' => [],
            'privacy' => [],
            'notifications' => [],
            'workflow' => [],
            'other' => [],
        ];

        foreach ($this->changed_keys ?? [] as $key) {
            $category = $this->determineKeyCategory($key);
            $categories[$category][] = $key;
        }

        return array_filter($categories);
    }

    /**
     * Determine which category a settings key belongs to
     */
    private function determineKeyCategory(string $key): string
    {
        if (str_contains($key, 'profile')) {
            return 'profile';
        }
        if (str_contains($key, 'preference')) {
            return 'preferences';
        }
        if (str_contains($key, 'privacy')) {
            return 'privacy';
        }
        if (str_contains($key, 'notification')) {
            return 'notifications';
        }
        if (str_contains($key, 'workflow')) {
            return 'workflow';
        }

        return 'other';
    }

    /**
     * Assess the impact level of changes
     */
    private function assessImpactLevel(): string
    {
        $changeCount = count($this->changed_keys ?? []);

        if ($changeCount === 0) {
            return 'none';
        }
        if ($changeCount <= 2) {
            return 'low';
        }
        if ($changeCount <= 5) {
            return 'medium';
        }

        return 'high';
    }

    /**
     * Create a rollback version
     */
    public function createRollback(?int $userId = null, ?string $reason = null): self
    {
        $rollbackVersion = new self([
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'version_number' => self::getNextVersionNumber($this->model_type, $this->model_id),
            'change_type' => 'rollback',
            'change_reason' => $reason ?? "Rollback to version {$this->version_number}",
            'settings_data' => $this->settings_data,
            'user_id' => $userId,
            'source' => 'rollback',
        ]);

        $rollbackVersion->save();

        return $rollbackVersion;
    }

    /**
     * Get the next version number for a model
     */
    public static function getNextVersionNumber(string $modelType, string|int $modelId): int
    {
        $latestVersion = self::forModel($modelType, $modelId)
            ->orderBy('version_number', 'desc')
            ->first();

        return $latestVersion ? $latestVersion->version_number + 1 : 1;
    }

    /**
     * Clean up expired versions
     */
    public static function cleanupExpiredVersions(): int
    {
        return self::where('expires_at', '<', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
