@extends('layouts.app')

@section('title', __('aboutus.title'))
@section('description', __('aboutus.meta_description'))

@section('content')
    <!-- Hero Section -->
    <section class="bg-blue-700 dark:bg-blue-900 text-white py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl font-extrabold leading-tight mb-4">
                {{ __('aboutus.hero_heading') }}
            </h1>
            <p class="text-xl font-light mb-8 max-w-3xl mx-auto">
                {{ __('aboutus.hero_subheading') }}
            </p>
            <x-button href="{{ route('contact') }}" variant="light" size="lg" class="inline-flex items-center group">
                {{ __('aboutus.contact_us_button') }}
                <x-icon name="arrow-right" class="ml-2 h-5 w-5 transition-transform duration-200 group-hover:translate-x-1" />
            </x-button>
        </div>
    </section>

    <!-- Our Mission Section -->
    <section class="py-16 bg-white dark:bg-gray-800 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="order-2 md:order-1">
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-6">
                    {{ __('aboutus.mission_title') }}
                </h2>
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                    {{ __('aboutus.mission_paragraph_1') }}
                </p>
                <p class="text-lg text-gray-700 dark:text-gray-300">
                    {{ __('aboutus.mission_paragraph_2') }}
                </p>
            </div>
            <div class="order-1 md:order-2">
                <img src="{{ asset('images/slider-3.jpg') }}" alt="Our Mission" class="rounded-xl shadow-lg w-full h-80 object-cover">
            </div>
        </div>
    </section>

    <!-- Our Values Section -->
    <section class="py-16 bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-12">
                {{ __('aboutus.values_title') }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 transform hover:scale-105 transition-transform duration-300">
                    <x-icon name="light-bulb" class="h-12 w-12 text-blue-600 dark:text-blue-400 mx-auto mb-6" />
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        {{ __('aboutus.value_innovation_title') }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('aboutus.value_innovation_description') }}
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 transform hover:scale-105 transition-transform duration-300">
                    <x-icon name="users" class="h-12 w-12 text-blue-600 dark:text-blue-400 mx-auto mb-6" />
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        {{ __('aboutus.value_integrity_title') }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('aboutus.value_integrity_description') }}
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 transform hover:scale-105 transition-transform duration-300">
                    <x-icon name="heart" class="h-12 w-12 text-blue-600 dark:text-blue-400 mx-auto mb-6" />
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        {{ __('aboutus.value_customer_focus_title') }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('aboutus.value_customer_focus_description') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team Section (Conceptual) -->
    <section class="py-16 bg-white dark:bg-gray-800 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-12">
                {{ __('aboutus.team_title') }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Team Member 1 -->
                <div class="flex flex-col items-center">
                    <img class="h-32 w-32 rounded-full object-cover mb-4 shadow-md" src="{{ asset('images/profile.png') }}" alt="Team Member">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('aboutus.team_member_1_name') }}</h3>
                    <p class="text-blue-600 dark:text-blue-400">{{ __('aboutus.team_member_1_role') }}</p>
                </div>
                <!-- Team Member 2 -->
                <div class="flex flex-col items-center">
                    <img class="h-32 w-32 rounded-full object-cover mb-4 shadow-md" src="{{ asset('images/profile.png') }}" alt="Team Member">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('aboutus.team_member_2_name') }}</h3>
                    <p class="text-blue-600 dark:text-blue-400">{{ __('aboutus.team_member_2_role') }}</p>
                </div>
                <!-- Team Member 3 -->
                <div class="flex flex-col items-center">
                    <img class="h-32 w-32 rounded-full object-cover mb-4 shadow-md" src="{{ asset('images/profile.png') }}" alt="Team Member">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('aboutus.team_member_3_name') }}</h3>
                    <p class="text-blue-600 dark:text-blue-400">{{ __('aboutus.team_member_3_role') }}</p>
                </div>
                <!-- Team Member 4 -->
                <div class="flex flex-col items-center">
                    <img class="h-32 w-32 rounded-full object-cover mb-4 shadow-md" src="{{ asset('images/profile.png') }}" alt="Team Member">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('aboutus.team_member_4_name') }}</h3>
                    <p class="text-blue-600 dark:text-blue-400">{{ __('aboutus.team_member_4_role') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="bg-blue-600 dark:bg-blue-800 text-white py-16 px-4 sm:px-6 lg:px-8 text-center">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-4xl font-extrabold mb-4">
                {{ __('aboutus.cta_heading') }}
            </h2>
            <p class="text-lg mb-8">
                {{ __('aboutus.cta_subheading') }}
            </p>
            <x-button href="{{ route('jobs.index') }}" variant="light" size="lg" class="inline-flex items-center group">
                {{ __('aboutus.find_your_dream_job') }}
                <x-icon name="briefcase" class="ml-2 h-5 w-5 transition-transform duration-200 group-hover:scale-110" />
            </x-button>
        </div>
    </section>
@endsection 