<?php

namespace App\Data\SettingsManagement;

use LumoSolutions\Actionable\Traits\ArrayConvertible;
use LumoSolutions\Actionable\Attributes\ArrayOf;
use LumoSolutions\Actionable\Attributes\DateFormat;
use LumoSolutions\Actionable\Attributes\FieldName;
use LumoSolutions\Actionable\Attributes\Ignore;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Settings Data Transfer Object
 * 
 * Handles settings operations for any Laravel model using Laravel Model Settings package
 * with Actionable smart attributes for API-friendly transformations.
 */
readonly class ModelSettingsData
{
    use ArrayConvertible;

    public function __construct(
        #[FieldName('model_type')]
        public string $modelType,
        
        #[FieldName('model_id')]
        public int|string $modelId,
        
        #[ArrayOf('mixed')]
        public array $settings,
        
        #[FieldName('operation_type')]
        public string $operationType = 'update', // update, get, delete, validate
        
        #[ArrayOf('string')]
        #[FieldName('settings_keys')]
        public ?array $settingsKeys = null, // For partial operations
        
        #[FieldName('validation_rules')]
        #[ArrayOf('string')]
        public ?array $validationRules = null,
        
        #[FieldName('default_values')]
        #[ArrayOf('mixed')]
        public ?array $defaultValues = null,
        
        #[FieldName('merge_strategy')]
        public string $mergeStrategy = 'merge', // merge, replace, append
        
        #[FieldName('cache_duration')]
        public ?int $cacheDuration = 3600, // Cache duration in seconds
        
        #[FieldName('user_id')]
        public ?int $userId = null, // For audit trails
        
        #[DateFormat('Y-m-d H:i:s')]
        #[FieldName('timestamp')]
        public ?\DateTime $timestamp = null,
        
        #[ArrayOf('mixed')]
        public ?array $metadata = null, // Additional context
        
        #[Ignore]
        public ?Model $modelInstance = null, // Internal use only
        
        #[Ignore]
        public ?array $internalFlags = null, // Internal processing flags
    ) {}

    /**
     * Create from model instance
     */
    public static function fromModel(
        Model $model, 
        array $settings = [], 
        string $operationType = 'update',
        ?int $userId = null
    ): self {
        return new self(
            modelType: get_class($model),
            modelId: $model->getKey(),
            settings: $settings,
            operationType: $operationType,
            userId: $userId,
            modelInstance: $model,
            timestamp: new \DateTime()
        );
    }

    /**
     * Create for bulk operations
     */
    public static function forBulkOperation(
        string $modelType,
        array $modelIds,
        array $settings,
        ?int $userId = null
    ): array {
        return array_map(
            fn($id) => new self(
                modelType: $modelType,
                modelId: $id,
                settings: $settings,
                operationType: 'bulk_update',
                userId: $userId,
                timestamp: new \DateTime()
            ),
            $modelIds
        );
    }

    /**
     * Create for settings retrieval
     */
    public static function forRetrieval(
        string $modelType,
        int|string $modelId,
        ?array $settingsKeys = null,
        ?int $cacheDuration = 3600
    ): self {
        return new self(
            modelType: $modelType,
            modelId: $modelId,
            settings: [],
            operationType: 'get',
            settingsKeys: $settingsKeys,
            cacheDuration: $cacheDuration,
            timestamp: new \DateTime()
        );
    }

    /**
     * Create for settings validation
     */
    public static function forValidation(
        string $modelType,
        array $settings,
        ?array $validationRules = null
    ): self {
        return new self(
            modelType: $modelType,
            modelId: 'validation',
            settings: $settings,
            operationType: 'validate',
            validationRules: $validationRules,
            timestamp: new \DateTime()
        );
    }

    /**
     * Get model class name without namespace
     */
    public function getModelClassName(): string
    {
        return class_basename($this->modelType);
    }

    /**
     * Check if operation is read-only
     */
    public function isReadOnlyOperation(): bool
    {
        return in_array($this->operationType, ['get', 'validate']);
    }

    /**
     * Check if operation requires model instance
     */
    public function requiresModelInstance(): bool
    {
        return !in_array($this->operationType, ['validate']);
    }

    /**
     * Get cache key for this settings operation
     */
    public function getCacheKey(): string
    {
        $keyParts = [
            'model_settings',
            $this->getModelClassName(),
            $this->modelId,
            $this->operationType
        ];

        if ($this->settingsKeys) {
            $keyParts[] = md5(implode(',', $this->settingsKeys));
        }

        return implode(':', $keyParts);
    }

    /**
     * Get flattened settings for dot notation access
     */
    public function getFlattenedSettings(): array
    {
        return $this->flattenArray($this->settings);
    }

    /**
     * Set nested settings value using dot notation
     */
    public function setNestedSetting(string $key, mixed $value): array
    {
        return $this->setNestedArrayValue($this->settings, $key, $value);
    }

    /**
     * Get nested settings value using dot notation
     */
    public function getNestedSetting(string $key, mixed $default = null): mixed
    {
        return $this->getNestedArrayValue($this->settings, $key, $default);
    }

    /**
     * Validate settings structure
     */
    public function validateSettingsStructure(): array
    {
        $errors = [];

        if (empty($this->settings) && !$this->isReadOnlyOperation()) {
            $errors[] = 'Settings array cannot be empty for write operations';
        }

        if ($this->requiresModelInstance() && empty($this->modelId)) {
            $errors[] = 'Model ID is required for this operation';
        }

        if (empty($this->modelType)) {
            $errors[] = 'Model type is required';
        }

        if (!class_exists($this->modelType)) {
            $errors[] = "Model class {$this->modelType} does not exist";
        }

        return $errors;
    }

    /**
     * Helper method to flatten array with dot notation
     */
    private function flattenArray(array $array, string $prefix = ''): array
    {
        $flattened = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $flattened = array_merge($flattened, $this->flattenArray($value, $newKey));
            } else {
                $flattened[$newKey] = $value;
            }
        }

        return $flattened;
    }

    /**
     * Helper method to set nested array value
     */
    private function setNestedArrayValue(array $array, string $key, mixed $value): array
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $keyPart) {
            if (!isset($current[$keyPart]) || !is_array($current[$keyPart])) {
                $current[$keyPart] = [];
            }
            $current = &$current[$keyPart];
        }

        $current = $value;
        return $array;
    }

    /**
     * Helper method to get nested array value
     */
    private function getNestedArrayValue(array $array, string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $current = $array;

        foreach ($keys as $keyPart) {
            if (!is_array($current) || !array_key_exists($keyPart, $current)) {
                return $default;
            }
            $current = $current[$keyPart];
        }

        return $current;
    }
} 