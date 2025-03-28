<?php

namespace App\Livewire;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

class JobTagTable extends BaseTable
{
    // UI Options
    public $showButtonOnHeader = true;
    public $showFilterOnHeader = false;
    public $buttonComponent = 'job_tags.table_components.add_button';
    
    public function query(): Builder
    {
        return Tag::query();
    }

    public function columns(): array
    {
        return [
            [
                'field' => 'name',
                'label' => __('messages.job_tag.job_tag'),
                'sortable' => true,
                'searchable' => true,
                'view' => 'job_tags.table_components.name',
            ],
            [
                'field' => 'description',
                'label' => __('messages.common.description'),
                'sortable' => true,
                'searchable' => true,
                'view' => 'job_tags.table_components.description',
                'class' => 'w-[65%]',
            ],
            [
                'field' => 'id',
                'label' => __('messages.common.action'),
                'sortable' => false,
                'searchable' => false,
                'view' => 'job_tags.table_components.action_button',
                'class' => 'w-[15%] text-center',
            ],
        ];
    }

    // Initialize sorting
    public function mount()
    {
        parent::mount();
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
    }

    // Custom table classes
    public function getTableClass(): string
    {
        return 'table table-striped';
    }
}
