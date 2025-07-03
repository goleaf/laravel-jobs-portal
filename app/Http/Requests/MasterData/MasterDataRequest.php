<?php

namespace App\Http\Requests\MasterData;

use App\Http\Requests\Foundation\AbstractBaseRequest;

/**
 * Master Data Request - Base class for master data validation
 * 
 * Handles validation for:
 * - Location data (countries, states, cities)
 * - Company classifications (size, industry, ownership)
 * - Job classifications (types, career levels, salary ranges)
 * - Skills and tags
 * - Reference data management
 * 
 * @package App\Http\Requests\MasterData
 * @version 1.0.0
 * @since 2024-12-28
 */
abstract class MasterDataRequest extends AbstractBaseRequest
{
    /**
     * Security level for master data operations
     */
    protected string $securityLevel = 'low';

    /**
     * Get domain-specific validation rules for master data
     */
    protected function getDomainRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'description' => ['sometimes', 'string', 'max:1000'],
        ];
    }

    /**
     * Get domain-specific error messages for master data
     */
    protected function getDomainMessages(): array
    {
        return [
            'name.required' => __('validation.master_data.name_required'),
            'name.string' => __('validation.master_data.name_string'),
            'name.max' => __('validation.master_data.name_max'),
            'status.in' => __('validation.master_data.status_invalid'),
            'sort_order.integer' => __('validation.master_data.sort_order_integer'),
            'sort_order.min' => __('validation.master_data.sort_order_min'),
            'description.max' => __('validation.master_data.description_max'),
        ];
    }

    /**
     * Get domain-specific attribute names for master data
     */
    protected function getDomainAttributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'status' => __('validation.attributes.status'),
            'sort_order' => __('validation.attributes.sort_order'),
            'description' => __('validation.attributes.description'),
        ];
    }

    /**
     * Common validation rules for location-based master data
     */
    protected function getLocationRules(): array
    {
        return [
            'country_id' => ['sometimes', 'required', 'exists:countries,id'],
            'state_id' => ['sometimes', 'required', 'exists:states,id'],
            'city_id' => ['sometimes', 'required', 'exists:cities,id'],
            'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Common validation rules for company classification master data
     */
    protected function getCompanyClassificationRules(): array
    {
        return [
            'company_size_id' => ['sometimes', 'required', 'exists:company_sizes,id'],
            'industry_id' => ['sometimes', 'required', 'exists:industries,id'],
            'ownership_type_id' => ['sometimes', 'required', 'exists:ownership_types,id'],
            'functional_area_id' => ['sometimes', 'required', 'exists:functional_areas,id'],
        ];
    }

    /**
     * Common validation rules for job classification master data
     */
    protected function getJobClassificationRules(): array
    {
        return [
            'job_category_id' => ['sometimes', 'required', 'exists:job_categories,id'],
            'salary_currency_id' => ['sometimes', 'required', 'exists:salary_currencies,id'],
            'salary_period_id' => ['sometimes', 'required', 'exists:salary_periods,id'],
            'degree_level_id' => ['sometimes', 'required', 'exists:required_degree_levels,id'],
            'experience_min' => ['sometimes', 'integer', 'min:0', 'max:50'],
            'experience_max' => ['sometimes', 'integer', 'min:0', 'max:50', 'gte:experience_min'],
        ];
    }

    /**
     * Common validation rules for multilingual content
     */
    protected function getMultilingualContentRules(): array
    {
        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_lt' => ['sometimes', 'string', 'max:255'],
            'description_en' => ['sometimes', 'string', 'max:1000'],
            'description_lt' => ['sometimes', 'string', 'max:1000'],
        ];
    }

    /**
     * Perform master data specific validation
     */
    protected function performCustomValidation($validator): void
    {
        // Check for duplicate names within the same category
        $this->validateUniqueName($validator);
        
        // Validate hierarchical relationships
        $this->validateHierarchicalRelationships($validator);
        
        // Validate business logic constraints
        $this->validateBusinessConstraints($validator);
    }

    /**
     * Validate unique name within category
     */
    protected function validateUniqueName($validator): void
    {
        // Override in specific request classes to implement name uniqueness validation
    }

    /**
     * Validate hierarchical relationships (e.g., state belongs to country)
     */
    protected function validateHierarchicalRelationships($validator): void
    {
        // Override in specific request classes to implement hierarchical validation
    }

    /**
     * Validate business logic constraints
     */
    protected function validateBusinessConstraints($validator): void
    {
        // Override in specific request classes to implement business logic validation
    }

    /**
     * Apply master data specific sanitization
     */
    protected function applySanitization(array $data): array
    {
        $sanitized = parent::applySanitization($data);

        // Master data specific sanitization
        if (isset($sanitized['name'])) {
            $sanitized['name'] = trim(ucwords(strtolower($sanitized['name'])));
        }

        if (isset($sanitized['description'])) {
            $sanitized['description'] = trim($sanitized['description']);
        }

        return $sanitized;
    }
} 