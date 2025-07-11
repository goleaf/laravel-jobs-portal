@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <div class="min-h-screen bg-gray-100 p-6">
        {{-- Dashboard Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900">{{ __('Welcome!') }}</h1>
            <x-button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                {{ __('Post a New Job') }}
            </x-button>
        </div>

        {{-- Summary Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <x-card class="p-4">
                <h2 class="text-sm text-gray-500">{{ __('Total Applications') }}</h2>
                <p class="text-4xl font-bold text-gray-900">1,234</p>
            </x-card>
            <x-card class="p-4">
                <h2 class="text-sm text-gray-500">{{ __('Job Views') }}</h2>
                <p class="text-4xl font-bold text-gray-900">5,678</p>
            </x-card>
            <x-card class="p-4">
                <h2 class="text-sm text-gray-500">{{ __('New Messages') }}</h2>
                <p class="text-4xl font-bold text-gray-900">12</p>
            </x-card>
            <x-card class="p-4">
                <h2 class="text-sm text-gray-500">{{ __('Hired Candidates') }}</h2>
                <p class="text-4xl font-bold text-gray-900">56</p>
            </x-card>
        </div>

        {{-- Recent Activity/Notifications Feed --}}
        <x-card class="p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Recent Activity') }}</h2>
            <ul>
                <li class="py-3 border-b border-gray-200 last:border-b-0">
                    <p class="text-gray-700">{{ __('New application for Senior Developer position.') }}</p>
                    <span class="text-sm text-gray-500">{{ __('2 hours ago') }}</span>
                </li>
                <li class="py-3 border-b border-gray-200 last:border-b-0">
                    <p class="text-gray-700">{{ __('Your job post for Marketing Manager is live.') }}</p>
                    <span class="text-sm text-gray-500">{{ __('1 day ago') }}</span>
                </li>
                <li class="py-3 border-b border-gray-200 last:border-b-0">
                    <p class="text-gray-700">{{ __('Candidate John Doe updated their profile.') }}</p>
                    <span class="text-sm text-gray-500">{{ __('3 days ago') }}</span>
                </li>
            </ul>
        </x-card>

        {{-- Quick Access/Featured Section (Examples) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-card class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Recommended Jobs') }}</h2>
                <ul>
                    <li class="mb-2"><a href="#" class="text-blue-600 hover:underline">{{ __('Frontend Developer - Remote') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-blue-600 hover:underline">{{ __('UI/UX Designer - New York') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-blue-600 hover:underline">{{ __('Data Scientist - London') }}</a></li>
                </ul>
            </x-card>
            <x-card class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Career Resources') }}</h2>
                <ul>
                    <li class="mb-2"><a href="#" class="text-blue-600 hover:underline">{{ __('How to Write a Perfect Resume') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-blue-600 hover:underline">{{ __('Interview Preparation Guide') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-blue-600 hover:underline">{{ __('Job Search Strategies 2024') }}</a></li>
                </ul>
            </x-card>
        </div>
    </div>
@endsection
