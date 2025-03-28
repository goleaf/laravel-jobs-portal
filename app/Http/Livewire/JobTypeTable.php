<?php

namespace App\Http\Livewire;

use App\Models\JobType;
use Illuminate\Database\Eloquent\Builder;

class JobTypeTable extends BaseTable
{
    protected $listeners = ['refresh' => '$refresh'];

    public function getQuery(): Builder
    {
        return JobType::query()
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            });
    }
    
    public function getColumns(): array
    {
        return [
            [
                'field' => 'name',
                'label' => __('messages.job_type.name'),
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'field' => 'description',
                'label' => __('messages.job_type.description'),
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'component' => 'job-types.action_buttons',
                'label' => __('messages.common.actions'),
            ],
        ];
    }
    
    public function render()
    {
        $items = $this->getQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
            
        return view('livewire.job-type-table', [
            'items' => $items,
            'columns' => $this->getColumns(),
        ])->extends('layouts.app');
    }
} 