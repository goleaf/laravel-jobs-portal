<?php

namespace App\Traits\Universal;

use App\Services\Universal\UniversalUniqueValueService;
use Illuminate\Support\Facades\App;

/**
 * HasUniqueValues Trait
 * 
 * Provides automatic unique value generation for models
 * using the Universal Unique Value Service.
 */
trait HasUniqueValues
{
    /**
     * Boot the trait.
     */
    protected static function bootHasUniqueValues(): void
    {
        static::creating(function ($model) {
            $model->generateUniqueValues();
        });
    }

    /**
     * Generate unique values for the model.
     */
    public function generateUniqueValues(): void
    {
        $service = App::make(UniversalUniqueValueService::class);
        
        foreach ($this->getUniqueValueFields() as $field => $config) {
            if (empty($this->{$field})) {
                $this->{$field} = $this->generateUniqueValue($service, $field, $config);
            }
        }
    }

    /**
     * Generate a specific unique value.
     */
    protected function generateUniqueValue(UniversalUniqueValueService $service, string $field, array $config): string
    {
        $type = $config['type'] ?? 'custom';
        $subjectId = $this->getKey();

        return match ($type) {
            'job-reference' => $service->generateJobReference($subjectId),
            'application-code' => $service->generateApplicationCode($subjectId),
            'candidate-code' => $service->generateCandidateCode($subjectId),
            'company-code' => $service->generateCompanyCode($subjectId),
            'invoice-number' => $service->generateInvoiceNumber($subjectId),
            'order-reference' => $service->generateOrderReference($subjectId),
            'api-key' => $service->generateApiKey($subjectId),
            'slug' => $service->generateUniqueSlug(
                $this->{$config['source_field'] ?? 'name'},
                $config['scope'] ?? 'general-slug',
                $subjectId
            ),
            'custom' => $service->generateCustomUnique(
                $config['scope'],
                $config['generator'],
                $subjectId,
                $config['attempts'] ?? 3
            ),
            default => throw new \Exception("Unknown unique value type: {$type}"),
        };
    }

    /**
     * Regenerate unique values (useful for updates or overrides).
     */
    public function regenerateUniqueValues(array $fields = []): void
    {
        $service = App::make(UniversalUniqueValueService::class);
        $fieldsToRegenerate = empty($fields) ? array_keys($this->getUniqueValueFields()) : $fields;
        
        foreach ($fieldsToRegenerate as $field) {
            $config = $this->getUniqueValueFields()[$field] ?? [];
            $this->{$field} = $this->generateUniqueValue($service, $field, $config);
        }
    }

    /**
     * Get unique value configuration for the model.
     * Override this method in your model to define unique value fields.
     */
    protected function getUniqueValueFields(): array
    {
        return [];
    }

    /**
     * Check if a field has unique value generation configured.
     */
    public function hasUniqueValueField(string $field): bool
    {
        return isset($this->getUniqueValueFields()[$field]);
    }

    /**
     * Get the unique value type for a field.
     */
    public function getUniqueValueType(string $field): ?string
    {
        return $this->getUniqueValueFields()[$field]['type'] ?? null;
    }
} 