@extends('layouts.app')

@section('title')
    {{ __('messages.dashboard') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="h3 mb-0">{{ __('messages.dashboard') }}</h1>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">{{ __('messages.total_users') }}</h5>
                                <h2 class="mb-0">{{ $stats['total_users'] ?? 0 }}</h2>
                            </div>
                            <div class="align-self-center">
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
                                <h5 class="card-title">{{ __('messages.total_jobs') }}</h5>
                                <h2 class="mb-0">{{ $stats['total_jobs'] ?? 0 }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-briefcase fa-2x"></i>
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
                                <h5 class="card-title">{{ __('messages.total_companies') }}</h5>
                                <h2 class="mb-0">{{ $stats['total_companies'] ?? 0 }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-building fa-2x"></i>
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
                                <h5 class="card-title">{{ __('messages.total_candidates') }}</h5>
                                <h2 class="mb-0">{{ $stats['total_candidates'] ?? 0 }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user-graduate fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.active_jobs') }}</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-success">{{ $stats['active_jobs'] ?? 0 }}</h3>
                        <p class="text-muted">{{ __('messages.jobs_currently_open') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.pending_jobs') }}</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-warning">{{ $stats['pending_jobs'] ?? 0 }}</h3>
                        <p class="text-muted">{{ __('messages.jobs_awaiting_approval') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 