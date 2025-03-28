<?php

namespace App\Livewire;

use App\Livewire\Components\Column;
use App\Livewire\Components\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;

abstract class TableComponent extends Component
{
    use WithPagination;

    // Set Tailwind theme for pagination
    protected $paginationTheme = 'tailwind';

    // Pagination
    #[Url(history: true)]
    public $perPage = 10;
    public $perPageOptions = [10, 25, 50, 100];

    // Searching
    #[Url(history: true)]
    public $search = '';
    public $searchDebounce = 300; // ms

    // Sorting
    #[Url(history: true)]
    public $sortField = '';
    #[Url(history: true)]
    public $sortDirection = 'asc';

    // Filters
    #[Url(history: true)]
    public $filters = [];
    public $showFilters = false;

    // UI control
    public $showButtonOnHeader = false;
    public $buttonComponent = null;
    public $showFilterOnHeader = true;

    // Event listeners
    protected $listeners = ['refresh' => '$refresh'];

    /**
     * Define the columns for the table
     */
    abstract public function columns(): array;

    /**
     * Define the query for the table
     */
    abstract public function query(): Builder;

    /**
     * Define the filters for the table
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Mount the component
     */
    public function mount()
    {
        $this->initializeFilters();
    }

    /**
     * Initialize filters with default values
     */
    protected function initializeFilters()
    {
        foreach ($this->filters() as $filter) {
            $this->filters[$filter->getKey()] = null;
        }
    }

    /**
     * Handle sorting column
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
     * Reset page when search changes
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset page when filters change
     */
    public function updatedFilters()
    {
        $this->resetPage();
    }

    /**
     * Reset page when per page changes
     */
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    /**
     * Toggle filters visibility
     */
    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->initializeFilters();
        $this->resetPage();
    }

    /**
     * Get formatted columns
     */
    #[Computed]
    protected function getColumns(): array
    {
        $columns = $this->columns();

        // Convert array format to Column objects if needed
        return collect($columns)->map(function ($column) {
            if ($column instanceof Column) {
                return $column;
            }

            // Convert array format to Column object
            $columnObj = Column::make($column['label'] ?? '', $column['field'] ?? null);

            if (isset($column['sortable']) && $column['sortable']) {
                $columnObj->sortable();
            }

            if (isset($column['searchable']) && $column['searchable']) {
                $columnObj->searchable();
            }

            if (isset($column['view'])) {
                $columnObj->view($column['view']);
            }

            if (isset($column['class'])) {
                $columnObj->class($column['class']);
            }

            if (isset($column['hidden']) && $column['hidden']) {
                $columnObj->hidden();
            }

            if (isset($column['format']) && is_callable($column['format'])) {
                $columnObj->format($column['format']);
            }

            return $columnObj;
        })->toArray();
    }

    /**
     * Get the results for the table
     */
    #[Computed]
    public function getTableData()
    {
        $query = $this->query();

        // Apply search if needed
        if (!empty($this->search)) {
            $query = $this->applySearch($query);
        }

        // Apply filters
        $query = $this->applyFilters($query);

        // Apply sorting
        if (!empty($this->sortField)) {
            $query = $query->orderBy($this->sortField, $this->sortDirection);
        }

        // Paginate
        return $query->paginate($this->perPage);
    }

    /**
     * Apply search to the query
     */
    protected function applySearch(Builder $query): Builder
    {
        $searchableColumns = collect($this->getColumns())
            ->filter(function (Column $column) {
                return $column->isSearchable() && !$column->isHidden();
            })
            ->map(function (Column $column) {
                return $column->getField();
            })
            ->toArray();

        if (empty($searchableColumns)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($searchableColumns) {
            foreach ($searchableColumns as $column) {
                $query->orWhere($column, 'like', '%' . $this->search . '%');
            }
        });
    }

    /**
     * Apply filters to the query
     */
    protected function applyFilters(Builder $query): Builder
    {
        collect($this->filters)
            ->filter(function ($value, $key) {
                return $value !== null && $value !== '';
            })
            ->each(function ($value, $key) use ($query) {
                $filter = collect($this->filters())->first(function (Filter $filter) use ($key) {
                    return $filter->getKey() === $key;
                });

                if ($filter && $filter->getCallback()) {
                    $callback = $filter->getCallback();
                    $callback($query, $value);
                }
            });

        return $query;
    }

    /**
     * Render the table
     */
    public function render()
    {
        return view('livewire.table', [
            'columns' => $this->getColumns(),
            'data' => $this->getTableData(),
            'filters' => $this->filters(),
        ]);
    }
} 