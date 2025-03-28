<?php

namespace App\Livewire;

use App\Livewire\Columns\Column;
use App\Livewire\Filters\Filter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

abstract class DataTable extends Component
{
    use WithPagination;

    public array $selectedRows = [];
    public array $perPageOptions = [10, 25, 50, 100];
    public int $perPage = 10;
    public string $search = '';
    public string $sortField = '';
    public string $sortDirection = 'asc';
    public array $activeFilters = [];
    public int $debounce = 350;
    public string $tableClass = 'min-w-full divide-y divide-gray-200';
    
    /**
     * Cache key for storing table state
     */
    protected string $cacheKey;
    
    /**
     * Cache duration in seconds (10 minutes by default)
     */
    protected int $cacheDuration = 600;

    /**
     * Table name identifier
     */
    protected string $tableName;
    
    /**
     * Initialize properties and load settings from cache
     */
    public function mount(): void
    {
        $this->tableName = $this->getTableName();
        $this->cacheKey = "datatable_state_{$this->tableName}_" . auth()->id();
        
        $this->loadTableState();
    }
    
    /**
     * Clean up and store table state
     */
    public function dehydrate(): void
    {
        $this->storeTableState();
    }
    
    /**
     * Get a table name based on the current class
     */
    protected function getTableName(): string
    {
        $className = class_basename(static::class);
        return str_replace('Table', '', $className);
    }

    /**
     * Define the query for retrieving data
     */
    abstract public function query(): Builder;

    /**
     * Define columns for the table
     */
    abstract public function columns(): array;

    /**
     * Define actions for the table (optional)
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * Define filters for the table (optional)
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Store the table state in cache
     */
    protected function storeTableState(): void
    {
        if (!$this->tableName) {
            return;
        }
        
        $state = [
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'activeFilters' => $this->activeFilters,
        ];
        
        Cache::put($this->cacheKey, $state, $this->cacheDuration);
    }
    
    /**
     * Load table state from cache
     */
    protected function loadTableState(): void
    {
        if (!$this->tableName) {
            return;
        }
        
        $state = Cache::get($this->cacheKey);
        
        if ($state) {
            $this->perPage = $state['perPage'] ?? $this->perPage;
            $this->sortField = $state['sortField'] ?? $this->sortField;
            $this->sortDirection = $state['sortDirection'] ?? $this->sortDirection;
            $this->activeFilters = $state['activeFilters'] ?? $this->activeFilters;
        }
        
        // Set default sort field if not set
        if (empty($this->sortField) && !empty($this->columns())) {
            $this->sortField = $this->columns()[0]->field;
        }
    }

    /**
     * Handle pagination
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
        $this->storeTableState();
    }

    /**
     * Handle search input
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Handle filter changes
     */
    public function setFilter(string $name, $value): void
    {
        $this->activeFilters[$name] = $value;
        $this->resetPage();
        $this->storeTableState();
    }

    /**
     * Clear a filter
     */
    public function clearFilter(string $name): void
    {
        unset($this->activeFilters[$name]);
        $this->resetPage();
        $this->storeTableState();
    }

    /**
     * Clear all filters
     */
    public function clearAllFilters(): void
    {
        $this->activeFilters = [];
        $this->resetPage();
        $this->storeTableState();
    }

    /**
     * Sort the data
     */
    public function sort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->storeTableState();
    }

    /**
     * Apply sorting to the query
     */
    protected function applySorting(Builder $query): Builder
    {
        if ($this->sortField && $this->columnExists($this->sortField)) {
            return $query->orderBy($this->sortField, $this->sortDirection);
        }
        
        return $query;
    }

    /**
     * Apply search to the query
     */
    protected function applySearch(Builder $query): Builder
    {
        if (empty($this->search)) {
            return $query;
        }
        
        $columns = collect($this->columns())
            ->filter(fn (Column $column) => $column->searchable)
            ->map(fn (Column $column) => $column->field)
            ->toArray();
            
        if (empty($columns)) {
            return $query;
        }
        
        return $query->where(function (Builder $query) use ($columns) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', "%{$this->search}%");
            }
        });
    }

    /**
     * Apply filters to the query
     */
    protected function applyFilters(Builder $query): Builder
    {
        if (empty($this->activeFilters)) {
            return $query;
        }
        
        $filters = collect($this->filters());
        
        foreach ($this->activeFilters as $name => $value) {
            if (empty($value)) {
                continue;
            }
            
            $filter = $filters->first(fn (Filter $filter) => $filter->name === $name);
            
            if ($filter) {
                $query = $filter->apply($query, $value);
            }
        }
        
        return $query;
    }

    /**
     * Check if a column exists
     */
    protected function columnExists(string $field): bool
    {
        return collect($this->columns())->contains(fn (Column $column) => $column->field === $field);
    }

    /**
     * Get formatted data for the view
     */
    public function getData(): LengthAwarePaginator
    {
        $query = $this->query();
        
        $query = $this->applySearch($query);
        $query = $this->applyFilters($query);
        $query = $this->applySorting($query);
        
        return $query->paginate($this->perPage);
    }

    /**
     * Reset table to default state
     */
    public function resetTable(): void
    {
        $this->perPage = 10;
        $this->search = '';
        $this->sortField = $this->columns()[0]->field ?? '';
        $this->sortDirection = 'asc';
        $this->activeFilters = [];
        $this->resetPage();
        $this->storeTableState();
    }

    /**
     * Render the datatable
     */
    public function render()
    {
        return view('livewire.datatable', [
            'data' => $this->getData(),
            'columns' => $this->columns(),
            'actions' => $this->actions(),
            'filters' => $this->filters(),
        ]);
    }
} 