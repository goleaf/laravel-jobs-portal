@extends('candidate.layouts.app')
@section('title')
    {{ __('Job Alerts') }}
@endsection

@section('content')
    <div class="overflow-hidden shadow rounded bg-white -lg">
        <div class="overflow-hidden shadow rounded bg-white -lg header">
            <h3 class="overflow-hidden shadow rounded bg-white -lg title">{{ __('Job Alerts') }}</h3>
            <div class="overflow-hidden shadow rounded bg-white -lg toolbar">
                <a href="{{ route('candidate.dashboard') }}" class="border border-gray-300 bg-transparent">
                    {{ __('Create Alert') }}
                </a>
            </div>
        </div>
        <div class="overflow-hidden shadow rounded bg-white -lg body">
            <p>{{ __('Manage your job rounded-md p-4s here.') }}</p>
            <!-- Job rounded-md p-4s content would go here -->
        </div>
    </div>
@endsection 