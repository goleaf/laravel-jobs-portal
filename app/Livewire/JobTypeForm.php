<?php

namespace App\Livewire;

use App\Models\JobType;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;

class JobTypeForm extends Component
{
    public $showModal = false;
    public $name = '';
    public $jobTypeId = null;
    public $isEditing = false;

    protected $listeners = [
        'showJobTypeModal' => 'showModal',
        'editJobType' => 'edit',
        'deleteJobType' => 'delete',
    ];

    public function showModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->jobTypeId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function edit($data)
    {
        $jobType = JobType::findOrFail($data['id']);
        $this->jobTypeId = $jobType->id;
        $this->name = $jobType->name;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $validator = Validator::make(
            ['name' => $this->name],
            ['name' => 'required|string|max:120|unique:job_types,name' . ($this->jobTypeId ? ',' . $this->jobTypeId : '')]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $key => $value) {
                $this->addError($key, $value[0]);
            }
            return;
        }

        if ($this->jobTypeId) {
            // Update existing job type
            $jobType = JobType::findOrFail($this->jobTypeId);
            $jobType->update(['name' => $this->name]);
            $message = __('messages.flash.update_success');
        } else {
            // Create new job type
            JobType::create(['name' => $this->name]);
            $message = __('messages.flash.create_success');
        }

        $this->dispatch('jobTypeSaved');
        $this->closeModal();
        session()->flash('success', $message);
    }

    public function delete($data)
    {
        $jobType = JobType::findOrFail($data['id']);
        $jobType->delete();
        
        $this->dispatch('jobTypeDeleted');
        session()->flash('success', __('messages.flash.delete_success'));
    }

    public function render()
    {
        return view('livewire.job-type-form');
    }
} 