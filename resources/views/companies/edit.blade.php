@extends('layouts.app')

@section('title', __('companies.edit_company'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('companies.edit_company') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('companies.edit_company_description') }}
            </p>
        </div>

        <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data" id="company-edit-form">
            @csrf
            @method('PUT')
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('companies.company_details') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('companies.basic_information_about_company') }}
                    </p>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Company Name -->
                        <div class="sm:col-span-2">
                            <x-ui.input
                                name="name"
                                id="name"
                                :label="__('companies.company_name')"
                                :placeholder="__('companies.company_name_placeholder')"
                                :value="old('name', $company->name)"
                                required
                                :error="$errors->first('name')"
                            />
                        </div>
                        <!-- Industry -->
                        <div>
                            <x-ui.select
                                name="industry_id"
                                id="industry_id"
                                :label="__('companies.industry')"
                                :options="$industries ?? []"
                                :selected="old('industry_id', $company->industry_id)"
                                required
                                :error="$errors->first('industry_id')"
                            />
                        </div>
                        <!-- Company Size -->
                        <div>
                            <x-ui.select
                                name="company_size_id"
                                id="company_size_id"
                                :label="__('companies.company_size')"
                                :options="$companySizes ?? []"
                                :selected="old('company_size_id', $company->company_size_id)"
                                required
                                :error="$errors->first('company_size_id')"
                            />
                        </div>
                        <!-- Location -->
                        <div>
                            <x-ui.input
                                name="location"
                                id="location"
                                :label="__('companies.location')"
                                :placeholder="__('companies.location_placeholder')"
                                :value="old('location', $company->location)"
                                required
                                :error="$errors->first('location')"
                                icon="map-pin"
                            />
                        </div>
                        <!-- Founded Year -->
                        <div>
                            <x-ui.input
                                name="founded_year"
                                id="founded_year"
                                type="number"
                                :label="__('companies.founded_year')"
                                :placeholder="__('companies.founded_year_placeholder')"
                                :value="old('founded_year', $company->founded_year)"
                                :error="$errors->first('founded_year')"
                                min="1800"
                                max="{{ date('Y') }}"
                            />
                        </div>
                        <!-- Website -->
                        <div class="sm:col-span-2">
                            <x-ui.input
                                name="website"
                                id="website"
                                :label="__('companies.website')"
                                :placeholder="__('companies.website_placeholder')"
                                :value="old('website', $company->website)"
                                :error="$errors->first('website')"
                                icon="globe-alt"
                            />
                        </div>
                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <x-ui.textarea
                                name="description"
                                id="description"
                                :label="__('companies.description')"
                                :placeholder="__('companies.description_placeholder')"
                                :value="old('description', $company->description)"
                                :error="$errors->first('description')"
                                rows="5"
                            />
                        </div>
                        <!-- Mission -->
                        <div class="sm:col-span-2">
                            <x-ui.textarea
                                name="mission"
                                id="mission"
                                :label="__('companies.mission')"
                                :placeholder="__('companies.mission_placeholder')"
                                :value="old('mission', $company->mission)"
                                :error="$errors->first('mission')"
                                rows="3"
                            />
                        </div>
                        <!-- Values -->
                        <div class="sm:col-span-2">
                            <x-ui.textarea
                                name="values"
                                id="values"
                                :label="__('companies.values')"
                                :placeholder="__('companies.values_placeholder')"
                                :value="old('values', $company->values)"
                                :error="$errors->first('values')"
                                rows="3"
                            />
                        </div>
                        <!-- Logo Upload -->
                        <div>
                            <x-ui.input
                                name="logo"
                                id="logo"
                                type="file"
                                :label="__('companies.logo')"
                                :error="$errors->first('logo')"
                                accept="image/*"
                            />
                        </div>
                        <!-- Cover Photo Upload -->
                        <div>
                            <x-ui.input
                                name="cover_photo"
                                id="cover_photo"
                                type="file"
                                :label="__('companies.cover_photo')"
                                :error="$errors->first('cover_photo')"
                                accept="image/*"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <x-ui.button type="submit" variant="primary">
                    {{ __('companies.save_changes') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection 