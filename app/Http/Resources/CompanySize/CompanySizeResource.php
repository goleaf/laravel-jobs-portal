<?php

namespace App\Http\Resources\CompanySize;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanySizeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'size' => $this->size,
            'display_name' => $this->getDisplayName(),
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'companies_count' => $this->whenCounted('companies'),
            'active_companies_count' => $this->whenCounted('activeCompanies'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'formatted_created_at' => $this->created_at?->format(__('formats.date_time')),
            'formatted_updated_at' => $this->updated_at?->format(__('formats.date_time')),
            'status_label' => $this->is_active ? __('common.active') : __('common.inactive'),
            'type_label' => $this->is_default ? __('common.default') : __('common.custom'),

            // Relationships
            'companies' => $this->whenLoaded('companies'),

            // Computed attributes
            'size_category' => $this->getSizeCategory(),
            'employee_range' => $this->getEmployeeRange(),

            // Permissions
            'can_update' => $request->user()?->can('update', $this->resource),
            'can_delete' => $request->user()?->can('delete', $this->resource),

            // Links
            'links' => [
                'self' => route('api.company-sizes.show', $this->id),
                'companies' => route('api.companies.index', ['company_size_id' => $this->id]),
            ],
        ];
    }

    /**
     * Get additional data when collection.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'total_company_sizes' => $this->collection?->count(),
                'active_company_sizes' => $this->collection?->where('is_active', true)->count(),
                'default_company_sizes' => $this->collection?->where('is_default', true)->count(),
            ],
        ];
    }

    /**
     * Get display name for the company size.
     */
    private function getDisplayName(): string
    {
        return __('company_sizes.display_format', ['size' => $this->size]);
    }

    /**
     * Get size category (small, medium, large).
     */
    private function getSizeCategory(): string
    {
        $size = strtolower($this->size);

        if (preg_match('/^(1-10|11-50|1-50)/', $size)) {
            return __('company_sizes.categories.small');
        }

        if (preg_match('/(51-200|51-250|101-500)/', $size)) {
            return __('company_sizes.categories.medium');
        }

        if (preg_match('/(500\+|1000\+|large)/', $size)) {
            return __('company_sizes.categories.large');
        }

        return __('company_sizes.categories.other');
    }

    /**
     * Get employee range description.
     */
    private function getEmployeeRange(): string
    {
        return __('company_sizes.employee_range', ['size' => $this->size]);
    }
}
