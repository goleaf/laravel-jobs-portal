@extends('layouts.app')

@section('title', __('errors.maintenance_mode'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-lg">
        <div class="text-center">
            <!-- Maintenance Illustration -->
            <div class="mx-auto w-64 h-64 mb-8">
                <svg viewBox="0 0 400 300" class="w-full h-full text-blue-600 dark:text-blue-400">
                    <!-- Background -->
                    <rect width="400" height="300" fill="currentColor" opacity="0.1" rx="20"/>
                    
                    <!-- Gear 1 -->
                    <g transform="translate(150,120)">
                        <circle r="30" fill="none" stroke="currentColor" stroke-width="4"/>
                        <circle r="20" fill="currentColor" opacity="0.3"/>
                        <g class="animate-spin-slow" style="transform-origin: center;">
                            <rect x="-3" y="-35" width="6" height="10" fill="currentColor"/>
                            <rect x="-3" y="25" width="6" height="10" fill="currentColor"/>
                            <rect x="-35" y="-3" width="10" height="6" fill="currentColor"/>
                            <rect x="25" y="-3" width="10" height="6" fill="currentColor"/>
                            <rect x="-25" y="-25" width="8" height="8" fill="currentColor" transform="rotate(45)"/>
                            <rect x="17" y="-25" width="8" height="8" fill="currentColor" transform="rotate(45)"/>
                            <rect x="-25" y="17" width="8" height="8" fill="currentColor" transform="rotate(45)"/>
                            <rect x="17" y="17" width="8" height="8" fill="currentColor" transform="rotate(45)"/>
                        </g>
                    </g>
                    
                    <!-- Gear 2 -->
                    <g transform="translate(250,160)">
                        <circle r="25" fill="none" stroke="currentColor" stroke-width="4"/>
                        <circle r="15" fill="currentColor" opacity="0.3"/>
                        <g class="animate-spin-reverse" style="transform-origin: center;">
                            <rect x="-2" y="-30" width="4" height="8" fill="currentColor"/>
                            <rect x="-2" y="22" width="4" height="8" fill="currentColor"/>
                            <rect x="-30" y="-2" width="8" height="4" fill="currentColor"/>
                            <rect x="22" y="-2" width="8" height="4" fill="currentColor"/>
                            <rect x="-21" y="-21" width="6" height="6" fill="currentColor" transform="rotate(45)"/>
                            <rect x="15" y="-21" width="6" height="6" fill="currentColor" transform="rotate(45)"/>
                            <rect x="-21" y="15" width="6" height="6" fill="currentColor" transform="rotate(45)"/>
                            <rect x="15" y="15" width="6" height="6" fill="currentColor" transform="rotate(45)"/>
                        </g>
                    </g>
                    
                    <!-- Tools -->
                    <g transform="translate(100,200)">
                        <rect x="0" y="0" width="4" height="40" fill="currentColor" rx="2"/>
                        <rect x="-8" y="35" width="20" height="8" fill="currentColor" rx="4"/>
                    </g>
                    
                    <g transform="translate(300,200)">
                        <rect x="0" y="0" width="4" height="35" fill="currentColor" rx="2"/>
                        <circle cx="2" cy="40" r="6" fill="none" stroke="currentColor" stroke-width="2"/>
                    </g>
                    
                    <!-- Progress Bar -->
                    <rect x="100" y="250" width="200" height="8" fill="currentColor" opacity="0.2" rx="4"/>
                    <rect x="100" y="250" width="120" height="8" fill="currentColor" opacity="0.6" rx="4" id="progress-bar"/>
                </svg>
            </div>

            <!-- Maintenance Message -->
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('errors.under_maintenance') }}
            </h1>
            
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                {{ __('errors.maintenance_description') }}
            </p>

            <!-- Countdown Timer -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('errors.estimated_completion') }}
                </h2>
                
                <div class="grid grid-cols-4 gap-4 mb-4" id="countdown-timer">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="days">00</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('errors.days') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="hours">00</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('errors.hours') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="minutes">00</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('errors.minutes') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="seconds">00</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('errors.seconds') }}</div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-1000" style="width: 60%" id="maintenance-progress"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                    {{ __('errors.maintenance_progress') }}: <span id="progress-percentage">60%</span>
                </p>
            </div>

            <!-- What's Being Updated -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('errors.whats_being_updated') }}
                </h2>
                
                <div class="space-y-3 text-left">
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <x-icon name="check" class="h-3 w-3 text-green-600 dark:text-green-400" />
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.database_optimization') }}</span>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <x-icon name="check" class="h-3 w-3 text-green-600 dark:text-green-400" />
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.security_updates') }}</span>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></div>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.performance_improvements') }}</span>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.new_features') }}</span>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.system_testing') }}</span>
                    </div>
                </div>
            </div>

            <!-- Stay Updated -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-white mb-4">
                    {{ __('errors.stay_updated') }}
                </h2>
                
                <p class="text-blue-100 mb-6">
                    {{ __('errors.get_notified_description') }}
                </p>
                
                <form data-subscribe-form class="flex flex-col sm:flex-row gap-3">
                    <x-ui.input
                        type="email"
                        id="notification-email"
                        placeholder="{{ __('errors.enter_email') }}"
                        class="flex-1 bg-white"
                        required
                    />
                    <x-ui.button 
                        type="submit" 
                        variant="secondary"
                        class="bg-white text-blue-600 hover:bg-gray-50"
                    >
                        {{ __('errors.notify_me') }}
                    </x-ui.button>
                </form>
                
                <p class="text-xs text-blue-200 mt-3">
                    {{ __('errors.notification_privacy') }}
                </p>
            </div>

            <!-- Alternative Access -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('errors.need_immediate_access') }}
                </h2>
                
                <div class="space-y-3">
                    <a href="mailto:support@jobportal.com" class="flex items-center justify-center space-x-2 p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <x-icon name="envelope" class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.contact_support') }}</span>
                    </a>
                    
                    <a href="tel:+1-555-123-4567" class="flex items-center justify-center space-x-2 p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <x-icon name="phone" class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.emergency_hotline') }}</span>
                    </a>
                    
                    <a href="https://status.jobportal.com" target="_blank" class="flex items-center justify-center space-x-2 p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <x-icon name="globe-alt" class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.status_page') }}</span>
                    </a>
                </div>
            </div>

            <!-- Social Media Updates -->
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ __('errors.follow_updates') }}
                </p>
                
                <div class="flex justify-center space-x-4">
                    <a href="https://twitter.com/jobportal" target="_blank" class="w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center transition-colors">
                        <x-icon name="globe-alt" class="h-5 w-5" />
                    </a>
                    
                    <a href="https://facebook.com/jobportal" target="_blank" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition-colors">
                        <x-icon name="globe-alt" class="h-5 w-5" />
                    </a>
                    
                    <a href="https://linkedin.com/company/jobportal" target="_blank" class="w-10 h-10 bg-blue-700 hover:bg-blue-800 text-white rounded-full flex items-center justify-center transition-colors">
                        <x-icon name="globe-alt" class="h-5 w-5" />
                    </a>
                </div>
            </div>

            <!-- Maintenance Details (for admins) -->
            @if(auth()->check() && auth()->user()->hasRole('admin'))
                <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                        {{ __('errors.admin_information') }}
                    </h3>
                    <div class="text-xs text-yellow-700 dark:text-yellow-300 space-y-1">
                        <p><strong>{{ __('errors.maintenance_started') }}:</strong> {{ $maintenanceStart ?? now()->subHours(2)->format('M j, Y \a\t g:i A') }}</p>
                        <p><strong>{{ __('errors.estimated_end') }}:</strong> {{ $maintenanceEnd ?? now()->addHours(2)->format('M j, Y \a\t g:i A') }}</p>
                        <p><strong>{{ __('errors.maintenance_type') }}:</strong> {{ $maintenanceType ?? __('errors.scheduled_maintenance') }}</p>
                        <p><strong>{{ __('errors.affected_services') }}:</strong> {{ $affectedServices ?? __('errors.all_services') }}</p>
                    </div>
                    
                    <div class="mt-3 flex space-x-2">
                        <x-ui.button 
                            data-check-status-button 
                            variant="secondary" 
                            size="sm"
                        >
                            {{ __('errors.check_status') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            href="{{ route('admin.maintenance') }}" 
                            variant="ghost" 
                            size="sm"
                        >
                            {{ __('errors.maintenance_panel') }}
                        </x-ui.button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Success Modal for Email Subscription -->
<x-ui.modal id="subscription-success-modal" size="sm">
    <div class="p-6 text-center">
        <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-4">
            <x-icon name="check" class="h-8 w-8 text-green-600 dark:text-green-400" />
        </div>
        
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            {{ __('errors.subscription_confirmed') }}
        </h3>
        
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            {{ __('errors.notification_confirmation') }}
        </p>
        
        <x-ui.button 
            data-close-modal="subscription-success-modal" 
            variant="primary"
        >
            {{ __('errors.got_it') }}
        </x-ui.button>
    </div>
</x-ui.modal>
@endsection

@push('styles')
<style>
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes spin-reverse {
    from { transform: rotate(360deg); }
    to { transform: rotate(0deg); }
}

.animate-spin-slow {
    animation: spin-slow 4s linear infinite;
}

.animate-spin-reverse {
    animation: spin-reverse 3s linear infinite;
}
</style>
@endpush

@push('scripts')
{{-- Logic is bundled via resources/js/app.js (pages/maintenance.js) --}}
@endpush