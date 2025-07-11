@extends('layouts.app')

@section('title', __('errors.page_not_found'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <!-- 404 Illustration -->
            <div class="mx-auto w-64 h-64 mb-8">
                <svg viewBox="0 0 400 300" class="w-full h-full text-blue-600 dark:text-blue-400">
                    <!-- Background -->
                    <rect width="400" height="300" fill="currentColor" opacity="0.1" rx="20"/>
                    
                    <!-- 404 Text -->
                    <text x="200" y="120" text-anchor="middle" class="fill-current text-6xl font-bold" opacity="0.8">404</text>
                    
                    <!-- Magnifying Glass -->
                    <circle cx="150" cy="180" r="25" fill="none" stroke="currentColor" stroke-width="4"/>
                    <line x1="170" y1="200" x2="190" y2="220" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                    
                    <!-- Question Mark -->
                    <text x="280" y="190" text-anchor="middle" class="fill-current text-3xl font-bold" opacity="0.6">?</text>
                    
                    <!-- Decorative Elements -->
                    <circle cx="80" cy="80" r="4" fill="currentColor" opacity="0.4"/>
                    <circle cx="320" cy="60" r="6" fill="currentColor" opacity="0.3"/>
                    <circle cx="350" cy="250" r="5" fill="currentColor" opacity="0.4"/>
                    <circle cx="50" cy="220" r="3" fill="currentColor" opacity="0.5"/>
                </svg>
            </div>

            <!-- Error Message -->
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('errors.page_not_found') }}
            </h1>
            
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                {{ __('errors.page_not_found_description') }}
            </p>

            <!-- Search Bar -->
            <div class="mb-8">
                <form action="{{ route('search') }}" method="GET" class="max-w-md mx-auto">
                    <div class="relative">
                        <x-ui.input
                            type="text"
                            name="q"
                            id="site-search"
                            placeholder="{{ __('errors.search_placeholder') }}"
                            icon="magnifying-glass"
                            class="pr-20"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <x-ui.button 
                                type="submit" 
                                variant="primary" 
                                size="sm"
                            >
                                {{ __('errors.search') }}
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center mb-12">
                <x-ui.button 
                    href="{{ route('home') }}" 
                    variant="primary"
                    icon="home"
                >
                    {{ __('errors.go_home') }}
                </x-ui.button>

                <x-ui.button 
                    data-action="go-back"
                    variant="secondary"
                    icon="arrow-left"
                >
                    {{ __('errors.go_back') }}
                </x-ui.button>

                <x-ui.button 
                    href="{{ route('help.index') }}" 
                    variant="ghost"
                    icon="question-mark-circle"
                >
                    {{ __('errors.get_help') }}
                </x-ui.button>
            </div>

            <!-- Popular Pages -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 max-w-2xl mx-auto">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('errors.popular_pages') }}
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('jobs.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <x-icon name="briefcase" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.browse_jobs') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('errors.find_opportunities') }}</p>
                        </div>
                    </a>

                    <a href="{{ route('companies.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                <x-icon name="building-office" class="h-4 w-4 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.explore_companies') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('errors.discover_employers') }}</p>
                        </div>
                    </a>

                    <a href="{{ route('help.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                <x-icon name="question-mark-circle" class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.help_center') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('errors.find_answers') }}</p>
                        </div>
                    </a>

                    <a href="{{ route('contact') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                <x-icon name="envelope" class="h-4 w-4 text-yellow-600 dark:text-yellow-400" />
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.contact_us') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('errors.get_support') }}</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="mt-12 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ __('errors.still_need_help') }}
                </p>
                
                <div class="space-y-2 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                    <x-ui.button 
                        href="{{ route('help.index') }}" 
                        variant="ghost" 
                        size="sm"
                        icon="question-mark-circle"
                    >
                        {{ __('errors.visit_help_center') }}
                    </x-ui.button>

                    <x-ui.button 
                        href="mailto:support@jobportal.com" 
                        variant="ghost" 
                        size="sm"
                        icon="envelope"
                    >
                        {{ __('errors.contact_support') }}
                    </x-ui.button>
                </div>
            </div>

            <!-- Error Details (for debugging in development) -->
            @if(config('app.debug') && isset($exception))
                <div class="mt-12 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 max-w-2xl mx-auto">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                        {{ __('errors.debug_information') }}
                    </h3>
                    <div class="text-xs text-red-700 dark:text-red-300 space-y-1">
                        <p><strong>{{ __('errors.requested_url') }}:</strong> {{ request()->fullUrl() }}</p>
                        <p><strong>{{ __('errors.referrer') }}:</strong> {{ request()->header('referer', __('errors.none')) }}</p>
                        <p><strong>{{ __('errors.user_agent') }}:</strong> {{ request()->header('user-agent') }}</p>
                        @if(isset($exception))
                            <p><strong>{{ __('errors.exception') }}:</strong> {{ get_class($exception) }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Floating Action Button for Quick Help -->
<div class="fixed bottom-6 right-6 z-50">
    <button 
        data-action="open-quick-help" 
        class="w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 hover:scale-105"
        title="{{ __('errors.quick_help') }}"
    >
        <x-icon name="question-mark-circle" class="h-6 w-6" />
    </button>
</div>

<!-- Quick Help Modal -->
<x-ui.modal id="quick-help-modal" size="md">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            {{ __('errors.quick_help') }}
        </h3>
        
        <div class="space-y-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 dark:text-blue-200 mb-2">
                    {{ __('errors.what_happened') }}
                </h4>
                <p class="text-sm text-blue-800 dark:text-blue-300">
                    {{ __('errors.page_not_found_explanation') }}
                </p>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <h4 class="font-medium text-green-900 dark:text-green-200 mb-2">
                    {{ __('errors.what_can_you_do') }}
                </h4>
                <ul class="text-sm text-green-800 dark:text-green-300 space-y-1">
                    <li>• {{ __('errors.check_url_spelling') }}</li>
                    <li>• {{ __('errors.use_search_function') }}</li>
                    <li>• {{ __('errors.visit_homepage') }}</li>
                    <li>• {{ __('errors.contact_if_persistent') }}</li>
                </ul>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <h4 class="font-medium text-yellow-900 dark:text-yellow-200 mb-2">
                    {{ __('errors.report_broken_link') }}
                </h4>
                <p class="text-sm text-yellow-800 dark:text-yellow-300 mb-3">
                    {{ __('errors.help_us_improve') }}
                </p>
                <x-ui.button 
                    data-action="report-broken-link" 
                    variant="secondary" 
                    size="sm"
                >
                    {{ __('errors.report_link') }}
                </x-ui.button>
            </div>
        </div>
        
        <div class="flex justify-end mt-6">
            <x-ui.button 
                data-action="close-modal" 
                variant="primary"
            >
                {{ __('errors.got_it') }}
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>
@endsection

@push('scripts')
<script src="{{ asset('js/404.js') }}"></script>
@endpush 