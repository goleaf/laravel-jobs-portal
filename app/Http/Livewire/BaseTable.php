<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

abstract class BaseTable extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $search = '';
    public $selected = [];
    public $selectAll = false;
    public $selectPage = false;
    public $filters = [];
    public $activeFilters = [];
    public $showFilters = false;

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search', 'sortField', 'sortDirection', 'perPage', 'activeFilters'];
    protected $listeners = ['refresh' => '$refresh'];

    public function updatedSelectPage($value)
    {
        $this->selected = $value 
            ? $this->getQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function updatedSelected()
    {
        $this->selectAll = false;
        $this->selectPage = false;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    public function clearSearch()
    {
        $this->search = '';
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function applyFilter($field, $value)
    {
        $this->activeFilters[$field] = $value;
        $this->resetPage();
    }

    public function removeFilter($field)
    {
        unset($this->activeFilters[$field]);
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->activeFilters = [];
        $this->resetPage();
    }

    public function resetPage()
    {
        $this->resetPage(pageName: 'page');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getSelectedCountProperty()
    {
        return count($this->selected);
    }

    abstract public function getQuery();
    
    abstract public function getColumns();

    public function hasSearchableColumns()
    {
        return collect($this->getColumns())->contains(fn($column) => isset($column['searchable']) && $column['searchable']);
    }

    public function hasFilterableColumns()
    {
        return collect($this->getColumns())->contains(fn($column) => isset($column['filterable']) && $column['filterable']);
    }

    public function getFilterableColumns()
    {
        return collect($this->getColumns())->filter(fn($column) => isset($column['filterable']) && $column['filterable']);
    }

    public function render()
    {
        $query = $this->getQuery();
        
        // Apply active filters
        foreach ($this->activeFilters as $field => $value) {
            if (!empty($value)) {
                $query->where($field, $value);
            }
        }
        
        $items = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage, pageName: 'page');

        return view('livewire.base-table', [
            'items' => $items,
            'columns' => $this->getColumns(),
            'filterableColumns' => $this->getFilterableColumns(),
        ]);
    }
} 