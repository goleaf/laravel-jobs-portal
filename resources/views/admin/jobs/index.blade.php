@extends('layouts.app')
@section('title')
    {{ __('Jobs Management') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ __('Jobs Management') }}</h1>
            <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Add Job') }}
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('All Jobs') }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Job Title') }}</th>
                                <th>{{ __('Company') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Expires') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td>{{ $job->id }}</td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $job->job_title }}</div>
                                            @if($job->job_type)
                                                <small class="text-muted">{{ $job->job_type }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($job->company->logo_url)
                                                <img src="{{ $job->company->logo_url }}" alt="Logo" class="rounded me-2" width="30" height="30">
                                            @else
                                                <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                    <i class="fas fa-building text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $job->company->name }}</div>
                                                <small class="text-muted">{{ $job->company->industry->name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($job->jobCategory)
                                            <span class="badge bg-info">{{ $job->jobCategory->name }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($job->country)
                                            {{ $job->country->name }}
                                            @if($job->state), {{ $job->state->name }}@endif
                                        @else
                                            <span class="text-muted">Remote</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($job->status)
                                            @case(1)
                                                <span class="badge bg-success">{{ __('Open') }}</span>
                                                @break
                                            @case(0)
                                                <span class="badge bg-secondary">{{ __('Draft') }}</span>
                                                @break
                                            @case(2)
                                                <span class="badge bg-warning">{{ __('Paused') }}</span>
                                                @break
                                            @case(3)
                                                <span class="badge bg-danger">{{ __('Closed') }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ __('Unknown') }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($job->job_expiry_date)
                                            <small class="{{ $job->job_expiry_date->isPast() ? 'text-danger' : 'text-success' }}">
                                                {{ $job->job_expiry_date->format('M d, Y') }}
                                            </small>
                                        @else
                                            <span class="text-muted">No expiry</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $job->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.jobs.show', $job->id) }}" class="btn btn-sm btn-outline-info" title="{{ __('View') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.jobs.edit', $job->id) }}" class="btn btn-sm btn-outline-warning" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.jobs.destroy', $job->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-briefcase fa-2x mb-2"></i>
                                            <p>{{ __('No jobs found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($jobs->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $jobs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endpush 