<?php

namespace App\Http\Livewire;

use App\Models\JobType;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;

class JobTypeTable extends BaseTable
{
    /**
     * Define the table model
     */
    public function getModelProperty(): string
    {
        return JobType::class;
    }

    /**
     * Define the table columns
     */
    public function getColumnsProperty(): array
    {
        return [
            [
                'field' => 'id',
                'label' => __('messages.common.id'),
                'sortable' => true,
            ],
            [
                'field' => 'name',
                'label' => __('messages.job_type.name'),
                'sortable' => true,
                'searchable' => true,
                'filterable' => true,
            ],
            [
                'field' => 'description',
                'label' => __('messages.job_type.description'),
                'sortable' => true,
                'searchable' => true,
                'filterable' => true,
            ],
            [
                'field' => 'created_at',
                'label' => __('messages.common.created_at'),
                'sortable' => true,
                'format' => function ($model) {
                    return $model->created_at->format('Y-m-d H:i');
                },
                'filterable' => true,
                'filter_type' => 'date_range',
            ],
            [
                'label' => __('messages.common.actions'),
                'component' => 'job-types.action-buttons',
            ],
        ];
    }

    /**
     * Apply additional query constraints
     */
    public function baseQuery(): Builder
    {
        return parent::baseQuery();
    }

    /**
     * Render the component view
     */
    public function render()
    {
        return view('livewire.job-type-table');
    }
} 