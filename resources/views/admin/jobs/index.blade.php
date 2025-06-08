@extends('layouts.app')

@section('title')
    {{ __('Manage Jobs') }}
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
    <div class="flex flex-wrap">
        <div class="flex-1 -12">
            <div class="bg-white shadow rounded -lg overflow-hidden">
                <div class="bg-white shadow rounded -lg overflow-hidden header flex justify-between items-center">
                    <h3>{{ __('Manage Jobs') }}</h3>
                    <a href="{{ route('admin.dashboard') }}" class="border border-gray-300 bg-transparent">
                        <i class="fas fa-plus"></i> {{ __('Add New Job') }}
                    </a>
                </div>
                <div class="bg-white shadow rounded -lg overflow-hidden body">
                    <!-- Search Filter -->
                    <div class="flex flex-wrap mb-3">
                        <div class="flex-1 md-4">
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="{{ __('Search jobs...') }}" id="searchInput">
                        </div>
                        <div class="flex-1 md-3">
                            <select class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500" id="statusFilter">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="active">{{  __(' __('admin.active') ')  }}</option>
                                <option value="inactive">{{  __(' __('admin.inactive') ')  }}</option>
                                <option value="expired">{{ __('Expired') }}</option>
                            </select>
                        </div>
                        <div class="flex-1 md-2">
                            <button class="border border-gray-300 bg-transparent" id="filterBtn">{{  __(' __('admin.filter') ')  }}</button>
                        </div>
                    </div>
                    
                    <!-- Jobs Table -->
                    <div class="w-full divide-y divide-gray-200 responsive">
                        <table class="min-w-full divide-y divide-gray-200 w-full divide-y divide-gray-200 striped">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Job Title') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Location') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Posted Date') }}</th>
                                    <th>{{  __(' __('admin.status') ')  }}</th>
                                    <th>{{  __(' __('admin.actions') ')  }}</th>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-indigo-600 -600">Full Time</span>
                                    </td>
                                    <td>{{ now()->subDays($i)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-green-600">{{ __('admin.active') }}</span>
                                    </td>
                                    <td>
                                        <div class="px-4 py-2 rounded font-medium transition-colors group" role="group">
                                            <a href="{{ route('admin.', $i) }}" class="border border-gray-300 bg-transparent" title="{{  __(' __('admin.view') ')  }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.', $i) }}" class="border border-gray-300 bg-transparent" title="{{  __(' __('admin.edit') ')  }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="border border-gray-300 bg-transparent" onclick="deleteJob({{ $i }})" title="{{  __(' __('admin.delete') ')  }}">
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
