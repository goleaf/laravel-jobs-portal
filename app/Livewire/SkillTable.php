<?php

namespace App\Livewire;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Builder;

class SkillTable extends BaseTable
{
    // UI Options
    public $showButtonOnHeader = true;
    public $showFilterOnHeader = false;
    public $buttonComponent = 'skills.add_button';
    
    public function query(): Builder
    {
        return Skill::query();
    }

    public function columns(): array
    {
        return [
            [
                'field' => 'name',
                'label' => __('messages.common.name'),
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'field' => 'description',
                'label' => __('messages.common.description'),
                'sortable' => true,
                'searchable' => true,
                'format' => function($row) {
                    return $row->description ? \Illuminate\Support\Str::limit($row->description, 100) : __('messages.common.n/a');
                }
            ],
            [
                'field' => 'id',
                'label' => __('messages.common.action'),
                'sortable' => false,
                'searchable' => false,
                'view' => 'skills.action_buttons',
                'class' => 'text-center',
            ],
        ];
    }
}
