<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

abstract class BaseTable extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $perPageOptions = [10, 25, 50, 100];
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $filters = [];
    public $showFilters = false;
    public $showButtonOnHeader = false;
    public $buttonComponent = null;
    public $showFilterOnHeader = false;
    public $filterComponent = null;
    public $tableName = 'data-table';

    protected $listeners = ['refresh' => '$refresh'];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    abstract public function columns(): array;
    abstract public function query(): Builder;

    public function filters(): array
    {
        return [];
    }

    public function getTableClass(): string
    {
        return 'min-w-full divide-y divide-gray-200 dark:divide-gray-700';
    }

    public function getTheadClass(): string
    {
        return 'bg-gray-50 dark:bg-gray-700';
    }

    public function getThClass(): string
    {
        return 'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider';
    }

    public function getTbodyClass(): string
    {
        return 'bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700';
    }

    public function getTrClass(): string
    {
        return 'hover:bg-gray-50 dark:hover:bg-gray-600';
    }

    public function getTdClass(): string
    {
        return 'px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300';
    }

    public function getPaginationClass(): string
    {
        return 'mt-4';
    }

    public function mount()
    {
        $this->resetFilters();
    }

    public function resetFilters()
    {
        $this->reset('filters');
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
        
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    #[Computed]
    public function rows()
    {
        $query = $this->query();
        
        // Apply search
        if (!empty($this->search)) {
            $this->applySearch($query);
        }
        
        // Apply filters
        $this->applyFilters($query);
        
        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);
        
        return $query->paginate($this->perPage);
    }

    protected function applySearch($query)
    {
        $searchColumns = $this->getSearchableColumns();
        
        if (empty($searchColumns)) {
            return;
        }
        
        $query->where(function ($subQuery) use ($searchColumns) {
            foreach ($searchColumns as $column) {
                $subQuery->orWhere($column, 'like', '%' . $this->search . '%');
            }
        });
    }

    protected function getSearchableColumns()
    {
        return collect($this->columns())
            ->filter(function ($column) {
                return isset($column['searchable']) && $column['searchable'];
            })
            ->pluck('field')
            ->toArray();
    }

    protected function applyFilters($query)
    {
        foreach ($this->filters as $key => $value) {
            if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
                continue;
            }
            
            $filter = collect($this->filters())->firstWhere('key', $key);
            
            if ($filter && isset($filter['callback']) && is_callable($filter['callback'])) {
                call_user_func($filter['callback'], $query, $value);
            }
        }
    }

    public function render()
    {
        return view('livewire.base-table', [
            'rows' => $this->rows,
            'columns' => $this->columns(),
            'filters' => $this->filters(),
        ]);
    }
} 