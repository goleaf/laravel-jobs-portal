<?php

namespace App\Livewire;

use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Components\Filter;

class JobTable extends LivewireTableComponent
{
    public $tableName = 'jobs';
    
    // UI settings
    public $showButtonOnHeader = true;
    public $showFilterOnHeader = true;
    public $buttonComponent = 'jobs.table-components.add_button';
    
    // Filter properties are now handled through $filters
    
    protected $listeners = [
        'resetPage', 
        'refresh' => '$refresh'
    ];

    /**
     * Initialize the table
     */
    protected function initializeTable()
    {
        parent::initializeTable();
        
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        
        // Set searchable fields
        $this->searchableFields = ['job_title', 'company.name', 'jobCategory.name'];
        
        $this->columns = [
            [
                'title' => __('messages.job.job_title'),
                'field' => 'job_title',
                'sortable' => true,
                'searchable' => true,
                'view' => 'jobs.table-components.job_title'
            ],
            [
                'title' => __('messages.job.is_featured'),
                'field' => 'hide_salary',
                'sortable' => true,
                'view' => 'jobs.table-components.is_featured'
            ],
            [
                'title' => __('messages.job.is_suspended'),
                'field' => 'is_suspended',
                'sortable' => true,
                'view' => 'jobs.table-components.is_suspended'
            ],
            [
                'title' => __('messages.common.created_on'),
                'field' => 'created_at',
                'sortable' => true,
                'searchable' => true,
                'view' => 'jobs.table-components.created_on'
            ],
            [
                'title' => __('messages.job.job_expiry_date'),
                'field' => 'job_expiry_date',
                'sortable' => true,
                'view' => 'jobs.table-components.expired_at'
            ],
            [
                'title' => __('messages.common.last_change_by'),
                'field' => 'last_change',
                'sortable' => true,
                'view' => 'jobs.table-components.last_change'
            ],
            [
                'title' => __('messages.common.action'),
                'field' => 'id',
                'view' => 'jobs.table-components.action_buttons'
            ],
        ];
    }

    /**
     * Set up filters for the table
     */
    protected function getFilters(): array
    {
        return [
            Filter::make('featured', __('messages.filter_name.featured_job'))
                ->options([
                    '' => __('messages.filter_name.select_featured_company'),
                    'yes' => __('messages.common.yes'),
                    'no' => __('messages.common.no'),
                ])
                ->toArray(),
                
            Filter::make('suspended', __('messages.filter_name.suspended_job'))
                ->options([
                    '' => __('messages.filter_name.select_suspended_job'),
                    '1' => __('messages.common.yes'),
                    '0' => __('messages.common.no'),
                ])
                ->toArray(),
                
            Filter::make('freelance', __('messages.filter_name.select_independent_work'))
                ->options([
                    '' => __('messages.filter_name.select_independent_work'),
                    '1' => __('messages.common.yes'),
                    '0' => __('messages.common.no'),
                ])
                ->toArray(),
                
            Filter::make('status', __('messages.filter_name.job_status'))
                ->options([
                    '' => __('messages.filter_name.job_status'),
                    'active' => __('messages.common.active'),
                    'expire' => __('messages.common.expire'),
                ])
                ->toArray(),
        ];
    }

    /**
     * Apply a single filter to the query
     */
    protected function applyFilter($builder, $key, $value)
    {
        switch ($key) {
            case 'featured':
                if ($value === 'yes') {
                    $builder->has('featured');
                } else {
                    $builder->doesntHave('featured');
                }
                break;
                
            case 'suspended':
                $builder->where('is_suspended', $value);
                break;
                
            case 'freelance':
                $builder->where('is_freelance', $value);
                break;
                
            case 'status':
                if ($value === 'expire') {
                    $builder->where('job_expiry_date', '<=', date('Y-m-d'));
                } else {
                    $builder->where('job_expiry_date', '>=', Carbon::tomorrow()->toDateString())
                        ->status(Job::STATUS_OPEN)
                        ->where('is_suspended', Job::NOT_SUSPENDED);
                }
                break;
                
            default:
                parent::applyFilter($builder, $key, $value);
        }
        
        return $builder;
    }

    /**
     * Get cell/TD attributes for specific column
     */
    public function getTdAttributes($column, $row, $colIndex, $rowIndex)
    {
        if ($colIndex == 6) {
            return [
                'class' => 'px-6 py-4 text-center w-32',
            ];
        }

        return ['class' => 'px-6 py-4 text-center'];
    }
    
    /**
     * Get data for the table 
     */
    protected function getData()
    {
        $query = Job::with('company', 'jobCategory', 'jobType', 'jobShift', 'activeFeatured', 'featured', 'admin');
        
        // Apply filters
        $query = $this->applyFilters($query);
        
        // Apply search
        $query = $this->applySearch($query);
        
        // Apply sorting
        $query = $this->applySorting($query);
        
        // Get paginated results
        return $query->paginate($this->perPage);
    }

    public function query(): Builder
    {
        return Job::query()
            ->with(['company', 'category', 'jobType'])
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('company', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                });
            });
    }

    public function getColumns(): array
    {
        return [
            [
                'key' => 'id',
                'label' => __('ID'),
                'sortable' => true,
            ],
            [
                'key' => 'title',
                'label' => __('Title'),
                'sortable' => true,
            ],
            [
                'key' => 'company_id',
                'label' => __('Company'),
                'sortable' => true,
                'format' => function ($row) {
                    return $row->company ? $row->company->name : '-';
                },
            ],
            [
                'key' => 'salary',
                'label' => __('Salary'),
                'sortable' => true,
                'format' => function ($row) {
                    return $row->salary_min . ' - ' . $row->salary_max;
                },
            ],
            [
                'key' => 'created_at',
                'label' => __('Posted'),
                'sortable' => true,
                'format' => function ($row) {
                    return $row->created_at->diffForHumans();
                },
            ],
        ];
    }

    public function getFilters(): array
    {
        return [
            [
                'key' => 'category_id',
                'label' => __('Category'),
                'type' => 'select',
                'options' => \App\Models\Category::pluck('name', 'id')->toArray(),
            ],
            [
                'key' => 'job_type_id',
                'label' => __('Job Type'),
                'type' => 'select',
                'options' => \App\Models\JobType::pluck('name', 'id')->toArray(),
            ],
            [
                'key' => 'salary_range',
                'label' => __('Salary Range'),
                'type' => 'select',
                'options' => [
                    '0-30000' => __('Up to 30,000'),
                    '30000-60000' => __('30,000 - 60,000'),
                    '60000-90000' => __('60,000 - 90,000'),
                    '90000-0' => __('90,000+'),
                ],
            ],
        ];
    }

    public function applyFilters($query): Builder
    {
        return $query
            ->when($this->filters['category_id'] ?? null, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($this->filters['job_type_id'] ?? null, function ($query, $jobTypeId) {
                return $query->where('job_type_id', $jobTypeId);
            })
            ->when($this->filters['salary_range'] ?? null, function ($query, $salaryRange) {
                $range = explode('-', $salaryRange);
                $min = (int) $range[0];
                $max = (int) $range[1];

                if ($min > 0 && $max > 0) {
                    return $query->where('salary_min', '>=', $min)
                        ->where('salary_max', '<=', $max);
                } elseif ($min > 0 && $max === 0) {
                    return $query->where('salary_min', '>=', $min);
                } elseif ($min === 0 && $max > 0) {
                    return $query->where('salary_max', '<=', $max);
                }

                return $query;
            });
    }

    public function getRowActions($row): array
    {
        $actions = [
            [
                'label' => __('View'),
                'method' => 'viewJob',
                'color' => 'primary',
            ],
        ];

        if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->id === $row->company->user_id)) {
            $actions[] = [
                'label' => __('Edit'),
                'method' => 'editJob',
                'color' => 'primary',
            ];
            $actions[] = [
                'label' => __('Delete'),
                'method' => 'deleteJob',
                'color' => 'red',
            ];
        }

        return $actions;
    }

    public function viewJob($id)
    {
        return redirect()->route('jobs.show', $id);
    }

    public function editJob($id)
    {
        return redirect()->route('jobs.edit', $id);
    }

    public function deleteJob($id)
    {
        $job = Job::find($id);

        if ($job && (Auth::user()->isAdmin() || Auth::user()->id === $job->company->user_id)) {
            $job->delete();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('Job deleted successfully.'),
            ]);
        }
    }
}
