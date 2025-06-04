@extends('layouts.app')

@section('title')
    {{ __('Manage Candidates') }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>{{ __('Manage Candidates') }}</h3>
                    <a href="{{ route('admin.candidates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Add New Candidate') }}
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="{{ __('Search candidates...') }}" id="searchInput">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="statusFilter">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-secondary" id="filterBtn">{{ __('Filter') }}</button>
                        </div>
                    </div>
                    
                    <!-- Candidates Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Registration Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\User::where('role_name', 'candidate')->orWhereNull('role_name')->latest()->take(10)->get() as $candidate)
                                <tr>
                                    <td>{{ $candidate->id }}</td>
                                    <td>{{ $candidate->first_name }} {{ $candidate->last_name }}</td>
                                    <td>{{ $candidate->email }}</td>
                                    <td>{{ $candidate->phone ?? __('Not provided') }}</td>
                                    <td>{{ $candidate->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge {{ $candidate->is_verified ? 'bg-success' : 'bg-warning' }}">
                                            {{ $candidate->is_verified ? __('Verified') : __('Pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.candidates.show', $candidate->id) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" onclick="deleteCandidate({{ $candidate->id }})" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('No candidates found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Candidates pagination">
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
function deleteCandidate(id) {
    if (confirm('{{ __("Are you sure you want to delete this candidate?") }}')) {
        // Implementation for delete functionality
        fetch(`/admin/candidates/${id}`, {
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