<?php

namespace App\Livewire;

use App\Models\JobCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class JobCategoryForm extends Component
{
    use WithFileUploads;
    
    public $showModal = false;
    public $name = '';
    public $description = '';
    public $image = null;
    public $existingImage = null;
    public $is_featured = false;
    public $jobCategoryId = null;
    public $isEditing = false;

    protected $listeners = [
        'showJobCategoryModal' => 'showModal',
        'editJobCategory' => 'edit',
        'deleteJobCategory' => 'delete',
        'showJobCategory' => 'show',
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
        $this->description = '';
        $this->image = null;
        $this->existingImage = null;
        $this->is_featured = false;
        $this->jobCategoryId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function edit($data)
    {
        $jobCategory = JobCategory::findOrFail($data['id']);
        $this->jobCategoryId = $jobCategory->id;
        $this->name = $jobCategory->name;
        $this->description = $jobCategory->description;
        $this->existingImage = $jobCategory->image_url;
        $this->is_featured = (bool) $jobCategory->is_featured;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function show($data)
    {
        $this->edit($data);
        // Additional logic for showing details if needed
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:120|unique:job_categories,name' . ($this->jobCategoryId ? ',' . $this->jobCategoryId : ''),
            'description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
        ];
        
        if ($this->image || (!$this->isEditing)) {
            $rules['image'] = 'required|image|max:2048'; // 2MB max
        }
        
        $validator = Validator::make([
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'is_featured' => $this->is_featured,
        ], $rules);

        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $key => $value) {
                $this->addError($key, $value[0]);
            }
            return;
        }

        // Handle the image upload if there's a new image
        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('job-categories', 'public');
        }

        if ($this->jobCategoryId) {
            // Update existing job category
            $jobCategory = JobCategory::findOrFail($this->jobCategoryId);
            
            // Delete old image if uploading a new one
            if ($imagePath && $jobCategory->image) {
                Storage::disk('public')->delete($jobCategory->image);
            }
            
            $jobCategory->update([
                'name' => $this->name,
                'description' => $this->description,
                'is_featured' => $this->is_featured,
                'image' => $imagePath ?: $jobCategory->image,
            ]);
            
            $message = __('messages.flash.update_success');
        } else {
            // Create new job category
            JobCategory::create([
                'name' => $this->name,
                'description' => $this->description,
                'is_featured' => $this->is_featured,
                'image' => $imagePath,
            ]);
            
            $message = __('messages.flash.create_success');
        }

        $this->dispatch('jobCategorySaved');
        $this->closeModal();
        session()->flash('success', $message);
    }

    public function delete($data)
    {
        $jobCategory = JobCategory::findOrFail($data['id']);
        
        // Delete the image file
        if ($jobCategory->image) {
            Storage::disk('public')->delete($jobCategory->image);
        }
        
        $jobCategory->delete();
        
        $this->dispatch('jobCategoryDeleted');
        session()->flash('success', __('messages.flash.delete_success'));
    }

    public function render()
    {
        return view('livewire.job-category-form');
    }
} 