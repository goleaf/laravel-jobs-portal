<?php

namespace App\Livewire;

use App\Models\JobCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Column;
use Livewire\Attributes\Filter;

class JobCategoryTable extends LivewireTableComponent
{
    protected $model = JobCategory::class;

    public $showButtonOnHeader = true;
    public $showFilterOnHeader = true;

    public $buttonComponent = 'job_categories.table_components.add_button';

    protected $listeners = [
        'refresh' => '$refresh',
        'jobCategorySaved' => '$refresh',
        'jobCategoryDeleted' => '$refresh'
    ];

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    
    public $filters = [
        'featured' => '',
        'date_range' => [
            'start' => '',
            'end' => '',
        ],
    ];

    public function updatingSearch()
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

    public function resetFilters()
    {
        $this->reset('filters');
        $this->search = '';
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    #[On('jobCategorySaved')]
    #[On('jobCategoryDeleted')]
    public function refresh()
    {
        // This will re-render the component
    }

    public function getJobCategoriesProperty()
    {
        return $this->jobCategoriesQuery()->paginate($this->perPage);
    }

    private function jobCategoriesQuery(): Builder
    {
        $query = JobCategory::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Apply featured filter
        if ($this->filters['featured'] !== '') {
            $query->where('is_featured', $this->filters['featured']);
        }

        // Apply date range filter
        if (!empty($this->filters['date_range']['start'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_range']['start']);
        }
        
        if (!empty($this->filters['date_range']['end'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_range']['end']);
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.job_category.name'), 'name')
                ->sortable()
                ->searchable()
                ->view('job_categories.table_components.name'),
            
            Column::make(__('messages.job_category.is_featured'), 'is_featured')
                ->sortable()
                ->view('job_categories.table_components.is_featured'),
                
            Column::make(__('messages.common.created_date'), 'created_at')
                ->sortable()
                ->view('job_categories.table_components.created_at'),
            
            Column::make(__('messages.common.action'), 'id')
                ->view('job_categories.table_components.action_button'),
        ];
    }
    
    public function filters(): array
    {
        return [
            Filter::make(__('messages.job_category.is_featured'), 'is_featured')
                ->select([
                    '1' => __('messages.common.yes'),
                    '0' => __('messages.common.no'),
                ])
        ];
    }

    public function render()
    {
        return view('livewire.job-category-table', [
            'jobCategories' => $this->jobCategories,
        ]);
    }
} 