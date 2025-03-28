<?php

namespace App\Livewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

abstract class Table extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 10;
    public $perPageOptions = [10, 25, 50, 100];
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $search = '';
    public $filters = [];
    
    /**
     * The base query to use for data retrieval
     */
    abstract protected function query(): Builder;
    
    /**
     * Define the table columns
     */
    abstract protected function columns(): array;
    
    /**
     * Define the table filters
     */
    protected function filters(): array
    {
        return [];
    }
    
    /**
     * Define the table actions
     */
    protected function actions(): array
    {
        return [];
    }
    
    /**
     * Define the table bulk actions
     */
    protected function bulkActions(): array
    {
        return [];
    }
    
    /**
     * Apply the search filter to the query
     */
    protected function applySearch(Builder $query): Builder
    {
        if (empty($this->search)) {
            return $query;
        }
        
        $columns = $this->columns();
        $searchColumns = array_filter($columns, fn($column) => $column['searchable'] ?? false);
        
        return $query->where(function (Builder $query) use ($searchColumns) {
            foreach ($searchColumns as $column) {
                $field = $column['field'];
                
                if (str_contains($field, '.')) {
                    // Handle relationships
                    [$relation, $relationField] = explode('.', $field, 2);
                    $query->orWhereHas($relation, function (Builder $query) use ($relationField) {
                        $query->where($relationField, 'like', '%' . $this->search . '%');
                    });
                } else {
                    $query->orWhere($field, 'like', '%' . $this->search . '%');
                }
            }
        });
    }
    
    /**
     * Apply the sort to the query
     */
    protected function applySort(Builder $query): Builder
    {
        return $query->orderBy($this->sortField, $this->sortDirection);
    }
    
    /**
     * Apply filters to the query
     */
    protected function applyFilters(Builder $query): Builder
    {
        foreach ($this->filters as $key => $value) {
            if (empty($value)) {
                continue;
            }
            
            $filter = collect($this->filters())->firstWhere('key', $key);
            
            if (!$filter) {
                continue;
            }
            
            if (isset($filter['apply']) && is_callable($filter['apply'])) {
                $query = call_user_func($filter['apply'], $query, $value);
            } else {
                $query->where($key, $value);
            }
        }
        
        return $query;
    }
    
    /**
     * Get the data for the table
     */
    protected function getData(): LengthAwarePaginator
    {
        $query = $this->query();
        $query = $this->applySearch($query);
        $query = $this->applySort($query);
        $query = $this->applyFilters($query);
        
        return $query->paginate($this->perPage);
    }
    
    /**
     * Reset pagination when search or filters change
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    /**
     * Reset pagination when filters change
     */
    public function updatedFilters()
    {
        $this->resetPage();
    }
    
    /**
     * Reset pagination when per page changes
     */
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    /**
     * Set the sort field and direction
     */
    public function sort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->filters = [];
        $this->resetPage();
    }
    
    /**
     * Render the table
     */
    public function render(): View
    {
        return view('livewire.table', [
            'data' => $this->getData(),
            'columns' => $this->columns(),
            'filters' => $this->filters(),
            'actions' => $this->actions(),
            'bulkActions' => $this->bulkActions(),
        ]);
    }
} 