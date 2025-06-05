@extends('layouts.app')

@section('title')
    {{ __('Admin Dashboard') }}
@endsection

@section('content')
<div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
    <div class="flex flex-wrap">
        <div class="flex-1 -12">
            <div class="bg-white shadow rounded -lg overflow-hidden">
                <div class="bg-white shadow rounded -lg overflow-hidden header">
                    <h3>{{ __('Admin Dashboard') }}</h3>
                </div>
                <div class="bg-white shadow rounded -lg overflow-hidden body">
                    <div class="flex flex-wrap">
                        <!-- Statistics Cards -->
                        <div class="flex-1 md-3 mb-4">
                            <div class="bg-white shadow rounded -lg overflow-hidden bg-indigo-600 -600 text-white">
                                <div class="bg-white shadow rounded -lg overflow-hidden body">
                                    <div class="flex justify-between">
                                        <div>
                                            <h5>{{ __('Total Users') }}</h5>
                                            <h2>{{ \App\Models\User::count() }}</h2>
                                        </div>
                                        <div>
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex-1 md-3 mb-4">
                            <div class="bg-white shadow rounded -lg overflow-hidden bg-green-600 text-white">
                                <div class="bg-white shadow rounded -lg overflow-hidden body">
                                    <div class="flex justify-between">
                                        <div>
                                            <h5>{{ __('Active Jobs') }}</h5>
                                            <h2>{{ \App\Models\Job::count() ?? 0 }}</h2>
                                        </div>
                                        <div>
                                            <i class="fas fa-briefcase fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex-1 md-3 mb-4">
                            <div class="bg-white shadow rounded -lg overflow-hidden bg-yellow-500 text-white">
                                <div class="bg-white shadow rounded -lg overflow-hidden body">
                                    <div class="flex justify-between">
                                        <div>
                                            <h5>{{ __('Companies') }}</h5>
                                            <h2>{{ \App\Models\Company::count() ?? 0 }}</h2>
                                        </div>
                                        <div>
                                            <i class="fas fa-building fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex-1 md-3 mb-4">
                            <div class="bg-white shadow rounded -lg overflow-hidden bg-blue-500 text-white">
                                <div class="bg-white shadow rounded -lg overflow-hidden body">
                                    <div class="flex justify-between">
                                        <div>
                                            <h5>{{ __('Applications') }}</h5>
                                            <h2>{{ \App\Models\JobApplication::count() ?? 0 }}</h2>
                                        </div>
                                        <div>
                                            <i class="fas fa-file-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex flex-wrap mt-4">
                        <div class="flex-1 -12">
                            <h4>{{ __('admin.quick_actions') }}</h4>
                            <div class="flex flex-wrap">
                                <div class="flex-1 md-2 mb-3">
                                    <a href="{{ route('admin.candidates.index') }}" class="border border-gray-300 bg-transparent">
                                        <i class="fas fa-users"></i><br>
                                        {{ __('Manage Candidates') }}
                                    </a>
                                </div>
                                <div class="flex-1 md-2 mb-3">
                                    <a href="{{ route('admin.jobs.index') }}" class="border border-gray-300 bg-transparent">
                                        <i class="fas fa-briefcase"></i><br>
                                        {{ __('Manage Jobs') }}
                                    </a>
                                </div>
                                <div class="flex-1 md-2 mb-3">
                                    <a href="{{ route('company.index') }}" class="border border-gray-300 bg-transparent">
                                        <i class="fas fa-building"></i><br>
                                        {{ __('Manage Companies') }}
                                    </a>
                                </div>
                                <div class="flex-1 md-2 mb-3">
                                    <a href="{{ route('admin.') }}" class="border border-gray-300 bg-transparent">
                                        <i class="fas fa-money-bill"></i><br>
                                        {{ __('Transactions') }}
                                    </a>
                                </div>
                                <div class="flex-1 md-2 mb-3">
                                    <a href="{{ route('admin.') }}" class="border border-gray-300 bg-transparent">
                                        <i class="fas fa-cog"></i><br>
                                        {{ __('{{ __('admin.settings') }}') }}
                                    </a>
                                </div>
                                <div class="flex-1 md-2 mb-3">
                                    <a href="{{ route('admin.') }}" class="border border-gray-300 bg-transparent">
                                        <i class="fas fa-envelope"></i><br>
                                        {{ __('Subscribers') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="flex flex-wrap mt-4">
                        <div class="flex-1 md-6">
                            <div class="bg-white shadow rounded -lg overflow-hidden">
                                <div class="bg-white shadow rounded -lg overflow-hidden header">
                                    <h5>{{ __('Recent Registrations') }}</h5>
                                </div>
                                <div class="bg-white shadow rounded -lg overflow-hidden body">
                                    <div class="list-group">
                                        @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                                        <div class="list-group-item">
                                            <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                            <small class="text-gray-500">{{ $user->created_at->diffForHumans() }}</small>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex-1 md-6">
                            <div class="bg-white shadow rounded -lg overflow-hidden">
                                <div class="bg-white shadow rounded -lg overflow-hidden header">
                                    <h5>{{ __('{{ __('admin.system_status') }}') }}</h5>
                                </div>
                                <div class="bg-white shadow rounded -lg overflow-hidden body">
                                    <div class="flex flex-wrap">
                                        <div class="flex-1 -6">
                                            <div class="text-center">
                                                <i class="fas fa-server fa-2x text-green-600"></i>
                                                <p class="mt-2">{{ __('System Online') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex-1 -6">
                                            <div class="text-center">
                                                <i class="fas fa-database fa-2x text-green-600"></i>
                                                <p class="mt-2">{{ __('Database Connected') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 