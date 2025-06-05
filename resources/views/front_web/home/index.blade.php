@extends('front_web.layouts.app')

@section('title', __('messages.home'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                {{ __('messages.welcome') }}
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-500 dark:text-gray-400 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                {{ __('messages.home_description') }}
            </p>
        </div>
    </div>
</div>
@endsection
