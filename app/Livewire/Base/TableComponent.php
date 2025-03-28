<?php

namespace App\Livewire\Base;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

abstract class TableComponent extends Component
{
    use WithPagination;

    // Pagination
    public int $perPage = 10;
    public array $perPageOptions = [10, 25, 50, 100];

    // Sorting
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Search
    public string $search = '';

    // UI Options
    public string $emptyMessage = 'messages.flash.no_record';
    public bool $showSearchInput = true;
    public bool $showPagination = true;
    public bool $showPerPageOptions = true;
    public bool $showHeader = true;
    
    // Common UI elements
    public bool $showButtonOnHeader = false;
    public string $buttonComponent = '';
    
    // Filters
    public array $filters = [];

    protected $listeners = ['refreshTable' => '$refresh'];

    /**
     * Define model used by the table
     */
    abstract protected function model(): string;

    /**
     * Define columns configuration
     */
    abstract public function columns(): array;

    /**
     * Initialize component
     */
    public function mount()
    {
        $this->initializeTable();
    }

    /**
     * Initialize table settings
     */
    protected function initializeTable(): void
    {
        // Override in child classes if needed
    }

    /**
     * Get table data
     */
    public function getTableData()
    {
        $query = $this->query();
        
        if (!empty($this->search)) {
            $query = $this->applySearch($query, $this->search);
        }
        
        $query = $this->applyFilters($query);
        
        return $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    /**
     * Base query for the table
     */
    protected function query(): Builder
    {
        $model = $this->model();
        return $model::query();
    }

    /**
     * Apply search to query
     */
    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            foreach ($this->getSearchableColumns() as $column) {
                $query->orWhere($column, 'like', '%'.$search.'%');
            }
        });
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Get columns that should be searched
     */
    protected function getSearchableColumns(): array
    {
        $searchableColumns = [];
        
        foreach ($this->columns() as $column) {
            if (isset($column['searchable']) && $column['searchable'] && isset($column['field'])) {
                $searchableColumns[] = $column['field'];
            }
        }
        
        return $searchableColumns;
    }

    /**
     * Sort by field
     */
    public function sortBy(string $field): void
    {
        $this->resetPage();
        
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Update per page value
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Update search value
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset all filters and search
     */
    public function resetAll(): void
    {
        $this->reset(['search', 'filters', 'sortField', 'sortDirection']);
        $this->resetPage();
    }

    /**
     * Check if column is sortable
     */
    public function isColumnSortable(array $column): bool
    {
        return $column['sortable'] ?? false;
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.base.table', [
            'data' => $this->getTableData(),
            'columns' => $this->columns(),
        ]);
    }
} 