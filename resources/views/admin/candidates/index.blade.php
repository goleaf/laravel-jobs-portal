@extends('layouts.app')

@section('title')
    {{ __('Manage Candidates') }}
@endsection

@section('content')
<div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
    <div class="flex flex-wrap">
        <div class="flex-1 -12">
            <div class="bg-white shadow rounded -lg overflow-hidden">
                <div class="bg-white shadow rounded -lg overflow-hidden header flex justify-between items-center">
                    <h3>{{ __('Manage Candidates') }}</h3>
                    <a href="{{ route('admin.') }}" class="border border-gray-300 bg-transparent">
                        <i class="fas fa-plus"></i> {{ __('Add New Candidate') }}
                    </a>
                </div>
                <div class="bg-white shadow rounded -lg overflow-hidden body">
                    <!-- Search Filter -->
                    <div class="flex flex-wrap mb-3">
                        <div class="flex-1 md-4">
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="{{ __('Search candidates...') }}" id="searchInput">
                        </div>
                        <div class="flex-1 md-3">
                            <select class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500" id="statusFilter">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="active">{{ __('{{ __('admin.active') }}') }}</option>
                                <option value="inactive">{{ __('{{ __('admin.inactive') }}') }}</option>
                            </select>
                        </div>
                        <div class="flex-1 md-2">
                            <button class="border border-gray-300 bg-transparent" id="filterBtn">{{ __('{{ __('admin.filter') }}') }}</button>
                        </div>
                    </div>
                    
                    <!-- Candidates Table -->
                    <div class="w-full divide-y divide-gray-200 responsive">
                        <table class="min-w-full divide-y divide-gray-200 w-full divide-y divide-gray-200 striped">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Registration Date') }}</th>
                                    <th>{{ __('{{ __('admin.status') }}') }}</th>
                                    <th>{{ __('{{ __('admin.actions') }}') }}</th>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium {{ $candidate->is_verified ?"bg-success' : 'bg-warning' }}">
                                            {{ $candidate->is_verified ? __('Verified') : __('Pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="px-4 py-2 rounded font-medium transition-colors group" role="group">
                                            <a href="{{ route('admin.', $candidate->id) }}" class="border border-gray-300 bg-transparent" title="{{ __('{{ __('admin.view') }}') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.', $candidate->id) }}" class="border border-gray-300 bg-transparent" title="{{ __('{{ __('admin.edit') }}') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="border border-gray-300 bg-transparent" onclick="deleteCandidate({{ $candidate->id }})" title="{{ __('{{ __('admin.delete') }}') }}">
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
                        <ul class="pagination justify-center">
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


@endsection 
@push('scripts')
    @vite('resources/js/admin/index.js')
@endpush
