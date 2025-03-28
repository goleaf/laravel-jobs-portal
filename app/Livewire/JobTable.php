<?php

namespace App\Livewire;

use App\Livewire\Components\Table;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobType;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class JobTable extends Table
{
    protected $listeners = ['refresh' => '$refresh'];

    /**
     * Define the base query for the table
     */
    protected function query(): Builder
    {
        return Job::query()
            ->with(['company', 'jobCategory', 'jobType'])
            ->select('jobs.*');
    }

    /**
     * Define the table columns
     */
    protected function columns(): array
    {
        return [
            [
                'label' => __('messages.job.job_title'),
                'field' => 'job_title',
                'sortable' => true,
                'searchable' => true,
                'format' => function ($job) {
                    return view('jobs.table-components.title', ['job' => $job]);
                }
            ],
            [
                'label' => __('messages.job.job_company'),
                'field' => 'company.company_name',
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'label' => __('messages.job.job_category'),
                'field' => 'jobCategory.name',
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'label' => __('messages.job.job_type'),
                'field' => 'jobType.name',
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'label' => __('messages.job.is_featured'),
                'field' => 'is_featured',
                'sortable' => true,
                'format' => function ($job) {
                    return view('jobs.table-components.featured', ['job' => $job]);
                }
            ],
            [
                'label' => __('messages.job.is_suspended'),
                'field' => 'is_suspended',
                'sortable' => true,
                'format' => function ($job) {
                    return view('jobs.table-components.suspended', ['job' => $job]);
                }
            ],
            [
                'label' => __('messages.job.job_expiry_date'),
                'field' => 'job_expiry_date',
                'sortable' => true,
                'format' => function ($job) {
                    return Carbon::parse($job->job_expiry_date)->format('Y-m-d');
                }
            ],
        ];
    }

    /**
     * Define the table filters
     */
    protected function filters(): array
    {
        return [
            [
                'key' => 'featured',
                'label' => __('messages.filter_name.featured_job'),
                'type' => 'select',
                'options' => [
                    '1' => __('messages.common.yes'),
                    '0' => __('messages.common.no'),
                ],
                'apply' => function (Builder $query, $value) {
                    return $query->where('is_featured', $value);
                }
            ],
            [
                'key' => 'suspended',
                'label' => __('messages.filter_name.suspended_job'),
                'type' => 'select',
                'options' => [
                    '1' => __('messages.common.yes'),
                    '0' => __('messages.common.no'),
                ],
                'apply' => function (Builder $query, $value) {
                    return $query->where('is_suspended', $value);
                }
            ],
            [
                'key' => 'job_category_id',
                'label' => __('messages.job.job_category'),
                'type' => 'select',
                'options' => JobCategory::pluck('name', 'id')->toArray(),
            ],
            [
                'key' => 'job_type_id',
                'label' => __('messages.job.job_type'),
                'type' => 'select',
                'options' => JobType::pluck('name', 'id')->toArray(),
            ],
            [
                'key' => 'expiry_date',
                'label' => __('messages.job.job_expiry_date'),
                'type' => 'date',
                'apply' => function (Builder $query, $value) {
                    return $query->whereDate('job_expiry_date', $value);
                }
            ],
        ];
    }

    /**
     * Define the row actions
     */
    protected function rowActions(): array
    {
        return [
            [
                'label' => __('messages.common.edit'),
                'action' => 'edit',
                'icon' => '<i class="fas fa-edit"></i>',
                'tooltip' => __('messages.common.edit'),
            ],
            [
                'label' => __('messages.common.view'),
                'action' => 'view',
                'icon' => '<i class="fas fa-eye"></i>',
                'tooltip' => __('messages.common.view'),
            ],
            [
                'label' => __('messages.common.delete'),
                'action' => 'delete',
                'icon' => '<i class="fas fa-trash"></i>',
                'tooltip' => __('messages.common.delete'),
                'visible' => function ($job) {
                    return !$job->is_default;
                }
            ],
        ];
    }

    /**
     * Define the table actions
     */
    protected function actions(): array
    {
        return [
            [
                'label' => __('messages.job.new_job'),
                'action' => 'createJob',
                'icon' => '<i class="fas fa-plus"></i>',
            ],
        ];
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
