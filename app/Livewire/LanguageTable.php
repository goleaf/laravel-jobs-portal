<?php

namespace App\Livewire;

use App\Livewire\Components\Column;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;

class LanguageTable extends TableComponent
{
    // UI Options
    public $showButtonOnHeader = true;
    public $showFilterOnHeader = false;
    public $buttonComponent = 'languages.add_button';
    
    public function query(): Builder
    {
        return Language::query();
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.language.language'), 'language')
                ->sortable()
                ->searchable(),
                
            Column::make(__('messages.language.iso_code'), 'iso_code')
                ->sortable()
                ->searchable()
                ->view('languages.table-components.iso_code'),
                
            Column::make(__('messages.common.action'), 'id')
                ->view('languages.table-components.action_buttons')
                ->class('text-center'),
        ];
    }

    // Initialize sorting
    public function mount()
    {
        parent::mount();
        $this->sortField = 'language';
        $this->sortDirection = 'asc';
    }
}
