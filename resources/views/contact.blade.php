@extends('layouts.app')

@section('title', __('contact.title'))
@section('description', __('contact.meta_description'))

@section('content')
    <!-- Hero Section -->
    <section class="bg-blue-700 dark:bg-blue-900 text-white py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl font-extrabold leading-tight mb-4">
                {{ __('contact.hero_heading') }}
            </h1>
            <p class="text-xl font-light mb-8 max-w-3xl mx-auto">
                {{ __('contact.hero_subheading') }}
            </p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-16 bg-white dark:bg-gray-800 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6 text-center">
                    {{ __('contact.form_title') }}
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 text-center">
                    {{ __('contact.form_description') }}
                </p>
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-ui.input
                                type="text"
                                name="first_name"
                                :label="__('contact.first_name')"
                                :placeholder="__('contact.first_name_placeholder')"
                                :value="old('first_name')"
                                required
                                :error="$errors->first('first_name')"
                                icon="user"
                            />
                        </div>
                        <div>
                            <x-ui.input
                                type="text"
                                name="last_name"
                                :label="__('contact.last_name')"
                                :placeholder="__('contact.last_name_placeholder')"
                                :value="old('last_name')"
                                :error="$errors->first('last_name')"
                                icon="user"
                            />
                        </div>
                    </div>
                    <div>
                        <x-ui.input
                            type="email"
                            name="email"
                            :label="__('contact.email_address')"
                            :placeholder="__('contact.email_placeholder')"
                            :value="old('email')"
                            required
                            :error="$errors->first('email')"
                            icon="at-symbol"
                        />
                    </div>
                    <div>
                        <x-ui.input
                            type="tel"
                            name="phone"
                            :label="__('contact.phone_number')"
                            :placeholder="__('contact.phone_placeholder')"
                            :value="old('phone')"
                            :error="$errors->first('phone')"
                            icon="phone"
                        />
                    </div>
                    <div>
                        <x-ui.input
                            type="text"
                            name="subject"
                            :label="__('contact.subject')"
                            :placeholder="__('contact.subject_placeholder')"
                            :value="old('subject')"
                            required
                            :error="$errors->first('subject')"
                            icon="chat-bubble-left-right"
                        />
                    </div>
                    <div>
                        <x-ui.textarea
                            name="message"
                            :label="__('contact.message')"
                            :placeholder="__('contact.message_placeholder')"
                            rows="5"
                            required
                            :error="$errors->first('message')"
                        >{{ old('message') }}</x-ui.textarea>
                    </div>
                    <div class="flex justify-end">
                        <x-button type="submit" variant="primary" size="lg" class="inline-flex items-center group">
                            {{ __('contact.send_message') }}
                            <x-icon name="paper-airplane" class="ml-2 h-5 w-5 transition-transform duration-200 group-hover:translate-x-1" />
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Map/Location Section (Conceptual) -->
    <section class="py-16 bg-gray-100 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-12">
                {{ __('contact.our_location_title') }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('contact.address_info') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">{{ __('contact.company_address_line1') }}</p>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">{{ __('contact.company_address_line2') }}</p>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('contact.company_address_line3') }}</p>
                    <p class="text-gray-600 dark:text-gray-400 mb-2"><strong>{{ __('contact.phone') }}:</strong> {{ __('contact.company_phone') }}</p>
                    <p class="text-gray-600 dark:text-gray-400"><strong>{{ __('contact.email') }}:</strong> <a href="mailto:{{ __('contact.company_email') }}" class="text-blue-600 hover:underline">{{ __('contact.company_email') }}</a></p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <!-- Placeholder for interactive map (e.g., Google Maps embed) -->
                    <div class="h-64 w-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                        {{ __('contact.map_placeholder') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection 