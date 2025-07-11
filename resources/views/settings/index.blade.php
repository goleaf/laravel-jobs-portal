@extends('layouts.app')

@section('title', __('settings.settings'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-10 text-center lg:text-left">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl leading-tight mb-3">
                {{ __('settings.settings') }}
            </h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400 max-w-3xl lg:mx-0 mx-auto">
                {{ __('settings.manage_preferences') }}
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Settings Navigation -->
            <div class="lg:w-1/4">
                <nav class="space-y-2 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                    <button 
                        onclick="showSettingsTab('profile')" 
                        class="settings-tab active w-full text-left px-4 py-3 text-base font-medium rounded-lg transition-all duration-200 flex items-center group {{ request('tab') == 'profile' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400' }}"
                        data-tab="profile"
                    >
                        <x-icon name="user" class="h-6 w-6 mr-3 transition-colors duration-200 {{ request('tab') == 'profile' ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" />
                        {{ __('settings.profile_settings') }}
                    </button>

                    <button 
                        onclick="showSettingsTab('notifications')" 
                        class="settings-tab w-full text-left px-4 py-3 text-base font-medium rounded-lg transition-all duration-200 flex items-center group {{ request('tab') == 'notifications' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400' }}"
                        data-tab="notifications"
                    >
                        <x-icon name="bell" class="h-6 w-6 mr-3 transition-colors duration-200 {{ request('tab') == 'notifications' ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" />
                        {{ __('settings.notification_preferences') }}
                    </button>

                    <button 
                        onclick="showSettingsTab('privacy')" 
                        class="settings-tab w-full text-left px-4 py-3 text-base font-medium rounded-lg transition-all duration-200 flex items-center group {{ request('tab') == 'privacy' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400' }}"
                        data-tab="privacy"
                    >
                        <x-icon name="shield-check" class="h-6 w-6 mr-3 transition-colors duration-200 {{ request('tab') == 'privacy' ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" />
                        {{ __('settings.privacy_security') }}
                    </button>

                    <button 
                        onclick="showSettingsTab('appearance')" 
                        class="settings-tab w-full text-left px-4 py-3 text-base font-medium rounded-lg transition-all duration-200 flex items-center group {{ request('tab') == 'appearance' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400' }}"
                        data-tab="appearance"
                    >
                        <x-icon name="paint-brush" class="h-6 w-6 mr-3 transition-colors duration-200 {{ request('tab') == 'appearance' ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" />
                        {{ __('settings.appearance') }}
                    </button>

                    <button 
                        onclick="showSettingsTab('integrations')" 
                        class="settings-tab w-full text-left px-4 py-3 text-base font-medium rounded-lg transition-all duration-200 flex items-center group {{ request('tab') == 'integrations' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400' }}"
                        data-tab="integrations"
                    >
                        <x-icon name="puzzle-piece" class="h-6 w-6 mr-3 transition-colors duration-200 {{ request('tab') == 'integrations' ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" />
                        {{ __('settings.integrations') }}
                    </button>

                    @can('admin')
                        <button 
                            onclick="showSettingsTab('system')" 
                            class="settings-tab w-full text-left px-4 py-3 text-base font-medium rounded-lg transition-all duration-200 flex items-center group {{ request('tab') == 'system' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400' }}"
                            data-tab="system"
                        >
                            <x-icon name="cog-6-tooth" class="h-6 w-6 mr-3 transition-colors duration-200 {{ request('tab') == 'system' ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" />
                            {{ __('settings.system_settings') }}
                        </button>
                    @endcan
                </nav>
            </div>

            <!-- Settings Content -->
            <div class="lg:w-3/4">
                <!-- Profile Settings -->
                <div id="profile-settings" class="settings-content">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white leading-tight">
                                {{ __('settings.profile_information') }}
                            </h3>
                        </div>
                        
                        <form onsubmit="updateProfile(event)" class="space-y-8">
                            <!-- Avatar Upload -->
                            <div>
                                <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('settings.profile_photo') }}
                                </label>
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        @if(auth()->user()->avatar)
                                            <img class="h-24 w-24 rounded-full object-cover border-4 border-blue-400 shadow-md" src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">
                                        @else
                                            <div class="h-24 w-24 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center border-4 border-blue-400 text-blue-600 dark:text-blue-300 text-4xl font-semibold">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file" id="avatar-upload" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                                        <x-button 
                                            type="button"
                                            onclick="document.getElementById('avatar-upload').click()" 
                                            variant="secondary"
                                            size="md"
                                        >
                                            {{ __('settings.change_photo') }}
                                        </x-button>
                                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('settings.photo_requirements') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <x-ui.input
                                        type="text"
                                        name="first_name"
                                        :label="__('settings.first_name')"
                                        :value="old('first_name', auth()->user()->first_name ?? '')"
                                        required
                                        icon="user"
                                    />
                                </div>

                                <div>
                                    <x-ui.input
                                        type="text"
                                        name="last_name"
                                        :label="__('settings.last_name')"
                                        :value="old('last_name', auth()->user()->last_name ?? '')"
                                        required
                                        icon="user"
                                    />
                                </div>
                            </div>

                            <div>
                                <x-ui.input
                                    type="email"
                                    name="email"
                                    :label="__('settings.email_address')"
                                    :value="old('email', auth()->user()->email ?? '')"
                                    required
                                    icon="at-symbol"
                                />
                            </div>

                            <div>
                                <x-ui.input
                                    type="tel"
                                    name="phone"
                                    :label="__('settings.phone_number')"
                                    :value="old('phone', auth()->user()->phone ?? '')"
                                    icon="phone"
                                />
                            </div>

                            <div>
                                <x-ui.textarea
                                    name="bio"
                                    rows="4"
                                    :label="__('settings.bio')"
                                    placeholder="{{ __('settings.tell_about_yourself') }}"
                                >{{ old('bio', auth()->user()->bio ?? '') }}</x-ui.textarea>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <x-button type="submit" variant="primary" size="lg">
                                    {{ __('settings.save_changes') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div id="notifications-settings" class="settings-content hidden">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white leading-tight">
                                {{ __('settings.notification_preferences') }}
                            </h3>
                        </div>
                        
                        <form onsubmit="updateNotificationSettings(event)" class="space-y-8">
                            <!-- Email Notifications -->
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.email_notifications') }}
                                </h4>
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                        <div>
                                            <label for="email_job_alerts" class="text-base font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                {{ __('settings.job_alerts') }}
                                            </label>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ __('settings.job_alerts_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="email_job_alerts" 
                                            id="email_job_alerts"
                                            value="1"
                                            {{ (auth()->user()->settings['email_job_alerts'] ?? true) ? 'checked' : '' }}
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:border-gray-600"
                                        >
                                    </div>

                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                        <div>
                                            <label for="email_application_updates" class="text-base font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                {{ __('settings.application_updates') }}
                                            </label>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ __('settings.application_updates_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="email_application_updates" 
                                            id="email_application_updates"
                                            value="1"
                                            {{ (auth()->user()->settings['email_application_updates'] ?? true) ? 'checked' : '' }}
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:border-gray-600"
                                        >
                                    </div>

                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                        <div>
                                            <label for="email_newsletter" class="text-base font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                {{ __('settings.newsletter') }}
                                            </label>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ __('settings.newsletter_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="email_newsletter" 
                                            id="email_newsletter"
                                            value="1"
                                            {{ (auth()->user()->settings['email_newsletter'] ?? true) ? 'checked' : '' }}
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:border-gray-600"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Push Notifications (Conceptual) -->
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.push_notifications') }}
                                </h4>
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                        <div>
                                            <label for="push_job_alerts" class="text-base font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                {{ __('settings.job_alerts') }}
                                            </label>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ __('settings.push_job_alerts_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="push_job_alerts" 
                                            id="push_job_alerts"
                                            value="1"
                                            {{ (auth()->user()->settings['push_job_alerts'] ?? false) ? 'checked' : '' }}
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:border-gray-600"
                                        >
                                    </div>

                                    <div class="flex items-center justify-between py-2">
                                        <div>
                                            <label for="push_application_updates" class="text-base font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                {{ __('settings.application_updates') }}
                                            </label>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ __('settings.push_application_updates_description') }}
                                            </p>
                                        </div>
                                        <input 
                                            type="checkbox" 
                                            name="push_application_updates" 
                                            id="push_application_updates"
                                            value="1"
                                            {{ (auth()->user()->settings['push_application_updates'] ?? false) ? 'checked' : '' }}
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:border-gray-600"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <x-button type="submit" variant="primary" size="lg">
                                    {{ __('settings.save_changes') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Privacy & Security Settings -->
                <div id="privacy-settings" class="settings-content hidden">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white leading-tight">
                                {{ __('settings.privacy_security') }}
                            </h3>
                        </div>
                        
                        <form onsubmit="updatePrivacySettings(event)" class="space-y-8">
                            <!-- Profile Visibility -->
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.profile_visibility') }}
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                id="profile_visibility_public" 
                                                name="profile_visibility" 
                                                type="radio" 
                                                value="public"
                                                {{ (auth()->user()->settings['profile_visibility'] ?? 'public') === 'public' ? 'checked' : '' }}
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                            >
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="profile_visibility_public" class="font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                {{ __('settings.public') }}
                                            </label>
                                            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('settings.public_description') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                id="profile_visibility_private" 
                                                name="profile_visibility" 
                                                type="radio" 
                                                value="private"
                                                {{ (auth()->user()->settings['profile_visibility'] ?? 'public') === 'private' ? 'checked' : '' }}
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                            >
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="profile_visibility_private" class="font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                {{ __('settings.private') }}
                                            </label>
                                            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('settings.private_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Two-Factor Authentication (Conceptual) -->
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.two_factor_auth') }}
                                </h4>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <div>
                                        <label for="two_factor_enabled" class="text-base font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                            {{ __('settings.enable_two_factor') }}
                                        </label>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ __('settings.two_factor_description') }}
                                        </p>
                                    </div>
                                    <input 
                                        type="checkbox" 
                                        name="two_factor_enabled" 
                                        id="two_factor_enabled"
                                        value="1"
                                        {{ (auth()->user()->settings['two_factor_enabled'] ?? false) ? 'checked' : '' }}
                                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:border-gray-600"
                                    >
                                </div>
                            </div>

                            <!-- Account Deletion (Conceptual) -->
                            <div>
                                <h4 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">
                                    {{ __('settings.delete_account') }}
                                </h4>
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    {{ __('settings.delete_account_description') }}
                                </p>
                                <x-button variant="danger" size="md" onclick="confirmAccountDeletion()">
                                    {{ __('settings.delete_my_account') }}
                                </x-button>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <x-button type="submit" variant="primary" size="lg">
                                    {{ __('settings.save_changes') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div id="appearance-settings" class="settings-content hidden">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white leading-tight">
                                {{ __('settings.appearance') }}
                            </h3>
                        </div>
                        
                        <form onsubmit="updateAppearanceSettings(event)" class="space-y-8">
                            <!-- Theme Selection -->
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.theme') }}
                                </h4>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input 
                                            type="radio" 
                                            name="theme" 
                                            value="light"
                                            {{ (auth()->user()->settings['theme'] ?? 'light') === 'light' ? 'checked' : '' }}
                                            class="form-radio h-5 w-5 text-blue-600 dark:bg-gray-700 dark:border-gray-600"
                                        >
                                        <span class="ml-2 text-gray-900 dark:text-gray-300">{{ __('settings.light_theme') }}</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input 
                                            type="radio" 
                                            name="theme" 
                                            value="dark"
                                            {{ (auth()->user()->settings['theme'] ?? 'light') === 'dark' ? 'checked' : '' }}
                                            class="form-radio h-5 w-5 text-blue-600 dark:bg-gray-700 dark:border-gray-600"
                                        >
                                        <span class="ml-2 text-gray-900 dark:text-gray-300">{{ __('settings.dark_theme') }}</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input 
                                            type="radio" 
                                            name="theme" 
                                            value="system"
                                            {{ (auth()->user()->settings['theme'] ?? 'light') === 'system' ? 'checked' : '' }}
                                            class="form-radio h-5 w-5 text-blue-600 dark:bg-gray-700 dark:border-gray-600"
                                        >
                                        <span class="ml-2 text-gray-900 dark:text-gray-300">{{ __('settings.system_theme') }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Font Size (Conceptual) -->
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                    {{ __('settings.font_size') }}
                                </h4>
                                <x-ui.select name="font_size" id="font_size">
                                    <option value="sm" {{ (auth()->user()->settings['font_size'] ?? 'base') === 'sm' ? 'selected' : '' }}>{{ __('settings.small') }}</option>
                                    <option value="base" {{ (auth()->user()->settings['font_size'] ?? 'base') === 'base' ? 'selected' : '' }}>{{ __('settings.medium') }}</option>
                                    <option value="lg" {{ (auth()->user()->settings['font_size'] ?? 'base') === 'lg' ? 'selected' : '' }}>{{ __('settings.large') }}</option>
                                </x-ui.select>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <x-button type="submit" variant="primary" size="lg">
                                    {{ __('settings.save_changes') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Integrations Settings -->
                <div id="integrations-settings" class="settings-content hidden">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white leading-tight">
                                {{ __('settings.integrations') }}
                            </h3>
                        </div>
                        
                        <div class="space-y-8">
                            <!-- Google Analytics Integration -->
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                    Google Analytics
                                </h4>
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    {{ __('settings.google_analytics_description') }}
                                </p>
                                @if(config('services.google_analytics.tracking_id'))
                                    <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300">
                                        <div class="flex items-center">
                                            <x-icon name="check-circle" class="h-5 w-5 mr-3" />
                                            <span>{{ __('settings.google_analytics_connected') }}</span>
                                        </div>
                                        <x-button variant="ghost" size="sm" onclick="disconnectIntegration('google_analytics')">
                                            {{ __('settings.disconnect') }}
                                        </x-button>
                                    </div>
                                @else
                                    <x-button variant="primary" size="md" onclick="connectIntegration('google_analytics')">
                                        {{ __('settings.connect') }}
                                    </x-button>
                                @endif
                            </div>

                            <!-- Slack Integration (Conceptual) -->
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                    Slack
                                </h4>
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    {{ __('settings.slack_description') }}
                                </p>
                                <x-button variant="primary" size="md" onclick="connectIntegration('slack')">
                                    {{ __('settings.connect') }}
                                </x-button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Settings (Admin Only) -->
                @can('admin')
                <div id="system-settings" class="settings-content hidden">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white leading-tight">
                                {{ __('settings.system_settings') }}
                            </h3>
                        </div>
                        
                        <form onsubmit="updateSystemSettings(event)" class="space-y-8">
                            <!-- Site Name -->
                            <div>
                                <x-ui.input
                                    name="site_name"
                                    id="site_name"
                                    :label="__('settings.site_name')"
                                    :value="old('site_name', config('app.name'))"
                                    required
                                    icon="globe-alt"
                                />
                            </div>

                            <!-- Default Locale -->
                            <div>
                                <x-ui.select
                                    name="default_locale"
                                    id="default_locale"
                                    :label="__('settings.default_language')"
                                    :options="$availableLocales ?? []"
                                    :selected="old('default_locale', config('app.locale'))"
                                    required
                                />
                            </div>

                            <!-- Maintenance Mode -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div>
                                    <label for="maintenance_mode" class="text-base font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                        {{ __('settings.maintenance_mode') }}
                                    </label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ __('settings.maintenance_mode_description') }}
                                    </p>
                                </div>
                                <input 
                                    type="checkbox" 
                                    name="maintenance_mode" 
                                    id="maintenance_mode"
                                    value="1"
                                    {{ app()->isDownForMaintenance() ? 'checked' : '' }}
                                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:border-gray-600"
                                >
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <x-button type="submit" variant="primary" size="lg">
                                    {{ __('settings.save_changes') }}
                                </x-button>
                            </div>
                        </form>
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
    function showSettingsTab(tabId) {
        document.querySelectorAll('.settings-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        document.getElementById(tabId + '-settings').classList.remove('hidden');

        document.querySelectorAll('.settings-tab').forEach(button => {
            button.classList.remove('bg-blue-500', 'text-white', 'shadow-md');
            button.classList.add('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700', 'hover:text-blue-600', 'dark:hover:text-blue-400');
            button.querySelector('svg').classList.remove('text-white');
            button.querySelector('svg').classList.add('text-gray-500', 'dark:text-gray-400', 'group-hover:text-blue-600', 'dark:group-hover:text-blue-400');
        });
        
        const activeButton = document.querySelector(`.settings-tab[data-tab="${tabId}"]`);
        activeButton.classList.add('bg-blue-500', 'text-white', 'shadow-md');
        activeButton.classList.remove('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700', 'hover:text-blue-600', 'dark:hover:text-blue-400');
        activeButton.querySelector('svg').classList.add('text-white');
        activeButton.querySelector('svg').classList.remove('text-gray-500', 'dark:text-gray-400', 'group-hover:text-blue-600', 'dark-group-hover:text-blue-400');

        // Update URL hash to persist tab selection
        window.location.hash = tabId;
    }

    // Handle URL hash on page load
    document.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.substring(1);
        if (hash) {
            showSettingsTab(hash);
        } else {
            // Default to profile tab if no hash
            showSettingsTab('profile');
        }
    });

    // Generic function for updating settings (conceptual)
    function updateSettings(event, formId, successMessage, errorMessage) {
        event.preventDefault();
        const form = document.getElementById(formId);
        const formData = new FormData(form);

        // Simulate API call
        console.log(`Updating ${formId} settings...`, Object.fromEntries(formData.entries()));

        // Simulate success/failure
        setTimeout(() => {
            alert(successMessage);
            // In a real application, you would handle the response and update UI accordingly
        }, 1000);
    }

    // Specific update functions (conceptual)
    function updateProfile(event) {
        updateSettings(event, 'profile-settings', '{{ __('settings.profile_updated_success') }}', '{{ __('settings.profile_updated_error') }}');
    }

    function updateNotificationSettings(event) {
        updateSettings(event, 'notifications-settings', '{{ __('settings.notifications_updated_success') }}', '{{ __('settings.notifications_updated_error') }}');
    }

    function updatePrivacySettings(event) {
        updateSettings(event, 'privacy-settings', '{{ __('settings.privacy_updated_success') }}', '{{ __('settings.privacy_updated_error') }}');
    }

    function updateAppearanceSettings(event) {
        updateSettings(event, 'appearance-settings', '{{ __('settings.appearance_updated_success') }}', '{{ __('settings.appearance_updated_error') }}');
    }

    function updateSystemSettings(event) {
        updateSettings(event, 'system-settings', '{{ __('settings.system_updated_success') }}', '{{ __('settings.system_updated_error') }}');
    }

    // Avatar Preview (conceptual)
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#profile-settings img').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Account Deletion Confirmation (conceptual)
    function confirmAccountDeletion() {
        if (confirm('{{ __('settings.confirm_delete_account') }}')) {
            alert('{{ __('settings.account_deleted') }}');
            // In a real application, initiate account deletion process
        }
    }

    // Integration Connection/Disconnection (conceptual)
    function connectIntegration(integrationName) {
        alert(`{{ __('settings.connecting') }} ${integrationName}...`);
        // Simulate connection
        setTimeout(() => {
            alert(`${integrationName} {{ __('settings.connected_success') }}`);
            location.reload(); // Reload to show connected state
        }, 1500);
    }

    function disconnectIntegration(integrationName) {
        if (confirm(`{{ __('settings.confirm_disconnect') }} ${integrationName}?`)) {
            alert(`{{ __('settings.disconnecting') }} ${integrationName}...`);
            // Simulate disconnection
            setTimeout(() => {
                alert(`${integrationName} {{ __('settings.disconnected_success') }}`);
                location.reload(); // Reload to show disconnected state
            }, 1500);
        }
    }
</script>
@endpush

@endpush
