<?php

namespace App\Livewire\Tables;

use App\Livewire\Base\TableComponent;
use App\Models\MaritalStatus;

class MaritalStatusTable extends TableComponent
{
    /**
     * Table configuration
     */
    protected function initializeTable(): void
    {
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->showButtonOnHeader = true;
        $this->buttonComponent = 'marital_status.table-components.add_button';
    }

    /**
     * Define the model
     */
    protected function model(): string
    {
        return MaritalStatus::class;
    }

    /**
     * Define the columns
     */
    public function columns(): array
    {
        return [
            [
                'label' => __('messages.marital_status.marital_status'),
                'field' => 'marital_status',
                'sortable' => true,
                'searchable' => true,
                'view' => 'marital_status.table-components.marital_status',
            ],
            [
                'label' => __('messages.common.created_date'),
                'field' => 'created_at',
                'sortable' => true,
                'view' => 'marital_status.table-components.created_at',
            ],
            [
                'label' => __('messages.common.action'),
                'field' => 'id',
                'view' => 'marital_status.table-components.action_button',
                'cellClass' => 'text-center',
            ],
        ];
    }
} 