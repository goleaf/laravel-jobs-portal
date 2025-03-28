<?php

namespace App\Livewire;

use App\Models\JobType;
use Livewire\Attributes\On;

class JobTypeTable extends LivewireTableComponent
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
            Column::make(__('messages.job_type.name'), 'name')
                ->sortable()
                ->searchable()
                ->view('job_types.table_components.name'),
            
            Column::make(__('messages.common.created_date'), 'created_at')
                ->sortable()
                ->view('job_types.table_components.created_at'),
            
            Column::make(__('messages.common.action'), 'id')
                ->view('job_types.table_components.action_button'),
        ];
    }
    
    public function resetFilters()
    {
        $this->reset(['search', 'filters']);
        $this->resetPage();
    }
} 