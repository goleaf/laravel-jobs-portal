@extends('layouts.app')

@section('title')
    {{ __('Admin Dashboard') }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Admin Dashboard') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Statistics Cards -->
                        <div class="col-md-3 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
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
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
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
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
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
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
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
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>{{ __('Quick Actions') }}</h4>
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-users"></i><br>
                                        {{ __('Manage Candidates') }}
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-success w-100">
                                        <i class="fas fa-briefcase"></i><br>
                                        {{ __('Manage Jobs') }}
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="{{ route('company.index') }}" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-building"></i><br>
                                        {{ __('Manage Companies') }}
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-info w-100">
                                        <i class="fas fa-money-bill"></i><br>
                                        {{ __('Transactions') }}
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-cog"></i><br>
                                        {{ __('Settings') }}
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="{{ route('admin.subscribers.index') }}" class="btn btn-outline-dark w-100">
                                        <i class="fas fa-envelope"></i><br>
                                        {{ __('Subscribers') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Recent Registrations') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                                        <div class="list-group-item">
                                            <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('System Status') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <i class="fas fa-server fa-2x text-success"></i>
                                                <p class="mt-2">{{ __('System Online') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <i class="fas fa-database fa-2x text-success"></i>
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