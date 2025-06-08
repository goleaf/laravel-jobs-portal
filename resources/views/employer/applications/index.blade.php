@extends('employer.layouts.app')

@section('title', __('messages.applications'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark: text-white mb-6">
        {{ __('messages.job_applications') }}
    </h1>
    
    <div class="bg-white dark:bg-gray-800 shadow rounded -lg p-6">
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('messages.no_applications') }}
        </p>
    </div>
</div>
@endsection
