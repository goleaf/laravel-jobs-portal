<?php

namespace App\Livewire;

use App\Models\MaritalStatus;
use App\Livewire\Components\Column;

class MaritalStatusTable extends LivewireTableComponent
{
    /**
     * @var string
     */
    protected $model = MaritalStatus::class;

    /**
     * @var bool
     */
    public $showButtonOnHeader = true;
    public $showFilterOnHeader = false;

    /**
     * @var string
     */
    public $buttonComponent = 'marital_status.table-components.add_button';
    
    /**
     * Define filter components array to prevent undefined array key error
     * @var array
     */
    public array $filterComponents = [];

    // Disable the configurable areas functionality
    protected array $configurableAreas = [];

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setDefaultSort('created_at', 'desc');

        $this->setThAttributes(function (Column $column) {
            return [
                'class' => 'text-center',
            ];
        });

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($columnIndex == '2') {
                return [
                    'class' => 'text-center',
                    'width' => '15%',

                ];
            }

            return [];
        });

        $this->setTableAttributes([
            'default' => false,
            'class' => 'table table-striped',
        ]);

        $this->setQueryStringStatus(false);
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.marital_status.marital_status'), 'marital_status')
                ->sortable()
                ->searchable()
                ->view('marital_status.table-components.marital_status'),
            Column::make(__('messages.common.created_date'), 'created_at')
                ->sortable()
                ->view('marital_status.table-components.created_at'),
            Column::make(__('messages.common.action'), 'id')
                ->view('marital_status.table-components.action_button'),
        ];
    }
    
    // Explicitly override the hasConfigurableAreaFor method to avoid the error
    public function hasConfigurableAreaFor(string $area): bool
    {
        return false;
    }
    
    // Use a custom view for this table that doesn't rely on configurableAreas
    public function render(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $data = [
            'columns' => $this->getColumns(),
            'rows' => $this->getRows(),
            'primaryKey' => $this->getPrimaryKey(),
            'tableName' => $this->getTableName(),
        ];

        return view('livewire.marital-status-table', $data);
    }
}
