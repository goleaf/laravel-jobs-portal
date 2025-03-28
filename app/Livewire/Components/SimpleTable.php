<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SimpleTable extends Component
{
    use WithPagination;

    // Table properties
    public $model;
    public $columns = [];
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';
    public $searchColumn = 'name';
    public $searchTerm = '';
    public $perPage = 10;
    public $showSearch = true;
    public $showPagination = true;
    public $showHeader = true;
    
    // UI customization
    public $tableClass = 'table table-striped';
    public $thClass = 'text-center';
    public $tdClass = '';
    
    // Action buttons
    public $showAddButton = false;
    public $addButtonTitle = 'Add New';
    public $addButtonEvent = 'openAddModal';
    
    // Filters
    public $filters = [];
    public $appliedFilters = [];
    
    protected $listeners = ['refresh' => '$refresh'];

    public function mount($model = null, $columns = [], $sortColumn = null, $perPage = null)
    {
        $this->model = $model;
        $this->columns = $columns;
        
        if ($sortColumn) {
            $this->sortColumn = $sortColumn;
        }
        
        if ($perPage) {
            $this->perPage = $perPage;
        }
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function applyFilter($name, $value)
    {
        $this->appliedFilters[$name] = $value;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->appliedFilters = [];
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->searchTerm = '';
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    protected function getQuery()
    {
        $query = $this->model::query();
        
        // Apply search if provided
        if ($this->searchTerm && $this->searchColumn) {
            $query->where($this->searchColumn, 'like', '%' . $this->searchTerm . '%');
        }
        
        // Apply filters
        foreach ($this->appliedFilters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->where($field, $value);
            }
        }
        
        // Apply sorting
        if ($this->sortColumn) {
            $query->orderBy($this->sortColumn, $this->sortDirection);
        }
        
        return $query;
    }

    public function render()
    {
        $items = $this->getQuery()->paginate($this->perPage);
        
        return view('livewire.components.simple-table', [
            'items' => $items,
        ]);
    }
} 