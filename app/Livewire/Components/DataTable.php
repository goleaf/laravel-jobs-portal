<?php

namespace App\Livewire\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

abstract class DataTable extends Component
{
    use WithPagination;

    // Pagination settings
    public $perPage = 10;
    public $perPageOptions = [10, 25, 50, 100];
    protected $paginationTheme = 'tailwind';
    
    // Sorting settings
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Search settings
    public string $search = '';
    public $searchDebounce = 300; // ms
    
    // Filters
    public array $filters = [];
    
    // Table properties
    public string $tableName = 'data-table';
    public $primaryKey = 'id';
    public bool $showButtonOnHeader = false;
    public bool $showFilterOnHeader = false;
    
    // UI Components
    public $buttonComponent = null;
    public array $filterComponents = [];
    
    protected $listeners = ['resetPage', 'refreshDatatable' => '$refresh'];

    public function mount()
    {
        $this->initializeComponent();
    }

    protected function initializeComponent()
    {
        // Override in child class if needed
    }

    /**
     * Get the query builder instance.
     */
    abstract public function builder(): Builder;

    /**
     * Get the columns for the table.
     */
    abstract public function columns(): array;

    /**
     * Sort by specified field.
     */
    public function sortBy($field)
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
     * Apply the search query to the builder.
     */
    protected function applySearch(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Apply sort to the query builder.
     */
    protected function applySort(Builder $query): Builder
    {
        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Apply filters to the query builder.
     */
    protected function applyFilters(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->reset('filters');
        $this->resetPage();
    }

    /**
     * Reset the page when search is updated
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Get the final query results
     */
    public function getResults()
    {
        $query = $this->builder();
        $query = $this->applySearch($query);
        $query = $this->applyFilters($query);
        $query = $this->applySort($query);
        
        return $query->paginate($this->perPage);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.components.data-table', [
            'results' => $this->getResults(),
            'columns' => $this->columns(),
        ]);
    }
} 