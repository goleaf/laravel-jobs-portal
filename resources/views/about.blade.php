@extends('layouts.app')

@section('title', __('about.page_title'))

@section('content')
<div class="container mx-auto px-6 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-8 text-center">{{ __('about.title') }}</h1>
        
        <div class="prose prose-lg max-w-none dark:prose-invert">
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 text-center">
                {{ __('about.welcome_message') }}
            </p>
            
            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('about.our_mission') }}</h2>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ __('about.mission_description') }}
                    </p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('about.our_vision') }}</h2>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ __('about.vision_description') }}
                    </p>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 p-8 rounded-lg">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('about.why_choose_platform') }}</h2>
                <ul class="grid md:grid-cols-2 gap-4 text-gray-600 dark:text-gray-300">
                    <li class="flex items-center">
                        <svg-icon name="checkmark" class="w-5 h-5 text-green-500 mr-3" />
                        {{ __('about.extensive_job_listings') }}
                    </li>
                    <li class="flex items-center">
                        <svg-icon name="checkmark" class="w-5 h-5 text-green-500 mr-3" />
                        {{ __('about.advanced_search_filters') }}
                    </li>
                    <li class="flex items-center">
                        <svg-icon name="checkmark" class="w-5 h-5 text-green-500 mr-3" />
                        {{ __('about.professional_networking') }}
                    </li>
                    <li class="flex items-center">
                        <svg-icon name="checkmark" class="w-5 h-5 text-green-500 mr-3" />
                        {{ __('about.secure_platform') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 