@extends('layouts.app')

@section('title', __('company.edit_company_profile'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('company.edit_company_profile') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('company.manage_company_information') }}
                    </p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Profile Completion -->
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('company.completion') }}</div>
                        <div class="flex items-center mt-1">
                            <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $profileCompletion ?? 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profileCompletion ?? 0 }}%</span>
                        </div>
                    </div>
                    
                    <x-ui.button 
                        href="{{ route('companies.show', $company) }}" 
                        variant="secondary"
                        icon="eye"
                    >
                        {{ __('company.view_public_profile') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <form action="{{ route('employer.company.update') }}" method="POST" enctype="multipart/form-data" id="company-form">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('company.basic_information') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('company.basic_company_details') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <!-- Company Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('company.company_logo') }}
                        </label>
                        
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                @if($company->logo)
                                    <img class="h-20 w-20 rounded-lg object-cover" src="{{ $company->logo }}" alt="{{ $company->name }}">
                                @else
                                    <div class="h-20 w-20 rounded-lg bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <x-icon name="building-office" class="h-10 w-10 text-gray-500 dark:text-gray-400" />
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <x-ui.file-upload
                                    name="logo"
                                    id="logo"
                                    accept="image/*"
                                    :allowedTypes="['logo']"
                                    maxSize="5"
                                    :hint="__('company.logo_requirements')"
                                    :error="$errors->first('logo')"
                                    :dropzone="false"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Company Cover Photo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('company.cover_photo') }}
                        </label>
                        
                        <div class="space-y-4">
                            @if($company->cover_photo)
                                <div class="w-full h-32 rounded-lg overflow-hidden">
                                    <img class="w-full h-full object-cover" src="{{ $company->cover_photo }}" alt="{{ $company->name }} Cover">
                                </div>
                            @else
                                <div class="w-full h-32 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <x-icon name="photo" class="h-8 w-8 text-gray-400" />
                                </div>
                            @endif
                            
                            <x-ui.file-upload
                                name="cover_photo"
                                id="cover_photo"
                                accept="image/*"
                                :allowedTypes="['cover']"
                                maxSize="10"
                                :hint="__('company.cover_photo_requirements')"
                                :error="$errors->first('cover_photo')"
                                :dropzone="false"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Company Name -->
                        <x-ui.input
                            name="name"
                            id="name"
                            :label="__('company.company_name')"
                            :value="old('name', $company->name ?? '')"
                            required
                            :error="$errors->first('name')"
                            icon="building-office"
                        />

                        <!-- Industry -->
                        <x-ui.select
                            name="industry_id"
                            id="industry_id"
                            :label="__('company.industry')"
                            :options="$industries ?? []"
                            :selected="old('industry_id', $company->industry_id ?? '')"
                            required
                            :error="$errors->first('industry_id')"
                            searchable="true"
                        />

                        <!-- Company Size -->
                        <x-ui.select
                            name="company_size_id"
                            id="company_size_id"
                            :label="__('company.company_size')"
                            :options="$companySizes ?? []"
                            :selected="old('company_size_id', $company->company_size_id ?? '')"
                            :error="$errors->first('company_size_id')"
                        />

                        <!-- Founded Year -->
                        <x-ui.input
                            name="founded_year"
                            id="founded_year"
                            type="number"
                            :label="__('company.founded_year')"
                            :value="old('founded_year', $company->founded_year ?? '')"
                            :error="$errors->first('founded_year')"
                            icon="calendar"
                            min="1800"
                            :max="date('Y')"
                        />

                        <!-- Website -->
                        <x-ui.input
                            name="website"
                            id="website"
                            type="url"
                            :label="__('company.website')"
                            :placeholder="__('company.website_placeholder')"
                            :value="old('website', $company->website ?? '')"
                            :error="$errors->first('website')"
                            icon="globe-alt"
                        />

                        <!-- Phone -->
                        <x-ui.input
                            name="phone"
                            id="phone"
                            type="tel"
                            :label="__('company.phone')"
                            :value="old('phone', $company->phone ?? '')"
                            :error="$errors->first('phone')"
                            icon="phone"
                        />
                    </div>

                    <!-- Company Description -->
                    <x-ui.textarea
                        name="description"
                        id="description"
                        :label="__('company.company_description')"
                        :placeholder="__('company.description_placeholder')"
                        :value="old('description', $company->description ?? '')"
                        required
                        :error="$errors->first('description')"
                        rows="6"
                        maxlength="2000"
                        showCounter="true"
                        :hint="__('company.description_hint')"
                    />

                    <!-- Mission Statement -->
                    <x-ui.textarea
                        name="mission"
                        id="mission"
                        :label="__('company.mission_statement')"
                        :placeholder="__('company.mission_placeholder')"
                        :value="old('mission', $company->mission ?? '')"
                        :error="$errors->first('mission')"
                        rows="4"
                        maxlength="1000"
                        showCounter="true"
                    />
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('company.location_information') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('company.headquarters_and_locations') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Country -->
                        <x-ui.select
                            name="country_id"
                            id="country_id"
                            :label="__('company.country')"
                            :options="$countries ?? []"
                            :selected="old('country_id', $company->country_id ?? '')"
                            required
                            :error="$errors->first('country_id')"
                            searchable="true"
                        />

                        <!-- State/Province -->
                        <x-ui.select
                            name="state_id"
                            id="state_id"
                            :label="__('company.state_province')"
                            :options="$states ?? []"
                            :selected="old('state_id', $company->state_id ?? '')"
                            :error="$errors->first('state_id')"
                            searchable="true"
                        />

                        <!-- City -->
                        <x-ui.input
                            name="city"
                            id="city"
                            :label="__('company.city')"
                            :value="old('city', $company->city ?? '')"
                            required
                            :error="$errors->first('city')"
                            icon="map-pin"
                        />

                        <!-- Postal Code -->
                        <x-ui.input
                            name="postal_code"
                            id="postal_code"
                            :label="__('company.postal_code')"
                            :value="old('postal_code', $company->postal_code ?? '')"
                            :error="$errors->first('postal_code')"
                            icon="map"
                        />
                    </div>

                    <!-- Address -->
                    <x-ui.textarea
                        name="address"
                        id="address"
                        :label="__('company.address')"
                        :placeholder="__('company.address_placeholder')"
                        :value="old('address', $company->address ?? '')"
                        :error="$errors->first('address')"
                        rows="3"
                    />

                    <!-- Additional Locations -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('company.additional_locations') }}
                        </label>
                        <div id="additional-locations">
                            @if($company->additional_locations ?? false)
                                @foreach($company->additional_locations as $index => $location)
                                    <div class="location-item flex items-center space-x-2 mb-2">
                                        <x-ui.input
                                            name="additional_locations[{{ $index }}]"
                                            :placeholder="__('company.location_placeholder')"
                                            :value="$location"
                                            class="flex-1"
                                        />
                                        <x-ui.button 
                                            type="button" 
                                            variant="ghost" 
                                            size="sm"
                                            onclick="removeLocation(this)"
                                            class="text-red-600 hover:text-red-500"
                                        >
                                            {{ __('company.remove') }}
                                        </x-ui.button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <x-ui.button 
                            type="button" 
                            variant="secondary" 
                            size="sm"
                            onclick="addLocation()"
                        >
                            {{ __('company.add_location') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>

            <!-- Social Media & Links -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('company.social_media_links') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('company.company_social_presence') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- LinkedIn -->
                        <x-ui.input
                            name="linkedin_url"
                            id="linkedin_url"
                            type="url"
                            :label="__('company.linkedin_page')"
                            :placeholder="__('company.linkedin_placeholder')"
                            :value="old('linkedin_url', $company->linkedin_url ?? '')"
                            :error="$errors->first('linkedin_url')"
                            icon="link"
                        />

                        <!-- Twitter -->
                        <x-ui.input
                            name="twitter_url"
                            id="twitter_url"
                            type="url"
                            :label="__('company.twitter_profile')"
                            :placeholder="__('company.twitter_placeholder')"
                            :value="old('twitter_url', $company->twitter_url ?? '')"
                            :error="$errors->first('twitter_url')"
                            icon="link"
                        />

                        <!-- Facebook -->
                        <x-ui.input
                            name="facebook_url"
                            id="facebook_url"
                            type="url"
                            :label="__('company.facebook_page')"
                            :placeholder="__('company.facebook_placeholder')"
                            :value="old('facebook_url', $company->facebook_url ?? '')"
                            :error="$errors->first('facebook_url')"
                            icon="link"
                        />

                        <!-- Instagram -->
                        <x-ui.input
                            name="instagram_url"
                            id="instagram_url"
                            type="url"
                            :label="__('company.instagram_profile')"
                            :placeholder="__('company.instagram_placeholder')"
                            :value="old('instagram_url', $company->instagram_url ?? '')"
                            :error="$errors->first('instagram_url')"
                            icon="link"
                        />

                        <!-- YouTube -->
                        <x-ui.input
                            name="youtube_url"
                            id="youtube_url"
                            type="url"
                            :label="__('company.youtube_channel')"
                            :placeholder="__('company.youtube_placeholder')"
                            :value="old('youtube_url', $company->youtube_url ?? '')"
                            :error="$errors->first('youtube_url')"
                            icon="link"
                        />

                        <!-- GitHub -->
                        <x-ui.input
                            name="github_url"
                            id="github_url"
                            type="url"
                            :label="__('company.github_organization')"
                            :placeholder="__('company.github_placeholder')"
                            :value="old('github_url', $company->github_url ?? '')"
                            :error="$errors->first('github_url')"
                            icon="link"
                        />
                    </div>
                </div>
            </div>

            <!-- Company Culture & Benefits -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('company.culture_benefits') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('company.what_makes_company_special') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <!-- Company Values -->
                    <x-ui.textarea
                        name="values"
                        id="values"
                        :label="__('company.company_values')"
                        :placeholder="__('company.values_placeholder')"
                        :value="old('values', $company->values ?? '')"
                        :error="$errors->first('values')"
                        rows="4"
                        maxlength="1000"
                        showCounter="true"
                    />

                    <!-- Work Environment -->
                    <x-ui.textarea
                        name="work_environment"
                        id="work_environment"
                        :label="__('company.work_environment')"
                        :placeholder="__('company.work_environment_placeholder')"
                        :value="old('work_environment', $company->work_environment ?? '')"
                        :error="$errors->first('work_environment')"
                        rows="4"
                        maxlength="1000"
                        showCounter="true"
                    />

                    <!-- Benefits -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('company.benefits_perks') }}
                        </label>
                        <div id="benefits-list">
                            @if($company->benefits ?? false)
                                @foreach($company->benefits as $index => $benefit)
                                    <div class="benefit-item flex items-center space-x-2 mb-2">
                                        <x-ui.input
                                            name="benefits[{{ $index }}]"
                                            :placeholder="__('company.benefit_placeholder')"
                                            :value="$benefit"
                                            class="flex-1"
                                        />
                                        <x-ui.button 
                                            type="button" 
                                            variant="ghost" 
                                            size="sm"
                                            onclick="removeBenefit(this)"
                                            class="text-red-600 hover:text-red-500"
                                        >
                                            {{ __('company.remove') }}
                                        </x-ui.button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <x-ui.button 
                            type="button" 
                            variant="secondary" 
                            size="sm"
                            onclick="addBenefit()"
                        >
                            {{ __('company.add_benefit') }}
                        </x-ui.button>
                    </div>

                    <!-- Remote Work Policy -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('company.remote_work_policy') }}
                        </label>
                        
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input 
                                    id="remote_onsite" 
                                    name="remote_policy" 
                                    type="radio" 
                                    value="onsite"
                                    {{ old('remote_policy', $company->remote_policy ?? '') === 'onsite' ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                >
                                <label for="remote_onsite" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('company.onsite_only') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="remote_hybrid" 
                                    name="remote_policy" 
                                    type="radio" 
                                    value="hybrid"
                                    {{ old('remote_policy', $company->remote_policy ?? '') === 'hybrid' ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                >
                                <label for="remote_hybrid" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('company.hybrid_remote') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="remote_full" 
                                    name="remote_policy" 
                                    type="radio" 
                                    value="remote"
                                    {{ old('remote_policy', $company->remote_policy ?? '') === 'remote' ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                >
                                <label for="remote_full" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('company.fully_remote') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="remote_flexible" 
                                    name="remote_policy" 
                                    type="radio" 
                                    value="flexible"
                                    {{ old('remote_policy', $company->remote_policy ?? '') === 'flexible' ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                >
                                <label for="remote_flexible" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('company.flexible_remote') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO & Visibility Settings -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('company.seo_visibility') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('company.search_optimization_settings') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <!-- SEO Title -->
                    <x-ui.input
                        name="seo_title"
                        id="seo_title"
                        :label="__('company.seo_title')"
                        :placeholder="__('company.seo_title_placeholder')"
                        :value="old('seo_title', $company->seo_title ?? '')"
                        :error="$errors->first('seo_title')"
                        maxlength="60"
                        :hint="__('company.seo_title_hint')"
                    />

                    <!-- Meta Description -->
                    <x-ui.textarea
                        name="meta_description"
                        id="meta_description"
                        :label="__('company.meta_description')"
                        :placeholder="__('company.meta_description_placeholder')"
                        :value="old('meta_description', $company->meta_description ?? '')"
                        :error="$errors->first('meta_description')"
                        rows="3"
                        maxlength="160"
                        showCounter="true"
                        :hint="__('company.meta_description_hint')"
                    />

                    <!-- Keywords -->
                    <x-ui.input
                        name="keywords"
                        id="keywords"
                        :label="__('company.keywords')"
                        :placeholder="__('company.keywords_placeholder')"
                        :value="old('keywords', $company->keywords ?? '')"
                        :error="$errors->first('keywords')"
                        :hint="__('company.keywords_hint')"
                    />

                    <!-- Visibility Settings -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('company.public_profile') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('company.allow_public_visibility') }}
                                </p>
                            </div>
                            <input 
                                id="is_public" 
                                name="is_public" 
                                type="checkbox" 
                                value="1"
                                {{ old('is_public', $company->is_public ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('company.searchable') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('company.appear_in_search_results') }}
                                </p>
                            </div>
                            <input 
                                id="is_searchable" 
                                name="is_searchable" 
                                type="checkbox" 
                                value="1"
                                {{ old('is_searchable', $company->is_searchable ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('company.featured_company') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('company.highlight_in_listings') }}
                                </p>
                            </div>
                            <input 
                                id="is_featured" 
                                name="is_featured" 
                                type="checkbox" 
                                value="1"
                                {{ old('is_featured', $company->is_featured ?? false) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <x-ui.button 
                    href="{{ route('employer.dashboard') }}" 
                    variant="secondary"
                >
                    {{ __('company.cancel') }}
                </x-ui.button>

                <div class="flex space-x-3">
                    <x-ui.button 
                        type="button" 
                        variant="secondary"
                        id="save-draft"
                    >
                        {{ __('company.save_draft') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        type="submit" 
                        variant="primary"
                        id="save-company"
                    >
                        {{ __('company.save_company_profile') }}
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('company-form');
    
    // Country/State dependency
    const countrySelect = document.getElementById('country_id');
    const stateSelect = document.getElementById('state_id');
    
    if (countrySelect && stateSelect) {
        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            
            // Clear state options
            stateSelect.innerHTML = '<option value="">{{ __("company.select_state") }}</option>';
            
            if (countryId) {
                // Fetch states for selected country
                fetch(`/api/countries/${countryId}/states`)
                    .then(response => response.json())
                    .then(states => {
                        states.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.name;
                            stateSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching states:', error));
            }
        });
    }
    
    // Auto-save draft functionality
    let autoSaveTimer;
    const autoSaveDelay = 60000; // 1 minute
    
    function autoSave() {
        const formData = new FormData(form);
        formData.append('auto_save', '1');
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Company profile auto-saved');
            }
        })
        .catch(error => console.error('Auto-save error:', error));
    }
    
    // Reset auto-save timer on form changes
    form.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSave, autoSaveDelay);
    });
    
    // Save draft button
    document.getElementById('save-draft').addEventListener('click', function() {
        const draftInput = document.createElement('input');
        draftInput.type = 'hidden';
        draftInput.name = 'save_as_draft';
        draftInput.value = '1';
        form.appendChild(draftInput);
        
        form.submit();
    });
    
    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        const submitButton = document.getElementById('save-company');
        const originalText = submitButton.textContent;
        
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('company.saving') }}...
            </div>
        `;
    });
    
    // Profile completion calculation
    function calculateCompletion() {
        const requiredFields = [
            'name', 'description', 'industry_id', 'country_id', 'city', 'website'
        ];
        
        let completed = 0;
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (element && element.value.trim()) {
                completed++;
            }
        });
        
        // Check if logo is uploaded
        const logo = document.getElementById('logo');
        if (logo && logo.files.length > 0) {
            completed++;
        }
        
        const percentage = Math.round((completed / (requiredFields.length + 1)) * 100);
        
        // Update progress bar
        const progressBar = document.querySelector('.bg-blue-600');
        const progressText = document.querySelector('.text-sm.font-medium');
        
        if (progressBar && progressText) {
            progressBar.style.width = percentage + '%';
            progressText.textContent = percentage + '%';
        }
    }
    
    // Update completion on form changes
    form.addEventListener('input', calculateCompletion);
    form.addEventListener('change', calculateCompletion);
    
    // Initial calculation
    calculateCompletion();
});

// Dynamic list management
function addLocation() {
    const container = document.getElementById('additional-locations');
    const index = container.children.length;
    
    const div = document.createElement('div');
    div.className = 'location-item flex items-center space-x-2 mb-2';
    div.innerHTML = `
        <input type="text" name="additional_locations[${index}]" placeholder="{{ __('company.location_placeholder') }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <button type="button" class="px-3 py-2 text-sm text-red-600 hover:text-red-500" onclick="removeLocation(this)">{{ __('company.remove') }}</button>
    `;
    
    container.appendChild(div);
}

function removeLocation(button) {
    button.parentElement.remove();
}

function addBenefit() {
    const container = document.getElementById('benefits-list');
    const index = container.children.length;
    
    const div = document.createElement('div');
    div.className = 'benefit-item flex items-center space-x-2 mb-2';
    div.innerHTML = `
        <input type="text" name="benefits[${index}]" placeholder="{{ __('company.benefit_placeholder') }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <button type="button" class="px-3 py-2 text-sm text-red-600 hover:text-red-500" onclick="removeBenefit(this)">{{ __('company.remove') }}</button>
    `;
    
    container.appendChild(div);
}

function removeBenefit(button) {
    button.parentElement.remove();
}
</script>
@endpush 