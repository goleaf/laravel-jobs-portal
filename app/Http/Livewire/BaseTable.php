<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

abstract class BaseTable extends Component
{
    use WithPagination;

    // UI properties
    public $showButtonOnHeader = true;
    public $showFilterOnHeader = true;
    public $paginationTheme = 'tailwind';
    public $buttonComponent = null;
    public $search = '';
    public $perPage = 10;
    public $perPageOptions = [10, 25, 50, 100];
    
    // Sorting
    public $sortField = 'id';
    public $sortDirection = 'desc';
    
    // Events
    protected $listeners = ['refresh' => '$refresh'];

    // Abstract methods that must be implemented by child classes
    abstract public function getQuery(): Builder;
    abstract public function getColumns(): array;
    
    public function getTableClass(): string
    {
        return 'w-full table-fixed';
    }
    
    public function getTheadClass(): string
    {
        return 'text-left bg-gray-100';
    }
    
    public function getThClass(): string
    {
        return 'p-3 text-sm font-semibold tracking-wide';
    }
    
    public function getTrClass($row, $index): string
    {
        return $index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
    }
    
    public function getTdClass(): string
    {
        return 'p-3 text-sm';
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function getFilteredQuery()
    {
        $query = $this->getQuery();
        
        // Apply search if provided
        if ($this->search) {
            $columns = $this->getSearchColumns();
            if (!empty($columns)) {
                $query->where(function(Builder $q) use ($columns) {
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'like', '%'.$this->search.'%');
                    }
                });
            }
        }
        
        // Apply sorting
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        }
        
        return $query;
    }
    
    public function getSearchColumns()
    {
        $columns = $this->getColumns();
        $searchableColumns = [];
        
        foreach ($columns as $column) {
            if (isset($column['searchable']) && $column['searchable'] && isset($column['field'])) {
                $searchableColumns[] = $column['field'];
            }
        }
        
        return $searchableColumns;
    }
    
    public function render()
    {
        $data = $this->getFilteredQuery()->paginate($this->perPage);
        
        return view('livewire.base-table', [
            'data' => $data,
            'columns' => $this->getColumns(),
            'showButtonOnHeader' => $this->showButtonOnHeader,
            'showFilterOnHeader' => $this->showFilterOnHeader,
            'buttonComponent' => $this->buttonComponent,
        ]);
    }
} 