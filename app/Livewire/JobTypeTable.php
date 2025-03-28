<?php

namespace App\Livewire;

use App\Livewire\Components\Column;
use App\Models\JobType;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class JobTypeTable extends TableComponent
{
    protected $model = JobType::class;

    public $showButtonOnHeader = true;
    public $showFilterOnHeader = false;

    public $buttonComponent = 'job_types.table_components.add_button';
    
    protected $listeners = [
        'refresh' => '$refresh',
        'jobTypeSaved' => '$refresh',
        'jobTypeDeleted' => '$refresh'
    ];

    #[On('jobTypeSaved')]
    #[On('jobTypeDeleted')]
    public function refresh()
    {
        // This will re-render the component
    }

    public function columns(): array
    {
        return [
            Column::make('name')
                ->title(__('messages.job_type.name'))
                ->sortable()
                ->searchable()
                ->view('job_types.table_components.name'),
            
            Column::make('created_at')
                ->title(__('messages.common.created_date'))
                ->sortable()
                ->view('job_types.table_components.created_at'),
            
            Column::make('id')
                ->title(__('messages.common.action'))
                ->view('job_types.table_components.action_button'),
        ];
    }
    
    public function query(): Builder
    {
        return JobType::query()->select('job_types.*');
    }
    
    public function resetFilters()
    {
        $this->reset(['search', 'filters']);
        $this->resetPage();
    }
} 