@extends('layouts.app')

@section('title', __('candidates.edit_candidate'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('candidates.edit_candidate') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('candidates.edit_candidate_description') }}
            </p>
        </div>

        <form action="{{ route('candidates.update', $candidate) }}" method="POST" enctype="multipart/form-data" id="candidate-edit-form">
            @csrf
            @method('PUT')
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('candidates.profile_details') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('candidates.basic_information_about_candidate') }}
                    </p>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Name -->
                        <div class="sm:col-span-2">
                            <x-ui.input
                                name="name"
                                id="name"
                                :label="__('candidates.name')"
                                :placeholder="__('candidates.name_placeholder')"
                                :value="old('name', $candidate->name ?? '')"
                                required
                                :error="$errors->first('name')"
                            />
                        </div>
                        <!-- Email -->
                        <div class="sm:col-span-2">
                            <x-ui.input
                                name="email"
                                id="email"
                                type="email"
                                :label="__('candidates.email')"
                                :placeholder="__('candidates.email_placeholder')"
                                :value="old('email', $candidate->email ?? '')"
                                required
                                :error="$errors->first('email')"
                            />
                        </div>
                        <!-- Headline -->
                        <div class="sm:col-span-2">
                            <x-ui.input
                                name="headline"
                                id="headline"
                                :label="__('candidates.headline')"
                                :placeholder="__('candidates.headline_placeholder')"
                                :value="old('headline', $candidate->headline)"
                                :error="$errors->first('headline')"
                            />
                        </div>
                        <!-- Phone Number -->
                        <div>
                            <x-ui.input
                                name="phone_number"
                                id="phone_number"
                                :label="__('candidates.phone_number')"
                                :placeholder="__('candidates.phone_number_placeholder')"
                                :value="old('phone_number', $candidate->phone_number)"
                                :error="$errors->first('phone_number')"
                            />
                        </div>
                        <!-- Location -->
                        <div>
                            <x-ui.input
                                name="location"
                                id="location"
                                :label="__('candidates.location')"
                                :placeholder="__('candidates.location_placeholder')"
                                :value="old('location', $candidate->location)"
                                :error="$errors->first('location')"
                                icon="map-pin"
                            />
                        </div>
                        <!-- Website -->
                        <div class="sm:col-span-2">
                            <x-ui.input
                                name="website"
                                id="website"
                                :label="__('candidates.website')"
                                :placeholder="__('candidates.website_placeholder')"
                                :value="old('website', $candidate->website)"
                                :error="$errors->first('website')"
                                icon="globe-alt"
                            />
                        </div>
                        <!-- Profile Picture Upload -->
                        <div>
                            <x-ui.input
                                name="profile_picture"
                                id="profile_picture"
                                type="file"
                                :label="__('candidates.profile_picture')"
                                :error="$errors->first('profile_picture')"
                                accept="image/*"
                            />
                        </div>
                        <!-- Resume Upload -->
                        <div>
                            <x-ui.input
                                name="resume"
                                id="resume"
                                type="file"
                                :label="__('candidates.resume')"
                                :error="$errors->first('resume')"
                                accept="application/pdf"
                            />
                        </div>
                        <!-- Bio -->
                        <div class="sm:col-span-2">
                            <x-ui.textarea
                                name="bio"
                                id="bio"
                                :label="__('candidates.bio')"
                                :placeholder="__('candidates.bio_placeholder')"
                                :value="old('bio', $candidate->bio)"
                                :error="$errors->first('bio')"
                                rows="5"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <x-ui.button type="submit" variant="primary">
                    {{ __('candidates.save_changes') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection 