@extends('candidate.layouts.app')

@section('title', __('messages.applications'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        {{ __('messages.my_applications')  }}
    </h1>
    
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('messages.no_applications')  }}
        </p>
    </div>
</div>
@endsection
