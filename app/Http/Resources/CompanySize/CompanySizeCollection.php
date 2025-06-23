<?php

namespace App\Http\Resources\CompanySize;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanySizeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'active_count' => $this->collection->where('is_active', true)->count(),
                'inactive_count' => $this->collection->where('is_active', false)->count(),
                'default_count' => $this->collection->where('is_default', true)->count(),
                'custom_count' => $this->collection->where('is_default', false)->count(),
                'categories' => [
                    'small' => $this->collection->filter(function ($item) {
                        return preg_match('/^(1-10|11-50|1-50)/', strtolower($item->size));
                    })->count(),
                    'medium' => $this->collection->filter(function ($item) {
                        return preg_match('/(51-200|51-250|101-500)/', strtolower($item->size));
                    })->count(),
                    'large' => $this->collection->filter(function ($item) {
                        return preg_match('/(500\+|1000\+|large)/', strtolower($item->size));
                    })->count(),
                ],
                'statistics' => [
                    'most_popular' => $this->getMostPopular(),
                    'least_popular' => $this->getLeastPopular(),
                    'newest' => $this->getNewest(),
                    'oldest' => $this->getOldest(),
                ],
            ],
            'links' => [
                'create' => route('api.company-sizes.store'),
                'export' => route('api.company-sizes.export'),
            ],
            'messages' => [
                'total_message' => __('company_sizes.collection.total_message', ['count' => $this->collection->count()]),
                'active_message' => __('company_sizes.collection.active_message', ['count' => $this->collection->where('is_active', true)->count()]),
                'empty_message' => $this->collection->isEmpty() ? __('company_sizes.collection.empty_message') : null,
            ],
        ];
    }

    /**
     * Get most popular company size.
     */
    private function getMostPopular(): ?array
    {
        $mostPopular = $this->collection
            ->sortByDesc('companies_count')
            ->first()
        ;

        if (!$mostPopular) {
            return null;
        }

        return [
            'id' => $mostPopular->id,
            'size' => $mostPopular->size,
            'companies_count' => $mostPopular->companies_count ?? 0,
            'message' => __('company_sizes.statistics.most_popular', [
                'size' => $mostPopular->size,
                'count' => $mostPopular->companies_count ?? 0,
            ]),
        ];
    }

    /**
     * Get least popular company size.
     */
    private function getLeastPopular(): ?array
    {
        $leastPopular = $this->collection
            ->sortBy('companies_count')
            ->first()
        ;

        if (!$leastPopular) {
            return null;
        }

        return [
            'id' => $leastPopular->id,
            'size' => $leastPopular->size,
            'companies_count' => $leastPopular->companies_count ?? 0,
            'message' => __('company_sizes.statistics.least_popular', [
                'size' => $leastPopular->size,
                'count' => $leastPopular->companies_count ?? 0,
            ]),
        ];
    }

    /**
     * Get newest company size.
     */
    private function getNewest(): ?array
    {
        $newest = $this->collection
            ->sortByDesc('created_at')
            ->first()
        ;

        if (!$newest) {
            return null;
        }

        return [
            'id' => $newest->id,
            'size' => $newest->size,
            'created_at' => $newest->created_at?->toISOString(),
            'message' => __('company_sizes.statistics.newest', [
                'size' => $newest->size,
                'date' => $newest->created_at?->format(__('formats.date')),
            ]),
        ];
    }

    /**
     * Get oldest company size.
     */
    private function getOldest(): ?array
    {
        $oldest = $this->collection
            ->sortBy('created_at')
            ->first()
        ;

        if (!$oldest) {
            return null;
        }

        return [
            'id' => $oldest->id,
            'size' => $oldest->size,
            'created_at' => $oldest->created_at?->toISOString(),
            'message' => __('company_sizes.statistics.oldest', [
                'size' => $oldest->size,
                'date' => $oldest->created_at?->format(__('formats.date')),
            ]),
        ];
    }
}
