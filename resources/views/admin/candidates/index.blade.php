@extends('layouts.app')
@section('title')
    {{ __('Candidates Management') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ __('Candidates Management') }}</h1>
            <a href="{{ route('admin.candidates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Add Candidate') }}
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('All Candidates') }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Experience') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Registered') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($candidates as $candidate)
                                <tr>
                                    <td>{{ $candidate->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($candidate->user->avatar_url)
                                                <img src="{{ $candidate->user->avatar_url }}" alt="Avatar" class="rounded-circle me-2" width="30" height="30">
                                            @else
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $candidate->user->full_name }}</div>
                                                @if($candidate->user->current_salary)
                                                    <small class="text-muted">{{ $candidate->user->current_salary }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $candidate->user->email }}</td>
                                    <td>{{ $candidate->user->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if($candidate->experience)
                                            {{ $candidate->experience->name ?? 'Not specified' }}
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($candidate->user->is_active)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $candidate->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.candidates.show', $candidate->id) }}" class="btn btn-sm btn-outline-info" title="{{ __('View') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="btn btn-sm btn-outline-warning" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.candidates.destroy', $candidate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
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
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <p>{{ __('No candidates found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($candidates->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $candidates->links() }}
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