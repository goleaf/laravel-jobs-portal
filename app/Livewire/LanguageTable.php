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
    public $buttonComponent = 'languages.table-components.add_button';
    
    public function query(): Builder
    {
        return Language::query();
    }

    public function columns(): array
    {
        return [
            Column::make('language')
                ->title(__('messages.language.language'))
                ->sortable()
                ->searchable(),
                
            Column::make('iso_code')
                ->title(__('messages.language.iso_code'))
                ->sortable()
                ->searchable()
                ->view('languages.table-components.iso_code'),
                
            Column::make('id')
                ->title(__('messages.common.action'))
                ->view('languages.table-components.action_button')
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
