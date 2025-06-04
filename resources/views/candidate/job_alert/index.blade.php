@extends('candidate.layouts.app')
@section('title')
    {{ __('Job Alerts') }}
@endsection

@section('content')
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-white shadow rounded-lg overflow-hidden -header">
            <h3 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('Job Alerts') }}</h3>
            <div class="bg-white shadow rounded-lg overflow-hidden -toolbar">
                <a href="{{ route('candidate.job.alerts.create') }}" class="btn px-4 py-2 rounded font-medium transition-colors -primary">
                    {{ __('Create Alert') }}
                </a>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden -body">
            <p>{{ __('Manage your job alerts here.') }}</p>
            <!-- Job alerts content would go here -->
        </div>
    </div>
@endsection 