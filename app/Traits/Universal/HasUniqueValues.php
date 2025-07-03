<?php

namespace App\Traits\Universal;

use App\Services\Universal\UniversalUniqueValueService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

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
        $subjectId = $this->getSubjectId();

        try {
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
        } catch (\JustBetter\UniqueValues\Exceptions\MaxAttemptsException $e) {
            // In test environments or when unique generation fails, use fallback
            return $this->generateFallbackUniqueValue($type, $field, $config);
        }
    }

    /**
     * Generate fallback unique value when service fails.
     */
    protected function generateFallbackUniqueValue(string $type, string $field, array $config): string
    {
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);

        return match ($type) {
            'job-reference' => "JOB-{$timestamp}-{$random}",
            'application-code' => "APP-{$timestamp}-{$random}",
            'candidate-code' => "CAN-{$random}",
            'company-code' => "COM-{$timestamp}-{$random}",
            'invoice-number' => "INV-{$timestamp}-{$random}",
            'order-reference' => "ORD-{$timestamp}-{$random}",
            'api-key' => Str::random(32),
            'slug' => Str::slug($this->{$config['source_field'] ?? 'name'}).'-'.$random,
            'custom' => $config['scope'].'-'.$timestamp.'-'.$random,
            default => $field.'-'.$timestamp.'-'.$random,
        };
    }

    /**
     * Get subject ID for unique value generation.
     */
    protected function getSubjectId(): string
    {
        // Use model key if available, otherwise generate a temporary unique identifier
        $key = $this->getKey();
        if ($key) {
            return (string) $key;
        }

        // For new models without keys, use a combination of class and timestamp
        return class_basename($this).'-'.now()->format('YmdHis').'-'.Str::random(6);
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
