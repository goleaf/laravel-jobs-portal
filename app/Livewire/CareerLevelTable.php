<?php

namespace App\Livewire;

use App\Models\CareerLevel;
use App\Livewire\Components\Column;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class CareerLevelTable extends LivewireTableComponent
{
    protected $model = CareerLevel::class;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'career_levels.table-components.add_button';
    
    // Properties to store form data
    public $careerLevelId = '';
    public $levelName = '';
    public $editingCareerLevel = null;

    protected $listeners = [
        'refreshDatatable' => '$refresh',
        'createCareerLevel' => 'create',
        'updateCareerLevel' => 'update',
        'editCareerLevel' => 'edit',
        'deleteCareerLevel' => 'delete'
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setDefaultSort('created_at', 'desc');

        $this->setTableAttributes([
            'default' => false,
            'class' => 'table table-striped',
        ]);

        $this->setThAttributes(function (Column $column) {
            if ($column->isField('level_name')) {
                return[
                    'style' => 'width:70%',
                    'class' => 'text-start',
                ];
            }

            return[
                'class' => 'min-w-100px text-center',
            ];
        });

        $this->setQueryStringStatus(false);
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.career_level.level_name'), 'level_name')
                ->sortable()
                ->searchable(),
            Column::make(__('common.created_date'), 'created_at')
                ->sortable()
                ->searchable()
                ->view('career_levels.table-components.created_at'),
            Column::make(__('common.action'), 'id')
                ->view('career_levels.table-components.action_button'),
        ];
    }
    
    /**
     * Create a new career level
     */
    #[On('createCareerLevel')]
    public function create($data)
    {
        try {
            CareerLevel::create([
                'level_name' => $data['levelName']
            ]);
            
            $this->dispatch('showSuccessToast', message: __('career_level.created_successfully'));
            
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('showErrorToast', message: __('common.something_went_wrong'));
            
            return false;
        }
    }
    
    /**
     * Load career level data for editing
     */
    #[On('editCareerLevel')]
    public function edit($data)
    {
        try {
            $careerLevel = CareerLevel::findOrFail($data['id']);
            $this->careerLevelId = $careerLevel->id;
            $this->levelName = $careerLevel->level_name;
            $this->editingCareerLevel = $careerLevel;
            
            // Dispatch event to fill form fields
            $this->dispatch('fillCareerLevelForm', [
                'id' => $careerLevel->id,
                'levelName' => $careerLevel->level_name
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('showErrorToast', message: __('common.something_went_wrong'));
            
            return false;
        }
    }
    
    /**
     * Update career level
     */
    #[On('updateCareerLevel')]
    public function update($data)
    {
        try {
            $careerLevel = CareerLevel::findOrFail($data['id']);
            $careerLevel->update([
                'level_name' => $data['levelName']
            ]);
            
            $this->dispatch('showSuccessToast', message: __('career_level.updated_successfully'));
            
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('showErrorToast', message: __('common.something_went_wrong'));
            
            return false;
        }
    }
    
    /**
     * Delete career level
     */
    #[On('deleteCareerLevel')]
    public function delete($data)
    {
        try {
            $careerLevel = CareerLevel::findOrFail($data['id']);
            $careerLevel->delete();
            
            $this->dispatch('showSuccessToast', message: __('career_level.deleted_successfully'));
            
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('showErrorToast', message: __('common.something_went_wrong'));
            
            return false;
        }
    }
}
