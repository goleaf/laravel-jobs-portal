<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

abstract class LivewireTableComponent extends Component
{
    use WithPagination;

    // Use Tailwind theme for pagination
    protected $paginationTheme = 'tailwind';
    
    // Core pagination properties
    public $perPage = 10;
    public $perPageOptions = [10, 25, 50, 100];
    
    // Sorting properties
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Search properties
    public $search = '';
    public $searchDebounce = 500; // ms
    public $searchableFields = [];
    
    // Filter properties
    public $filters = [];
    
    // Table properties
    public $tableName = 'livewire-table';
    public $primaryKey = 'id';
    
    // UI control
    public $showButtonOnHeader = false;
    public $showFilterOnHeader = false;
    public $buttonComponent = null;
    public $filterComponent = '';
    
    // Advanced features
    public $refreshInterval = null; // Set to a value in ms to enable polling
    
    protected $listeners = ['resetPage', 'refresh' => '$refresh'];

    protected $model;

    /**
     * Initialize the table
     */
    public function mount()
    {
        $this->initializeTable();
        $this->initializeFilters();
        $this->initializeDefaults();
    }

    /**
     * Initialize table properties
     * Override this in child components to customize
     */
    protected function initializeTable()
    {
        // Set default sort field and direction
        $this->sortField = $this->getDefaultSortField();
        $this->sortDirection = $this->getDefaultSortDirection();
    }

    /**
     * Initialize filters with default values
     */
    protected function initializeFilters()
    {
        // This can be overridden in child classes to set default filter values
    }

    /**
     * Initialize component defaults.
     */
    protected function initializeDefaults()
    {
        // Can be overridden by child components
    }

    /**
     * Get the default sort field
     */
    protected function getDefaultSortField(): string
    {
        return 'created_at';
    }

    /**
     * Get the default sort direction
     */
    protected function getDefaultSortDirection(): string
    {
        return 'desc';
    }

    /**
     * Get table name
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Get the primary key for the table
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * Get available columns
     * Override this in child components to define columns
     */
    abstract public function columns(): array;

    /**
     * Get available filters
     * Override this in child components to define filters
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Handle sorting when a column header is clicked
     */
    public function sort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->resetPage();
    }

    /**
     * Reset pagination when search or filters change
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filter changes
     */
    public function updatingFilters()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when per page changes
     */
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Reset the page
     */
    public function resetPage($pageName = 'page')
    {
        $this->gotoPage(1, $pageName);
    }

    /**
     * Reset all filters to default values
     */
    public function resetFilters()
    {
        $this->reset('filters');
        $this->initializeFilters();
        $this->resetPage();
    }

    /**
     * Reset a specific filter
     */
    public function resetFilter($key)
    {
        if (isset($this->filters[$key])) {
            unset($this->filters[$key]);
        }
        $this->resetPage();
    }

    /**
     * Select all options for a filter
     */
    public function selectAllFilters($key)
    {
        $filters = collect($this->filters())->firstWhere('key', $key);
        
        if ($filters && isset($filters['options'])) {
            $this->filters[$key] = array_keys($filters['options']);
        }
        
        $this->resetPage();
    }

    /**
     * Get the base query for the table
     */
    protected function getBaseQuery(): Builder
    {
        if (isset($this->model) && class_exists($this->model)) {
            return $this->model::query();
        }
        
        // Override this method in child classes if model is not set
        return collect([]);
    }

    /**
     * Apply search query to builder
     */
    protected function applySearch($builder)
    {
        if (empty($this->search)) {
            return $builder;
        }

        // Get searchable columns
        $searchableColumns = collect($this->columns())
            ->filter(function ($column) {
                return $column->isSearchable();
            })
            ->map(function ($column) {
                return $column->getField();
            })
            ->toArray();

        // Merge with any additional searchable fields
        $searchableColumns = array_merge($searchableColumns, $this->searchableFields);

        // Apply search to each column
        if (!empty($searchableColumns)) {
            $builder->where(function ($query) use ($searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $query->orWhere($column, 'like', '%' . $this->search . '%');
                }
            });
        }

        return $builder;
    }

    /**
     * Apply filters to the query
     */
    protected function applyFilters($builder)
    {
        foreach ($this->filters as $key => $value) {
            // Skip empty filters
            if ($value === '' || $value === null || $value === []) {
                continue;
            }

            // Apply filter (this can be overridden in child classes)
            $this->applyFilter($builder, $key, $value);
        }

        return $builder;
    }

    /**
     * Apply a single filter to the query
     * Override this in child classes to customize filter application
     */
    protected function applyFilter($builder, $key, $value)
    {
        if (is_array($value)) {
            $builder->whereIn($key, $value);
        } else {
            $builder->where($key, $value);
        }
        
        return $builder;
    }

    /**
     * Apply sorting to builder
     */
    protected function applySorting($builder)
    {
        return $builder->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Get the query results after applying search, filters, and sorting
     */
    public function getQueryResults()
    {
        $builder = $this->getBaseQuery();
        
        $builder = $this->applySearch($builder);
        $builder = $this->applyFilters($builder);
        $builder = $this->applySorting($builder);
        
        return $builder->paginate($this->perPage);
    }

    /**
     * Default render method that uses a standard table view
     */
    public function render()
    {
        $columns = collect($this->columns());
        $filters = collect($this->filters())->map(function($filter) {
            if (is_array($filter)) {
                return $filter;
            }
            return $filter->toArray();
        })->toArray();
        
        return view('livewire.table', [
            'columns' => $columns,
            'data' => $this->getQueryResults(),
            'filters' => $filters,
            'showButtonOnHeader' => $this->showButtonOnHeader,
            'showFilterOnHeader' => $this->showFilterOnHeader,
            'buttonComponent' => $this->buttonComponent,
        ]);
    }
}
