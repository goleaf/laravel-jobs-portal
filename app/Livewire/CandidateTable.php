<?php

namespace App\Livewire;

use App\Livewire\Components\Column;
use App\Livewire\Components\DataTable;
use App\Livewire\Components\Filter;
use App\Models\Candidate;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CandidateTable extends DataTable
{
    protected $listeners = ['resetPage', 'refreshDatatable' => '$refresh', 'changeStatusFilter', 'changeImmediateFilter'];

    public string $tableName = 'candidates';
    public bool $showButtonOnHeader = true;
    public bool $showFilterOnHeader = true;
    public string $buttonComponent = 'candidates.table-components.add_button';
    
    public $status = Candidate::ALL;
    public $immediate = Candidate::ALL;

    protected function initializeComponent()
    {
        $this->filterComponents = ['candidates.table-components.filter'];
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.company.candidate_name'), 'user.first_name')
                ->sortable()
                ->searchable()
                ->view('candidates.table-components.name_email'),

            Column::make(__('messages.company.email'), 'user.email')
                ->hideIf(true)
                ->searchable(),

            Column::make(__('messages.candidate.available_at'), 'immediate_available')
                ->view('candidates.table-components.available'),

            Column::make(__('messages.company.email_verified'), 'user.email_verified_at')
                ->sortable()
                ->view('candidates.table-components.email_verified'),

            Column::make(__('messages.common.status'), 'user.is_active')
                ->sortable()
                ->view('candidates.table-components.status'),

            Column::make(__('messages.common.last_change_by'), 'last_change')
                ->sortable()
                ->view('candidates.table-components.last_change'),

            Column::make(__('messages.common.action'), 'id')
                ->view('candidates.table-components.action_button'),
        ];
    }

    public function builder(): Builder
    {
        $query = Candidate::with(['user.candidateSkill', 'admin']);

        if ($this->status != Candidate::ALL) {
            $status = $this->status == Candidate::ACTIVE;
            $query->whereHas('user', function($q) use ($status) {
                $q->where('is_active', $status);
            });
        }

        if ($this->immediate != Candidate::ALL) {
            $immediate = $this->immediate == Candidate::IMMEDIATE_AVAILABLE;
            $query->where('immediate_available', $immediate);
        }

        return $query;
    }

    protected function applySearch(Builder $query): Builder
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->whereHas('user', function ($userQuery) {
            $userQuery->where(function ($q) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $this->search . '%'])
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        });
    }

    public function changeStatusFilter($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function changeImmediateFilter($immediate)
    {
        $this->immediate = $immediate;
        $this->resetPage();
    }
}
