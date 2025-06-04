@extends('layouts.app')

@section('title')
    {{ __('Manage Jobs') }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>{{ __('Manage Jobs') }}</h3>
                    <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Add New Job') }}
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="{{ __('Search jobs...') }}" id="searchInput">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="statusFilter">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                                <option value="expired">{{ __('Expired') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-secondary" id="filterBtn">{{ __('Filter') }}</button>
                        </div>
                    </div>
                    
                    <!-- Jobs Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Job Title') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Location') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Posted Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data - replace with real model data when Job model is available -->
                                @for($i = 1; $i <= 10; $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>Sample Job Title {{ $i }}</td>
                                    <td>Sample Company {{ $i }}</td>
                                    <td>New York, NY</td>
                                    <td>
                                        <span class="badge bg-primary">Full Time</span>
                                    </td>
                                    <td>{{ now()->subDays($i)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.jobs.show', $i) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.jobs.edit', $i) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" onclick="deleteJob({{ $i }})" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Jobs pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <span class="page-link">{{ __('Previous') }}</span>
                            </li>
                            <li class="page-item active">
                                <span class="page-link">1</span>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">{{ __('Next') }}</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteJob(id) {
    if (confirm('{{ __("Are you sure you want to delete this job?") }}')) {
        fetch(`/admin/jobs/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endsection 