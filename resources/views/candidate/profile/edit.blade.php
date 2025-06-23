@extends('layouts.app')

@section('title', __('profile.edit_profile'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('profile.edit_profile') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('profile.complete_profile_description') }}
                    </p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Profile Completion -->
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('profile.completion') }}</div>
                        <div class="flex items-center mt-1">
                            <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $profileCompletion ?? 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $profileCompletion ?? 0 }}%</span>
                        </div>
                    </div>
                    
                    <x-ui.button 
                        href="{{ route('candidate.profile.show') }}" 
                        variant="secondary"
                        icon="eye"
                    >
                        {{ __('profile.view_public_profile') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <form action="{{ route('candidate.profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('profile.personal_information') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('profile.basic_information_about_you') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <!-- Profile Photo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('profile.profile_photo') }}
                        </label>
                        
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                @if($candidate->avatar ?? null)
                                    <img class="h-20 w-20 rounded-full object-cover" src="{{ $candidate->avatar }}" alt="{{ $candidate->full_name }}">
                                @else
                                    <div class="h-20 w-20 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <x-icon name="user" class="h-10 w-10 text-gray-500 dark:text-gray-400" />
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <x-ui.file-upload
                                    name="avatar"
                                    id="avatar"
                                    accept="image/*"
                                    :allowedTypes="['logo']"
                                    maxSize="5"
                                    :hint="__('profile.photo_requirements')"
                                    :error="$errors->first('avatar')"
                                    :dropzone="false"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- First Name -->
                        <x-ui.input
                            name="first_name"
                            id="first_name"
                            :label="__('profile.first_name')"
                            :value="old('first_name', $candidate->first_name ?? '')"
                            required
                            :error="$errors->first('first_name')"
                            icon="user"
                        />

                        <!-- Last Name -->
                        <x-ui.input
                            name="last_name"
                            id="last_name"
                            :label="__('profile.last_name')"
                            :value="old('last_name', $candidate->last_name ?? '')"
                            required
                            :error="$errors->first('last_name')"
                            icon="user"
                        />

                        <!-- Email -->
                        <x-ui.input
                            name="email"
                            id="email"
                            type="email"
                            :label="__('profile.email')"
                            :value="old('email', $candidate->email ?? '')"
                            required
                            :error="$errors->first('email')"
                            icon="envelope"
                        />

                        <!-- Phone -->
                        <x-ui.input
                            name="phone"
                            id="phone"
                            type="tel"
                            :label="__('profile.phone')"
                            :value="old('phone', $candidate->phone ?? '')"
                            :error="$errors->first('phone')"
                            icon="phone"
                        />

                        <!-- Date of Birth -->
                        <x-ui.input
                            name="date_of_birth"
                            id="date_of_birth"
                            type="date"
                            :label="__('profile.date_of_birth')"
                            :value="old('date_of_birth', $candidate->date_of_birth?->format('Y-m-d') ?? '')"
                            :error="$errors->first('date_of_birth')"
                            icon="calendar"
                            :max="date('Y-m-d', strtotime('-16 years'))"
                        />

                        <!-- Gender -->
                        <x-ui.select
                            name="gender"
                            id="gender"
                            :label="__('profile.gender')"
                            :options="[
                                '' => __('profile.select_gender'),
                                'male' => __('profile.male'),
                                'female' => __('profile.female'),
                                'other' => __('profile.other'),
                                'prefer_not_to_say' => __('profile.prefer_not_to_say')
                            ]"
                            :selected="old('gender', $candidate->gender ?? '')"
                            :error="$errors->first('gender')"
                        />

                        <!-- Marital Status -->
                        <x-ui.select
                            name="marital_status_id"
                            id="marital_status_id"
                            :label="__('profile.marital_status')"
                            :options="$maritalStatuses ?? []"
                            :selected="old('marital_status_id', $candidate->marital_status_id ?? '')"
                            :error="$errors->first('marital_status_id')"
                        />

                        <!-- Nationality -->
                        <x-ui.select
                            name="nationality_id"
                            id="nationality_id"
                            :label="__('profile.nationality')"
                            :options="$countries ?? []"
                            :selected="old('nationality_id', $candidate->nationality_id ?? '')"
                            :error="$errors->first('nationality_id')"
                            searchable="true"
                        />
                    </div>

                    <!-- Professional Title -->
                    <x-ui.input
                        name="professional_title"
                        id="professional_title"
                        :label="__('profile.professional_title')"
                        :placeholder="__('profile.professional_title_placeholder')"
                        :value="old('professional_title', $candidate->professional_title ?? '')"
                        :error="$errors->first('professional_title')"
                        icon="briefcase"
                        :hint="__('profile.professional_title_hint')"
                    />

                    <!-- Bio/Summary -->
                    <x-ui.textarea
                        name="bio"
                        id="bio"
                        :label="__('profile.professional_summary')"
                        :placeholder="__('profile.bio_placeholder')"
                        :value="old('bio', $candidate->bio ?? '')"
                        :error="$errors->first('bio')"
                        rows="6"
                        maxlength="1000"
                        showCounter="true"
                        :hint="__('profile.bio_hint')"
                    />
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('profile.location_information') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('profile.where_you_are_located') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Country -->
                        <x-ui.select
                            name="country_id"
                            id="country_id"
                            :label="__('profile.country')"
                            :options="$countries ?? []"
                            :selected="old('country_id', $candidate->country_id ?? '')"
                            required
                            :error="$errors->first('country_id')"
                            searchable="true"
                        />

                        <!-- State/Province -->
                        <x-ui.select
                            name="state_id"
                            id="state_id"
                            :label="__('profile.state_province')"
                            :options="$states ?? []"
                            :selected="old('state_id', $candidate->state_id ?? '')"
                            :error="$errors->first('state_id')"
                            searchable="true"
                        />

                        <!-- City -->
                        <x-ui.input
                            name="city"
                            id="city"
                            :label="__('profile.city')"
                            :value="old('city', $candidate->city ?? '')"
                            :error="$errors->first('city')"
                            icon="map-pin"
                        />

                        <!-- Postal Code -->
                        <x-ui.input
                            name="postal_code"
                            id="postal_code"
                            :label="__('profile.postal_code')"
                            :value="old('postal_code', $candidate->postal_code ?? '')"
                            :error="$errors->first('postal_code')"
                            icon="map"
                        />
                    </div>

                    <!-- Address -->
                    <x-ui.textarea
                        name="address"
                        id="address"
                        :label="__('profile.address')"
                        :placeholder="__('profile.address_placeholder')"
                        :value="old('address', $candidate->address ?? '')"
                        :error="$errors->first('address')"
                        rows="3"
                    />

                    <!-- Willing to Relocate -->
                    <div class="flex items-center">
                        <input 
                            id="willing_to_relocate" 
                            name="willing_to_relocate" 
                            type="checkbox" 
                            value="1"
                            {{ old('willing_to_relocate', $candidate->willing_to_relocate ?? false) ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="willing_to_relocate" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            {{ __('profile.willing_to_relocate') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- Career Preferences -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('profile.career_preferences') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('profile.what_you_are_looking_for') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Career Level -->
                        <x-ui.select
                            name="career_level_id"
                            id="career_level_id"
                            :label="__('profile.career_level')"
                            :options="$careerLevels ?? []"
                            :selected="old('career_level_id', $candidate->career_level_id ?? '')"
                            :error="$errors->first('career_level_id')"
                        />

                        <!-- Job Type Preference -->
                        <x-ui.select
                            name="preferred_job_type_id"
                            id="preferred_job_type_id"
                            :label="__('profile.preferred_job_type')"
                            :options="$jobTypes ?? []"
                            :selected="old('preferred_job_type_id', $candidate->preferred_job_type_id ?? '')"
                            :error="$errors->first('preferred_job_type_id')"
                        />

                        <!-- Expected Salary Min -->
                        <x-ui.input
                            name="expected_salary_min"
                            id="expected_salary_min"
                            type="number"
                            :label="__('profile.expected_salary_min')"
                            :value="old('expected_salary_min', $candidate->expected_salary_min ?? '')"
                            :error="$errors->first('expected_salary_min')"
                            icon="currency-dollar"
                            step="1000"
                            min="0"
                        />

                        <!-- Expected Salary Max -->
                        <x-ui.input
                            name="expected_salary_max"
                            id="expected_salary_max"
                            type="number"
                            :label="__('profile.expected_salary_max')"
                            :value="old('expected_salary_max', $candidate->expected_salary_max ?? '')"
                            :error="$errors->first('expected_salary_max')"
                            icon="currency-dollar"
                            step="1000"
                            min="0"
                        />
                    </div>

                    <!-- Skills -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('profile.skills') }}
                        </label>
                        <x-ui.select
                            name="skills[]"
                            id="skills"
                            :options="$skills ?? []"
                            multiple="true"
                            searchable="true"
                            :selected="old('skills', $candidateSkills ?? [])"
                            :error="$errors->first('skills')"
                            :hint="__('profile.select_relevant_skills')"
                        />
                    </div>

                    <!-- Job Categories of Interest -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('profile.job_categories_interest') }}
                        </label>
                        <x-ui.select
                            name="job_categories[]"
                            id="job_categories"
                            :options="$jobCategories ?? []"
                            multiple="true"
                            searchable="true"
                            :selected="old('job_categories', $candidateCategories ?? [])"
                            :error="$errors->first('job_categories')"
                            :hint="__('profile.select_categories_interest')"
                        />
                    </div>

                    <!-- Work Preferences -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('profile.work_preferences') }}
                        </label>
                        
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="flex items-center">
                                <input 
                                    id="remote_work" 
                                    name="remote_work" 
                                    type="checkbox" 
                                    value="1"
                                    {{ old('remote_work', $candidate->remote_work ?? false) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="remote_work" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('profile.open_to_remote_work') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="part_time" 
                                    name="part_time" 
                                    type="checkbox" 
                                    value="1"
                                    {{ old('part_time', $candidate->part_time ?? false) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="part_time" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('profile.open_to_part_time') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="freelance" 
                                    name="freelance" 
                                    type="checkbox" 
                                    value="1"
                                    {{ old('freelance', $candidate->freelance ?? false) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="freelance" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('profile.open_to_freelance') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('profile.social_links') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('profile.professional_social_profiles') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- LinkedIn -->
                        <x-ui.input
                            name="linkedin_url"
                            id="linkedin_url"
                            type="url"
                            :label="__('profile.linkedin_profile')"
                            :placeholder="__('profile.linkedin_placeholder')"
                            :value="old('linkedin_url', $candidate->linkedin_url ?? '')"
                            :error="$errors->first('linkedin_url')"
                            icon="link"
                        />

                        <!-- GitHub -->
                        <x-ui.input
                            name="github_url"
                            id="github_url"
                            type="url"
                            :label="__('profile.github_profile')"
                            :placeholder="__('profile.github_placeholder')"
                            :value="old('github_url', $candidate->github_url ?? '')"
                            :error="$errors->first('github_url')"
                            icon="link"
                        />

                        <!-- Portfolio Website -->
                        <x-ui.input
                            name="portfolio_url"
                            id="portfolio_url"
                            type="url"
                            :label="__('profile.portfolio_website')"
                            :placeholder="__('profile.portfolio_placeholder')"
                            :value="old('portfolio_url', $candidate->portfolio_url ?? '')"
                            :error="$errors->first('portfolio_url')"
                            icon="link"
                        />

                        <!-- Other Website -->
                        <x-ui.input
                            name="website_url"
                            id="website_url"
                            type="url"
                            :label="__('profile.other_website')"
                            :placeholder="__('profile.website_placeholder')"
                            :value="old('website_url', $candidate->website_url ?? '')"
                            :error="$errors->first('website_url')"
                            icon="link"
                        />
                    </div>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('profile.privacy_settings') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('profile.control_profile_visibility') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('profile.public_profile') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('profile.allow_employers_to_find_you') }}
                                </p>
                            </div>
                            <input 
                                id="is_public" 
                                name="is_public" 
                                type="checkbox" 
                                value="1"
                                {{ old('is_public', $candidate->is_public ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('profile.show_contact_info') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('profile.display_email_phone_publicly') }}
                                </p>
                            </div>
                            <input 
                                id="show_contact_info" 
                                name="show_contact_info" 
                                type="checkbox" 
                                value="1"
                                {{ old('show_contact_info', $candidate->show_contact_info ?? false) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('profile.job_alerts') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('profile.receive_email_notifications') }}
                                </p>
                            </div>
                            <input 
                                id="job_alerts" 
                                name="job_alerts" 
                                type="checkbox" 
                                value="1"
                                {{ old('job_alerts', $candidate->job_alerts ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <x-ui.button 
                    href="{{ route('candidate.dashboard') }}" 
                    variant="secondary"
                >
                    {{ __('profile.cancel') }}
                </x-ui.button>

                <div class="flex space-x-3">
                    <x-ui.button 
                        type="button" 
                        variant="secondary"
                        id="save-draft"
                    >
                        {{ __('profile.save_draft') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        type="submit" 
                        variant="primary"
                        id="save-profile"
                    >
                        {{ __('profile.save_profile') }}
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
    const form = document.getElementById('profile-form');
    
    // Country/State dependency
    const countrySelect = document.getElementById('country_id');
    const stateSelect = document.getElementById('state_id');
    
    if (countrySelect && stateSelect) {
        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            
            // Clear state options
            stateSelect.innerHTML = '<option value="">{{ __("profile.select_state") }}</option>';
            
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
    
    // Salary validation
    const salaryMin = document.getElementById('expected_salary_min');
    const salaryMax = document.getElementById('expected_salary_max');
    
    function validateSalary() {
        const min = parseInt(salaryMin.value) || 0;
        const max = parseInt(salaryMax.value) || 0;
        
        if (min && max && min > max) {
            salaryMax.setCustomValidity('{{ __("profile.max_salary_must_be_greater") }}');
        } else {
            salaryMax.setCustomValidity('');
        }
    }
    
    if (salaryMin && salaryMax) {
        salaryMin.addEventListener('input', validateSalary);
        salaryMax.addEventListener('input', validateSalary);
    }
    
    // Auto-save draft functionality
    let autoSaveTimer;
    const autoSaveDelay = 30000; // 30 seconds
    
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
                console.log('Profile auto-saved');
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
        const submitButton = document.getElementById('save-profile');
        const originalText = submitButton.textContent;
        
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('profile.saving') }}...
            </div>
        `;
    });
    
    // Profile completion calculation
    function calculateCompletion() {
        const requiredFields = [
            'first_name', 'last_name', 'email', 'phone', 'professional_title', 
            'bio', 'country_id', 'city', 'career_level_id'
        ];
        
        let completed = 0;
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (element && element.value.trim()) {
                completed++;
            }
        });
        
        // Check if skills are selected
        const skills = document.getElementById('skills');
        if (skills && skills.selectedOptions.length > 0) {
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
</script>
@endpush 