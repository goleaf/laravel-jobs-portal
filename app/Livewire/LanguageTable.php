<?php

namespace App\Livewire;

use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;

class LanguageTable extends BaseTable
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
            [
                'field' => 'language',
                'label' => __('messages.language.language'),
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'field' => 'iso_code',
                'label' => __('messages.language.iso_code'),
                'sortable' => true,
                'searchable' => true,
                'view' => 'languages.iso_code',
            ],
            [
                'field' => 'id',
                'label' => __('messages.common.action'),
                'sortable' => false,
                'searchable' => false,
                'view' => 'languages.action_buttons',
                'class' => 'text-center',
            ],
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
