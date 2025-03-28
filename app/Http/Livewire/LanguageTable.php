<?php

namespace App\Http\Livewire;

use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;

class LanguageTable extends BaseTable
{
    protected $listeners = ['refresh' => '$refresh'];

    public function getQuery(): Builder
    {
        return Language::query()
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('language', 'like', '%' . $search . '%')
                        ->orWhere('iso_code', 'like', '%' . $search . '%');
                });
            });
    }
    
    public function getColumns(): array
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
            ],
            [
                'field' => 'is_default',
                'label' => __('messages.language.is_default'),
                'format' => function ($row) {
                    return $row->is_default 
                        ? '<span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">' . __('messages.common.yes') . '</span>'
                        : '<span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">' . __('messages.common.no') . '</span>';
                }
            ],
            [
                'component' => 'languages.action_buttons',
                'label' => __('messages.common.actions'),
            ],
        ];
    }
    
    public function render()
    {
        $items = $this->getQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
            
        return view('livewire.language-table', [
            'items' => $items,
            'columns' => $this->getColumns(),
        ])->extends('layouts.app');
    }
} 