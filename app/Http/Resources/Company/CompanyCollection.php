<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = CompanyResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => $this->getMeta($request),
            'filters' => $this->getAppliedFilters($request),
            'statistics' => $this->getStatistics(),
        ];
    }

    /**
     * Get the pagination metadata.
     *
     * @param Request $request
     * @return array
     */
    protected function getMeta(Request $request): array
    {
        $paginator = $this->resource;
        
        return [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'first_page_url' => $paginator->url(1),
                'last_page_url' => $paginator->url($paginator->lastPage()),
            ],
            'request_info' => [
                'generated_at' => now()->toISOString(),
                'locale' => app()->getLocale(),
                'user_timezone' => $request->user()?->timezone ?? config('app.timezone'),
                'version' => '1.0',
                'response_time' => round((microtime(true) - LARAVEL_START) * 1000, 2) . 'ms',
            ],
        ];
    }

    /**
     * Get applied filters information.
     *
     * @param Request $request
     * @return array
     */
    protected function getAppliedFilters(Request $request): array
    {
        $filters = [];

        // Search filters
        if ($request->filled('search')) {
            $filters['search'] = [
                'value' => $request->input('search'),
                'label' => __('filters.search'),
                'type' => 'text',
            ];
        }

        // Location filters
        if ($request->filled('city_id')) {
            $filters['city'] = [
                'value' => $request->input('city_id'),
                'label' => __('filters.city'),
                'type' => 'select',
            ];
        }

        if ($request->filled('state_id')) {
            $filters['state'] = [
                'value' => $request->input('state_id'),
                'label' => __('filters.state'),
                'type' => 'select',
            ];
        }

        if ($request->filled('country_id')) {
            $filters['country'] = [
                'value' => $request->input('country_id'),
                'label' => __('filters.country'),
                'type' => 'select',
            ];
        }

        // Company size filter
        if ($request->filled('company_size_id')) {
            $filters['company_size'] = [
                'value' => $request->input('company_size_id'),
                'label' => __('filters.company_size'),
                'type' => 'select',
            ];
        }

        // Industry filter
        if ($request->filled('industry_id')) {
            $filters['industry'] = [
                'value' => $request->input('industry_id'),
                'label' => __('filters.industry'),
                'type' => 'select',
            ];
        }

        // Ownership type filter
        if ($request->filled('ownership_type_id')) {
            $filters['ownership_type'] = [
                'value' => $request->input('ownership_type_id'),
                'label' => __('filters.ownership_type'),
                'type' => 'select',
            ];
        }

        // Status filters
        if ($request->filled('is_active')) {
            $filters['status'] = [
                'value' => $request->boolean('is_active'),
                'label' => __('filters.status'),
                'type' => 'boolean',
            ];
        }

        if ($request->filled('is_featured')) {
            $filters['featured'] = [
                'value' => $request->boolean('is_featured'),
                'label' => __('filters.featured'),
                'type' => 'boolean',
            ];
        }

        if ($request->filled('is_verified')) {
            $filters['verified'] = [
                'value' => $request->boolean('is_verified'),
                'label' => __('filters.verified'),
                'type' => 'boolean',
            ];
        }

        // Date filters
        if ($request->filled('created_from')) {
            $filters['created_from'] = [
                'value' => $request->input('created_from'),
                'label' => __('filters.created_from'),
                'type' => 'date',
            ];
        }

        if ($request->filled('created_to')) {
            $filters['created_to'] = [
                'value' => $request->input('created_to'),
                'label' => __('filters.created_to'),
                'type' => 'date',
            ];
        }

        // Sorting
        if ($request->filled('sort_by')) {
            $filters['sort'] = [
                'field' => $request->input('sort_by'),
                'direction' => $request->input('sort_direction', 'asc'),
                'label' => __('filters.sort_by'),
                'type' => 'sort',
            ];
        }

        return [
            'applied' => $filters,
            'count' => count($filters),
            'clear_url' => $request->url(),
            'available_filters' => $this->getAvailableFilters(),
        ];
    }

    /**
     * Get available filter options.
     *
     * @return array
     */
    protected function getAvailableFilters(): array
    {
        return [
            'sort_options' => [
                'name' => __('filters.sort_by_name'),
                'created_at' => __('filters.sort_by_newest'),
                'updated_at' => __('filters.sort_by_recently_updated'),
                'jobs_count' => __('filters.sort_by_jobs_count'),
                'followers_count' => __('filters.sort_by_popularity'),
                'established_in' => __('filters.sort_by_establishment'),
            ],
            'status_options' => [
                'active' => __('filters.active_companies'),
                'inactive' => __('filters.inactive_companies'),
                'featured' => __('filters.featured_companies'),
                'verified' => __('filters.verified_companies'),
            ],
            'date_ranges' => [
                'today' => __('filters.today'),
                'this_week' => __('filters.this_week'),
                'this_month' => __('filters.this_month'),
                'this_year' => __('filters.this_year'),
                'last_30_days' => __('filters.last_30_days'),
                'last_90_days' => __('filters.last_90_days'),
            ],
        ];
    }

    /**
     * Get collection statistics.
     *
     * @return array
     */
    protected function getStatistics(): array
    {
        $collection = $this->collection;
        
        return [
            'summary' => [
                'total_companies' => $collection->count(),
                'active_companies' => $collection->where('is_active', true)->count(),
                'featured_companies' => $collection->where('is_featured', true)->count(),
                'verified_companies' => $collection->where('is_profile_verified', true)->count(),
            ],
            'distribution' => [
                'by_size' => $collection->groupBy('company_size_id')->map->count(),
                'by_industry' => $collection->groupBy('industry_id')->map->count(),
                'by_country' => $collection->groupBy('country_id')->map->count(),
                'by_establishment_year' => $collection->groupBy('established_in')->map->count(),
            ],
            'averages' => [
                'avg_jobs_per_company' => round($collection->avg('jobs_count') ?? 0, 2),
                'avg_followers_per_company' => round($collection->avg('followers_count') ?? 0, 2),
                'avg_team_size' => round($collection->avg('team_size') ?? 0, 2),
            ],
            'ranges' => [
                'establishment_years' => [
                    'min' => $collection->min('established_in'),
                    'max' => $collection->max('established_in'),
                ],
                'team_sizes' => [
                    'min' => $collection->min('team_size'),
                    'max' => $collection->max('team_size'),
                ],
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'links' => [
                'self' => $request->url(),
                'create' => route('api.companies.store'),
                'export' => route('api.companies.export', $request->query()),
            ],
            'actions' => [
                'bulk_actions' => $this->getBulkActions($request),
                'export_formats' => ['csv', 'excel', 'pdf'],
                'import_url' => route('api.companies.import'),
            ],
            'cache_info' => [
                'cached_at' => cache()->get('companies_cached_at'),
                'cache_key' => 'companies_' . md5($request->getQueryString()),
                'ttl' => config('cache.ttl.companies', 3600),
            ],
        ];
    }

    /**
     * Get available bulk actions based on user permissions.
     *
     * @param Request $request
     * @return array
     */
    protected function getBulkActions(Request $request): array
    {
        $user = $request->user();
        $actions = [];

        if ($user && $user->hasRole('admin')) {
            $actions = [
                'activate' => __('actions.activate_selected'),
                'deactivate' => __('actions.deactivate_selected'),
                'feature' => __('actions.feature_selected'),
                'unfeature' => __('actions.unfeature_selected'),
                'verify' => __('actions.verify_selected'),
                'unverify' => __('actions.unverify_selected'),
                'delete' => __('actions.delete_selected'),
                'export' => __('actions.export_selected'),
            ];
        } elseif ($user && $user->hasRole('employer')) {
            $actions = [
                'export' => __('actions.export_selected'),
            ];
        }

        return $actions;
    }
}
