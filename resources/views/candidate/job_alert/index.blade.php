@extends('candidate.layouts.app')
@section('title')
    {{ __('Job Alerts') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Job Alerts') }}</h3>
            <div class="card-toolbar">
                <a href="{{ route('candidate.job-alerts.create') }}" class="btn btn-primary">
                    {{ __('Create Alert') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <p>{{ __('Manage your job alerts here.') }}</p>
            <!-- Job alerts content would go here -->
        </div>
    </div>
@endsection 