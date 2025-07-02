@extends('layouts.app')

@section('title', __('settings.settings'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('settings.settings') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('settings.manage_preferences') }}
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Settings Navigation -->
            <div class="lg:w-1/4">
                <nav class="space-y-1">
                    <button 
                        onclick="showSettingsTab('profile')" 
                        class="settings-tab active w-full text-left px-3 py-2 text-sm font-medium rounded-md"
                        data-tab="profile"
                    >
                        <div class="flex items-center">
                            <x-icon name="user" class="h-5 w-5 mr-3" />
                            {{ __('settings.profile_settings') }}
                        </div>
                    </button>

                    <button 
                        onclick="showSettingsTab('notifications')" 
                        class="settings-tab w-full text-left px-3 py-2 text-sm font-medium rounded-md"
                        data-tab="notifications"
                    >
                        <div class="flex items-center">
                            <x-icon name="bell" class="h-5 w-5 mr-3" />
                            {{ __('settings.notification_preferences') }}
                        </div>
                    </button>

                    <button 
                        onclick="showSettingsTab('privacy')" 
                        class="settings-tab w-full text-left px-3 py-2 text-sm font-medium rounded-md"
                        data-tab="privacy"
                    >
                        <div class="flex items-center">
                            <x-icon name="shield-check" class="h-5 w-5 mr-3" />
                            {{ __('settings.privacy_security') }}
                        </div>
                    </button>

                    <button 
                        onclick="showSettingsTab('appearance')" 
                        class="settings-tab w-full text-left px-3 py-2 text-sm font-medium rounded-md"
                        data-tab="appearance"
                    >
                        <div class="flex items-center">
                            <x-icon name="paint-brush" class="h-5 w-5 mr-3" />
                            {{ __('settings.appearance') }}
                        </div>
                    </button>

                    <button 
                        onclick="showSettingsTab('integrations')" 
                        class="settings-tab w-full text-left px-3 py-2 text-sm font-medium rounded-md"
                        data-tab="integrations"
                    >
                        <div class="flex items-center">
                            <x-icon name="puzzle-piece" class="h-5 w-5 mr-3" />
                            {{ __('settings.integrations') }}
                        </div>
                    </button>

                    @can('admin')
                        <button 
                            onclick="showSettingsTab('system')" 
                            class="settings-tab w-full text-left px-3 py-2 text-sm font-medium rounded-md"
                            data-tab="system"
                        >
                            <div class="flex items-center">
                                <x-icon name="cog-6-tooth" class="h-5 w-5 mr-3" />
                                {{ __('settings.system_settings') }}
                            </div>
                        </button>
                    @endcan
                </nav>
            </div>

            <!-- Settings Content -->
            <div class="lg:w-3/4">
                <!-- Profile Settings -->
                <div id="profile-settings" class="settings-content">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('settings.profile_information') }}
                            </h3>
                        </div>
                        
                        <form onsubmit="updateProfile(event)" class="p-6 space-y-6">
                            <!-- Avatar Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('settings.profile_photo') }}
                                </label>
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        @if(auth()->user()->avatar)
                                            <img class="h-16 w-16 rounded-full object-cover" src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">
                                        @else
                                            <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-xl font-medium text-gray-700 dark:text-gray-300">
                                                    {{ substr(auth()->user()->name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file" id="avatar-upload" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                                        <x-ui.button 
                                            type="button"
                                            onclick="document.getElementById('avatar-upload').click()" 
                                            variant="secondary"
                                            size="sm"
                                        >
                                            {{ __('settings.change_photo') }}
                                        </x-ui.button>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('settings.photo_requirements') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('settings.first_name') }}
                                    </label>
                                    <x-ui.input
                                        type="text"
                                        name="first_name"
                                        value="{{ auth()->user()->first_name ?? '' }}"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('settings.last_name') }}
                                    </label>
                                    <x-ui.input
                                        type="text"
                                        name="last_name"
                                        value="{{ auth()->user()->last_name ?? '' }}"
                                        required
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('settings.email_address') }}
                                </label>
                                <x-ui.input
                                    type="email"
                                    name="email"
                                    value="{{ auth()->user()->email }}"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('settings.phone_number') }}
                                </label>
                                <x-ui.input
                                    type="tel"
                                    name="phone"
                                    value="{{ auth()->user()->phone ?? '' }}"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('settings.bio') }}
                                </label>
                                <x-ui.textarea
                                    name="bio"
                                    rows="4"
                                    placeholder="{{ __('settings.tell_about_yourself') }}"
                                >{{ auth()->user()->bio ?? '' }}</x-ui.textarea>
                            </div>

                            <div class="flex justify-end">
                                <x-ui.button type="submit" variant="primary">
                                    {{ __('settings.save_changes') }}
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div id="notifications-settings" class="settings-content hidden">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('settings.notification_preferences') }}
                            </h3>
                        </div>
                        
                        <form onsubmit="updateNotificationSettings(event)" class="p-6 space-y-6">
                            <!-- Email Notifications -->
                            <div>
                                <h4 class="text-base font-medium text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.email_notifications') }}
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.job_alerts') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.job_alerts_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="email_job_alerts" 
                                            value="1"
                                            {{ (auth()->user()->settings['email_job_alerts'] ?? true) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.application_updates') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.application_updates_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="email_application_updates" 
                                            value="1"
                                            {{ (auth()->user()->settings['email_application_updates'] ?? true) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.marketing_emails') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.marketing_emails_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="email_marketing" 
                                            value="1"
                                            {{ (auth()->user()->settings['email_marketing'] ?? false) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Push Notifications -->
                            <div>
                                <h4 class="text-base font-medium text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.push_notifications') }}
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.browser_notifications') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.browser_notifications_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="push_browser" 
                                            value="1"
                                            {{ (auth()->user()->settings['push_browser'] ?? true) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.mobile_notifications') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.mobile_notifications_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="push_mobile" 
                                            value="1"
                                            {{ (auth()->user()->settings['push_mobile'] ?? true) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Frequency -->
                            <div>
                                <h4 class="text-base font-medium text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.notification_frequency') }}
                                </h4>
                                <x-ui.select
                                    name="notification_frequency"
                                    :options="[
                                        'instant' => __('settings.instant'),
                                        'daily' => __('settings.daily_digest'),
                                        'weekly' => __('settings.weekly_digest'),
                                        'never' => __('settings.never')
                                    ]"
                                    :selected="auth()->user()->settings['notification_frequency'] ?? 'daily'"
                                />
                            </div>

                            <div class="flex justify-end">
                                <x-ui.button type="submit" variant="primary">
                                    {{ __('settings.save_preferences') }}
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Privacy & Security Settings -->
                <div id="privacy-settings" class="settings-content hidden">
                    <div class="space-y-6">
                        <!-- Password Change -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ __('settings.change_password') }}
                                </h3>
                            </div>
                            
                            <form onsubmit="changePassword(event)" class="p-6 space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('settings.current_password') }}
                                    </label>
                                    <x-ui.input
                                        type="password"
                                        name="current_password"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('settings.new_password') }}
                                    </label>
                                    <x-ui.input
                                        type="password"
                                        name="new_password"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('settings.confirm_password') }}
                                    </label>
                                    <x-ui.input
                                        type="password"
                                        name="confirm_password"
                                        required
                                    />
                                </div>

                                <div class="flex justify-end">
                                    <x-ui.button type="submit" variant="primary">
                                        {{ __('settings.update_password') }}
                                    </x-ui.button>
                                </div>
                            </form>
                        </div>

                        <!-- Two-Factor Authentication -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ __('settings.two_factor_authentication') }}
                                </h3>
                            </div>
                            
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ __('settings.enable_2fa') }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('settings.2fa_description') }}
                                        </p>
                                    </div>
                                    <x-ui.button 
                                        onclick="toggle2FA()" 
                                        variant="{{ auth()->user()->two_factor_enabled ? 'secondary' : 'primary' }}"
                                    >
                                        {{ auth()->user()->two_factor_enabled ? __('settings.disable') : __('settings.enable') }}
                                    </x-ui.button>
                                </div>
                            </div>
                        </div>

                        <!-- Privacy Settings -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ __('settings.privacy_settings') }}
                                </h3>
                            </div>
                            
                            <form onsubmit="updatePrivacySettings(event)" class="p-6 space-y-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.profile_visibility') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.profile_visibility_description') }}
                                            </p>
                                        </div>
                                        <x-ui.select
                                            name="profile_visibility"
                                            :options="[
                                                'public' => __('settings.public'),
                                                'private' => __('settings.private'),
                                                'contacts_only' => __('settings.contacts_only')
                                            ]"
                                            :selected="auth()->user()->settings['profile_visibility'] ?? 'public'"
                                            class="w-40"
                                        />
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.show_online_status') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.online_status_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="show_online_status" 
                                            value="1"
                                            {{ (auth()->user()->settings['show_online_status'] ?? true) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.allow_search_engines') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.search_engines_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="allow_search_engines" 
                                            value="1"
                                            {{ (auth()->user()->settings['allow_search_engines'] ?? true) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <x-ui.button type="submit" variant="primary">
                                        {{ __('settings.save_privacy_settings') }}
                                    </x-ui.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div id="appearance-settings" class="settings-content hidden">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('settings.appearance_preferences') }}
                            </h3>
                        </div>
                        
                        <form onsubmit="updateAppearanceSettings(event)" class="p-6 space-y-6">
                            <!-- Theme Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                    {{ __('settings.theme') }}
                                </label>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="relative">
                                        <input type="radio" name="theme" value="light" id="theme-light" class="sr-only">
                                        <label for="theme-light" class="flex flex-col items-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-500">
                                            <div class="w-16 h-12 bg-white border border-gray-300 rounded mb-2"></div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('settings.light') }}</span>
                                        </label>
                                    </div>

                                    <div class="relative">
                                        <input type="radio" name="theme" value="dark" id="theme-dark" class="sr-only">
                                        <label for="theme-dark" class="flex flex-col items-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-500">
                                            <div class="w-16 h-12 bg-gray-800 border border-gray-600 rounded mb-2"></div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('settings.dark') }}</span>
                                        </label>
                                    </div>

                                    <div class="relative">
                                        <input type="radio" name="theme" value="system" id="theme-system" class="sr-only" checked>
                                        <label for="theme-system" class="flex flex-col items-center p-4 border-2 border-blue-500 rounded-lg cursor-pointer">
                                            <div class="w-16 h-12 bg-gradient-to-r from-white to-gray-800 border border-gray-400 rounded mb-2"></div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('settings.system') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('settings.language') }}
                                </label>
                                <x-ui.select
                                    name="language"
                                    :options="[
                                        'en' => 'English',
                                        'ar' => 'العربية',
                                        'de' => 'Deutsch',
                                        'es' => 'Español',
                                        'fr' => 'Français',
                                        'pt' => 'Português',
                                        'ru' => 'Русский',
                                        'tr' => 'Türkçe',
                                        'zh' => '中文'
                                    ]"
                                    :selected="auth()->user()->settings['language'] ?? 'en'"
                                />
                            </div>

                            <!-- Timezone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('settings.timezone') }}
                                </label>
                                <x-ui.select
                                    name="timezone"
                                    :options="$timezones ?? []"
                                    :selected="auth()->user()->settings['timezone'] ?? 'UTC'"
                                    searchable="true"
                                />
                            </div>

                            <!-- Date Format -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('settings.date_format') }}
                                </label>
                                <x-ui.select
                                    name="date_format"
                                    :options="[
                                        'Y-m-d' => '2024-01-15',
                                        'm/d/Y' => '01/15/2024',
                                        'd/m/Y' => '15/01/2024',
                                        'F j, Y' => 'January 15, 2024',
                                        'j F Y' => '15 January 2024'
                                    ]"
                                    :selected="auth()->user()->settings['date_format'] ?? 'Y-m-d'"
                                />
                            </div>

                            <div class="flex justify-end">
                                <x-ui.button type="submit" variant="primary">
                                    {{ __('settings.save_appearance') }}
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Integrations Settings -->
                <div id="integrations-settings" class="settings-content hidden">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('settings.connected_accounts') }}
                            </h3>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Social Media Integrations -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                            <x-icon name="globe-alt" class="h-6 w-6 text-white" />
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('social.linkedin') }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('settings.linkedin_description') }}</p>
                                        </div>
                                    </div>
                                    <x-ui.button 
                                        onclick="connectLinkedIn()" 
                                        variant="secondary"
                                        size="sm"
                                    >
                                        {{ __('settings.connect') }}
                                    </x-ui.button>
                                </div>

                                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                                            <x-icon name="globe-alt" class="h-6 w-6 text-white" />
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('social.github') }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('settings.github_description') }}</p>
                                        </div>
                                    </div>
                                    <x-ui.button 
                                        onclick="connectGitHub()" 
                                        variant="secondary"
                                        size="sm"
                                    >
                                        {{ __('settings.connect') }}
                                    </x-ui.button>
                                </div>
                            </div>

                            <!-- API Keys -->
                            <div>
                                <h4 class="text-base font-medium text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.api_access') }}
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.api_key') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.api_key_description') }}
                                            </p>
                                        </div>
                                        <x-ui.button 
                                            onclick="generateAPIKey()" 
                                            variant="secondary"
                                            size="sm"
                                        >
                                            {{ __('settings.generate_key') }}
                                        </x-ui.button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Settings (Admin Only) -->
                @can('admin')
                    <div id="system-settings" class="settings-content hidden">
                        <div class="space-y-6">
                            <!-- Site Configuration -->
                            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ __('settings.site_configuration') }}
                                    </h3>
                                </div>
                                
                                <form onsubmit="updateSystemSettings(event)" class="p-6 space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('settings.site_name') }}
                                        </label>
                                        <x-ui.input
                                            type="text"
                                            name="site_name"
                                            value="{{ config('app.name') }}"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('settings.site_description') }}
                                        </label>
                                        <x-ui.textarea
                                            name="site_description"
                                            rows="3"
                                        >{{ config('app.description', '') }}</x-ui.textarea>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('settings.maintenance_mode') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('settings.maintenance_mode_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="maintenance_mode" 
                                            value="1"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                    </div>

                                    <div class="flex justify-end">
                                        <x-ui.button type="submit" variant="primary">
                                            {{ __('settings.save_system_settings') }}
                                        </x-ui.button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize settings
    initializeSettings();
});

function initializeSettings() {
    // Set active theme
    const savedTheme = localStorage.getItem('theme') || 'system';
    document.querySelector(`input[name="theme"][value="${savedTheme}"]`).checked = true;
    
    // Update theme selection UI
    updateThemeSelection();
}

function showSettingsTab(tabName) {
    // Hide all content
    document.querySelectorAll('.settings-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show selected content
    document.getElementById(`${tabName}-settings`).classList.remove('hidden');
    
    // Update navigation
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.classList.remove('active', 'bg-blue-100', 'text-blue-700', 'dark:bg-blue-900', 'dark:text-blue-300');
        tab.classList.add('text-gray-600', 'hover:text-gray-900', 'dark:text-gray-400', 'dark:hover:text-gray-300');
    });
    
    const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
    activeTab.classList.add('active', 'bg-blue-100', 'text-blue-700', 'dark:bg-blue-900', 'dark:text-blue-300');
    activeTab.classList.remove('text-gray-600', 'hover:text-gray-900', 'dark:text-gray-400', 'dark:hover:text-gray-300');
}

function updateProfile(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('/settings/profile', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('{{ __("settings.profile_updated") }}', 'success');
        } else {
            showNotification(data.message || '{{ __("settings.update_failed") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating profile:', error);
        showNotification('{{ __("settings.update_error") }}', 'error');
    });
}

function updateNotificationSettings(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('/settings/notifications', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('{{ __("settings.notifications_updated") }}', 'success');
        } else {
            showNotification(data.message || '{{ __("settings.update_failed") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating notifications:', error);
        showNotification('{{ __("settings.update_error") }}', 'error');
    });
}

function changePassword(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    // Validate password confirmation
    if (formData.get('new_password') !== formData.get('confirm_password')) {
        showNotification('{{ __("settings.passwords_dont_match") }}', 'error');
        return;
    }
    
    fetch('/settings/password', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('{{ __("settings.password_updated") }}', 'success');
            event.target.reset();
        } else {
            showNotification(data.message || '{{ __("settings.password_update_failed") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error changing password:', error);
        showNotification('{{ __("settings.password_update_error") }}', 'error');
    });
}

function updateAppearanceSettings(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const theme = formData.get('theme');
    
    // Apply theme immediately
    applyTheme(theme);
    localStorage.setItem('theme', theme);
    
    fetch('/settings/appearance', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('{{ __("settings.appearance_updated") }}', 'success');
        } else {
            showNotification(data.message || '{{ __("settings.update_failed") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating appearance:', error);
        showNotification('{{ __("settings.update_error") }}', 'error');
    });
}

function applyTheme(theme) {
    const html = document.documentElement;
    
    if (theme === 'dark') {
        html.classList.add('dark');
    } else if (theme === 'light') {
        html.classList.remove('dark');
    } else {
        // System theme
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    }
}

function updateThemeSelection() {
    document.querySelectorAll('input[name="theme"]').forEach(radio => {
        const label = radio.nextElementSibling;
        if (radio.checked) {
            label.classList.add('border-blue-500');
            label.classList.remove('border-gray-200', 'dark:border-gray-600');
        } else {
            label.classList.remove('border-blue-500');
            label.classList.add('border-gray-200', 'dark:border-gray-600');
        }
        
        radio.addEventListener('change', function() {
            updateThemeSelection();
        });
    });
}

function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = input.closest('form').querySelector('img');
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggle2FA() {
    // Implementation for 2FA toggle
    showNotification('{{ __("settings.2fa_feature_coming_soon") }}', 'info');
}

function connectLinkedIn() {
    // Implementation for LinkedIn connection
    window.open('/auth/linkedin', '_blank');
}

function connectGitHub() {
    // Implementation for GitHub connection
    window.open('/auth/github', '_blank');
}

function generateAPIKey() {
    fetch('/settings/api-key', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('{{ __("settings.api_key_generated") }}', 'success');
            // Show API key in modal or copy to clipboard
            navigator.clipboard.writeText(data.api_key);
        } else {
            showNotification(data.message || '{{ __("settings.api_key_failed") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error generating API key:', error);
        showNotification('{{ __("settings.api_key_error") }}', 'error');
    });
}

function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'system';
    applyTheme(savedTheme);
});
</script>
@endpush
