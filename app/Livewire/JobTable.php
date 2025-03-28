<?php

namespace App\Livewire;

use App\Livewire\Components\Column;
use App\Livewire\Components\DataTable;
use App\Livewire\Components\Filter;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobType;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class JobTable extends DataTable
{
    public string $tableName = 'jobs';
    public bool $showButtonOnHeader = true;
    public bool $showFilterOnHeader = true;
    public string $buttonComponent = 'jobs.table-components.add_button';
    
    protected function initializeComponent()
    {
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
    }

    /**
     * Define the table columns
     */
    public function columns(): array
    {
        return [
            Column::make(__('messages.job.job_title'), 'job_title')
                ->sortable()
                ->searchable()
                ->view('jobs.table-components.title'),
                
            Column::make(__('messages.job.job_company'), 'company.company_name')
                ->sortable()
                ->searchable(),
                
            Column::make(__('messages.job.job_category'), 'jobCategory.name')
                ->sortable()
                ->searchable(),
                
            Column::make(__('messages.job.job_type'), 'jobType.name')
                ->sortable()
                ->searchable(),
                
            Column::make(__('messages.job.is_featured'), 'is_featured')
                ->sortable()
                ->view('jobs.table-components.featured'),
                
            Column::make(__('messages.job.is_suspended'), 'is_suspended')
                ->sortable()
                ->view('jobs.table-components.suspended'),
                
            Column::make(__('messages.job.job_expiry_date'), 'job_expiry_date')
                ->sortable()
                ->format(function ($value) {
                    return Carbon::parse($value)->format('Y-m-d');
                }),
                
            Column::make(__('messages.common.action'), 'id')
                ->view('jobs.table-components.action_buttons'),
        ];
    }

    /**
     * Define the table filters
     */
    public function filters(): array
    {
        return [
            Filter::make(__('messages.filter_name.featured_job'), 'is_featured')
                ->select([
                    '1' => __('messages.common.yes'),
                    '0' => __('messages.common.no'),
                ]),
                
            Filter::make(__('messages.filter_name.suspended_job'), 'is_suspended')
                ->select([
                    '1' => __('messages.common.yes'),
                    '0' => __('messages.common.no'),
                ]),
                
            Filter::make(__('messages.job.job_category'), 'job_category_id')
                ->select(JobCategory::pluck('name', 'id')->toArray()),
                
            Filter::make(__('messages.job.job_type'), 'job_type_id')
                ->select(JobType::pluck('name', 'id')->toArray()),
                
            Filter::make(__('messages.job.job_expiry_date'), 'job_expiry_date')
                ->dateRange(),
        ];
    }

    /**
     * Define the base query
     */
    public function builder(): Builder
    {
        return Job::query()
            ->with(['company', 'jobCategory', 'jobType'])
            ->select('jobs.*');
    }

    /**
     * Apply search to the query
     */
    protected function applySearch(Builder $query): Builder
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($query) {
            $query->where('job_title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('company', function ($q) {
                      $q->where('company_name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('jobCategory', function ($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('jobType', function ($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        });
    }

    /**
     * Handle view job action
     */
    public function view($id)
    {
        return redirect()->route('admin.jobs.show', $id);
    }

    /**
     * Handle edit job action
     */
    public function edit($id)
    {
        return redirect()->route('admin.jobs.edit', $id);
    }

    /**
     * Handle delete job action
     */
    public function delete($id)
    {
        $job = Job::findOrFail($id);
        
        if ($job->is_default) {
            $this->dispatchBrowserEvent('error', [
                'message' => __('messages.job.cannot_delete_default'),
            ]);
            return;
        }
        
        $job->delete();
        
        $this->dispatchBrowserEvent('success', [
            'message' => __('messages.flash.delete_success'),
        ]);
    }

    /**
     * Handle create job action
     */
    public function createJob()
    {
        return redirect()->route('admin.jobs.create');
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.job-table');
    }
}
