@extends('layouts.app')

@section('title', __('search.advanced_search'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                    {{ __('search.find_your_perfect_match') }}
                </h1>
                <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">
                    {{ __('search.advanced_search_description') }}
                </p>
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <form method="GET" action="{{ route('search.results') }}" id="advanced-search-form">
                <!-- Basic Search -->
                <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('search.basic_search') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Keywords -->
                        <x-ui.input
                            name="keywords"
                            id="keywords"
                            :label="__('search.keywords')"
                            :placeholder="__('search.keywords_placeholder')"
                            :value="request('keywords')"
                            icon="magnifying-glass"
                            :hint="__('search.keywords_hint')"
                        />

                        <!-- Search Type -->
                        <x-ui.select
                            name="search_type"
                            id="search_type"
                            :label="__('search.search_for')"
                            :options="[
                                'jobs' => __('search.jobs'),
                                'candidates' => __('search.candidates'),
                                'companies' => __('search.companies'),
                                'all' => __('search.everything')
                            ]"
                            :selected="request('search_type', 'jobs')"
                            required
                        />
                    </div>
                </div>

                <!-- Location Search -->
                <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('search.location_preferences') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Country -->
                        <x-ui.select
                            name="country_id"
                            id="country_id"
                            :label="__('search.country')"
                            :options="$countries ?? []"
                            :selected="request('country_id')"
                            searchable="true"
                        />

                        <!-- City -->
                        <x-ui.input
                            name="city"
                            id="city"
                            :label="__('search.city')"
                            :placeholder="__('search.city_placeholder')"
                            :value="request('city')"
                            icon="map-pin"
                        />

                        <!-- Remote Work -->
                        <x-ui.select
                            name="remote_option"
                            id="remote_option"
                            :label="__('search.remote_work')"
                            :options="[
                                '' => __('search.any_location_type'),
                                'yes' => __('search.remote_only'),
                                'hybrid' => __('search.hybrid_remote'),
                                'no' => __('search.onsite_only')
                            ]"
                            :selected="request('remote_option')"
                        />
                    </div>

                    <!-- Location Radius -->
                    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('search.search_radius') }}
                            </label>
                            <div class="flex items-center space-x-4">
                                <input 
                                    type="range" 
                                    name="radius" 
                                    id="radius" 
                                    min="0" 
                                    max="200" 
                                    value="{{ request('radius', 25) }}"
                                    class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                    oninput="updateRadiusDisplay(this.value)"
                                >
                                <span id="radius-display" class="text-sm font-medium text-gray-900 dark:text-white min-w-0">
                                    {{ request('radius', 25) }} km
                                </span>
                            </div>
                        </div>

                        <!-- Include Surrounding Areas -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('search.include_surrounding_areas') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('search.expand_search_nearby_cities') }}
                                </p>
                            </div>
                            <input 
                                id="include_surrounding" 
                                name="include_surrounding" 
                                type="checkbox" 
                                value="1"
                                {{ request('include_surrounding') ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>
                    </div>
                </div>

                <!-- Job-Specific Filters -->
                <div id="job-filters" class="px-6 py-6 border-b border-gray-200 dark:border-gray-700" style="display: {{ request('search_type', 'jobs') === 'jobs' ? 'block' : 'none' }};">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('search.job_preferences') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Job Category -->
                        <x-ui.select
                            name="category_id"
                            id="category_id"
                            :label="__('search.job_category')"
                            :options="$categories ?? []"
                            :selected="request('category_id')"
                            searchable="true"
                        />

                        <!-- Job Type -->
                        <x-ui.select
                            name="job_type_id"
                            id="job_type_id"
                            :label="__('search.job_type')"
                            :options="$jobTypes ?? []"
                            :selected="request('job_type_id')"
                        />

                        <!-- Experience Level -->
                        <x-ui.select
                            name="experience_level"
                            id="experience_level"
                            :label="__('search.experience_level')"
                            :options="[
                                '' => __('search.any_experience'),
                                'entry' => __('search.entry_level'),
                                'mid' => __('search.mid_level'),
                                'senior' => __('search.senior_level'),
                                'executive' => __('search.executive')
                            ]"
                            :selected="request('experience_level')"
                        />
                    </div>

                    <!-- Salary Range -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('search.salary_range') }}
                        </label>
                        
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <x-ui.input
                                name="salary_min"
                                id="salary_min"
                                type="number"
                                :label="__('search.minimum_salary')"
                                :placeholder="__('search.min_amount')"
                                :value="request('salary_min')"
                                icon="currency-dollar"
                            />

                            <x-ui.input
                                name="salary_max"
                                id="salary_max"
                                type="number"
                                :label="__('search.maximum_salary')"
                                :placeholder="__('search.max_amount')"
                                :value="request('salary_max')"
                                icon="currency-dollar"
                            />

                            <x-ui.select
                                name="salary_period"
                                id="salary_period"
                                :label="__('search.salary_period')"
                                :options="[
                                    'yearly' => __('search.per_year'),
                                    'monthly' => __('search.per_month'),
                                    'weekly' => __('search.per_week'),
                                    'daily' => __('search.per_day'),
                                    'hourly' => __('search.per_hour')
                                ]"
                                :selected="request('salary_period', 'yearly')"
                            />
                        </div>
                    </div>

                    <!-- Skills & Requirements -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('search.required_skills') }}
                        </label>
                        <div id="skills-input" class="space-y-2">
                            @if(request('skills'))
                                @foreach(explode(',', request('skills')) as $skill)
                                    <div class="skill-tag flex items-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ trim($skill) }}
                                            <button type="button" onclick="removeSkill(this)" class="ml-2 text-blue-600 hover:text-blue-500">
                                                <x-icon name="x-mark" class="h-3 w-3" />
                                            </button>
                                        </span>
                                        <input type="hidden" name="skills[]" value="{{ trim($skill) }}">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <div class="flex mt-2">
                            <input 
                                type="text" 
                                id="skill-input" 
                                placeholder="{{ __('search.type_skill_press_enter') }}"
                                class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                onkeypress="addSkill(event)"
                            >
                            <button 
                                type="button" 
                                onclick="addSkillFromInput()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                {{ __('search.add') }}
                            </button>
                        </div>
                    </div>

                    <!-- Company Size -->
                    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <x-ui.select
                            name="company_size_id"
                            id="company_size_id"
                            :label="__('search.company_size')"
                            :options="$companySizes ?? []"
                            :selected="request('company_size_id')"
                        />

                        <!-- Posted Date -->
                        <x-ui.select
                            name="posted_date"
                            id="posted_date"
                            :label="__('search.posted_date')"
                            :options="[
                                '' => __('search.any_time'),
                                'today' => __('search.today'),
                                'week' => __('search.past_week'),
                                'month' => __('search.past_month'),
                                '3months' => __('search.past_3_months')
                            ]"
                            :selected="request('posted_date')"
                        />
                    </div>
                </div>

                <!-- Candidate-Specific Filters -->
                <div id="candidate-filters" class="px-6 py-6 border-b border-gray-200 dark:border-gray-700" style="display: {{ request('search_type') === 'candidates' ? 'block' : 'none' }};">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('search.candidate_preferences') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Years of Experience -->
                        <x-ui.select
                            name="experience_years"
                            id="experience_years"
                            :label="__('search.years_of_experience')"
                            :options="[
                                '' => __('search.any_experience'),
                                '0-1' => __('search.0_1_years'),
                                '2-5' => __('search.2_5_years'),
                                '6-10' => __('search.6_10_years'),
                                '11-15' => __('search.11_15_years'),
                                '15+' => __('search.15_plus_years')
                            ]"
                            :selected="request('experience_years')"
                        />

                        <!-- Education Level -->
                        <x-ui.select
                            name="education_level"
                            id="education_level"
                            :label="__('search.education_level')"
                            :options="$educationLevels ?? []"
                            :selected="request('education_level')"
                        />

                        <!-- Availability -->
                        <x-ui.select
                            name="availability"
                            id="availability"
                            :label="__('search.availability')"
                            :options="[
                                '' => __('search.any_availability'),
                                'immediate' => __('search.immediate'),
                                '2weeks' => __('search.2_weeks_notice'),
                                '1month' => __('search.1_month_notice'),
                                '3months' => __('search.3_months_notice')
                            ]"
                            :selected="request('availability')"
                        />
                    </div>

                    <!-- Candidate Status -->
                    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <x-ui.select
                            name="candidate_status"
                            id="candidate_status"
                            :label="__('search.candidate_status')"
                            :options="[
                                '' => __('search.any_status'),
                                'actively_looking' => __('search.actively_looking'),
                                'open_to_offers' => __('search.open_to_offers'),
                                'not_looking' => __('search.not_looking')
                            ]"
                            :selected="request('candidate_status')"
                        />

                        <!-- Expected Salary -->
                        <x-ui.input
                            name="expected_salary_max"
                            id="expected_salary_max"
                            type="number"
                            :label="__('search.max_expected_salary')"
                            :placeholder="__('search.maximum_budget')"
                            :value="request('expected_salary_max')"
                            icon="currency-dollar"
                        />
                    </div>
                </div>

                <!-- Company-Specific Filters -->
                <div id="company-filters" class="px-6 py-6 border-b border-gray-200 dark:border-gray-700" style="display: {{ request('search_type') === 'companies' ? 'block' : 'none' }};">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('search.company_preferences') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Industry -->
                        <x-ui.select
                            name="industry_id"
                            id="industry_id"
                            :label="__('search.industry')"
                            :options="$industries ?? []"
                            :selected="request('industry_id')"
                            searchable="true"
                        />

                        <!-- Company Size -->
                        <x-ui.select
                            name="company_size_filter"
                            id="company_size_filter"
                            :label="__('search.company_size')"
                            :options="$companySizes ?? []"
                            :selected="request('company_size_filter')"
                        />

                        <!-- Founded Year -->
                        <x-ui.select
                            name="founded_year_range"
                            id="founded_year_range"
                            :label="__('search.founded_year')"
                            :options="[
                                '' => __('search.any_year'),
                                '2020+' => __('search.after_2020'),
                                '2010-2019' => __('search.2010_2019'),
                                '2000-2009' => __('search.2000_2009'),
                                '1990-1999' => __('search.1990_1999'),
                                'before_1990' => __('search.before_1990')
                            ]"
                            :selected="request('founded_year_range')"
                        />
                    </div>
                </div>

                <!-- Advanced Options -->
                <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('search.advanced_options') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Sort By -->
                        <x-ui.select
                            name="sort_by"
                            id="sort_by"
                            :label="__('search.sort_by')"
                            :options="[
                                'relevance' => __('search.relevance'),
                                'date_desc' => __('search.newest_first'),
                                'date_asc' => __('search.oldest_first'),
                                'salary_desc' => __('search.highest_salary'),
                                'salary_asc' => __('search.lowest_salary'),
                                'rating_desc' => __('search.highest_rated'),
                                'popularity' => __('search.most_popular')
                            ]"
                            :selected="request('sort_by', 'relevance')"
                        />

                        <!-- Results Per Page -->
                        <x-ui.select
                            name="per_page"
                            id="per_page"
                            :label="__('search.results_per_page')"
                            :options="[
                                '10' => '10 ' . __('search.results'),
                                '25' => '25 ' . __('search.results'),
                                '50' => '50 ' . __('search.results'),
                                '100' => '100 ' . __('search.results')
                            ]"
                            :selected="request('per_page', '25')"
                        />
                    </div>

                    <!-- Search Preferences -->
                    <div class="mt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('search.ai_powered_matching') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('search.use_ai_improve_results') }}
                                </p>
                            </div>
                            <input 
                                id="ai_matching" 
                                name="ai_matching" 
                                type="checkbox" 
                                value="1"
                                {{ request('ai_matching', true) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('search.include_similar_results') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('search.expand_search_similar_matches') }}
                                </p>
                            </div>
                            <input 
                                id="include_similar" 
                                name="include_similar" 
                                type="checkbox" 
                                value="1"
                                {{ request('include_similar', true) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('search.save_search_preferences') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('search.remember_filters_future') }}
                                </p>
                            </div>
                            <input 
                                id="save_preferences" 
                                name="save_preferences" 
                                type="checkbox" 
                                value="1"
                                {{ request('save_preferences') ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                        </div>
                    </div>
                </div>

                <!-- Search Actions -->
                <div class="px-6 py-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0 sm:space-x-4">
                        <div class="flex space-x-3">
                            <x-ui.button 
                                type="submit" 
                                variant="primary"
                                icon="magnifying-glass"
                                id="search-button"
                            >
                                {{ __('search.search_now') }}
                            </x-ui.button>
                            
                            <x-ui.button 
                                type="button" 
                                variant="secondary"
                                onclick="clearAllFilters()"
                            >
                                {{ __('search.clear_all') }}
                            </x-ui.button>
                        </div>

                        <div class="flex space-x-3">
                            @auth
                                <x-ui.button 
                                    type="button" 
                                    variant="ghost"
                                    icon="bookmark"
                                    onclick="saveSearch()"
                                >
                                    {{ __('search.save_search') }}
                                </x-ui.button>
                            @endauth
                            
                            <x-ui.button 
                                href="{{ route('search.saved') }}" 
                                variant="ghost"
                                icon="folder"
                            >
                                {{ __('search.saved_searches') }}
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Search Suggestions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('search.popular_searches') }}
                </h3>
            </div>
            
            <div class="px-6 py-6">
                <div class="flex flex-wrap gap-3">
                    @foreach($popularSearches ?? [] as $search)
                        <a href="{{ route('search.results', $search['params']) }}" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <x-icon name="magnifying-glass" class="h-4 w-4 mr-2" />
                            {{ $search['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Search Modal -->
<div id="save-search-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('search.save_search') }}
                </h3>
                <button onclick="hideSaveSearchModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-icon name="x-mark" class="h-6 w-6" />
                </button>
            </div>

            <!-- Save Form -->
            <form action="{{ route('search.save') }}" method="POST" id="save-search-form">
                @csrf
                <input type="hidden" name="search_params" id="search-params-input">
                
                <div class="space-y-4">
                    <!-- Search Name -->
                    <x-ui.input
                        name="name"
                        id="save_search_name"
                        :label="__('search.search_name')"
                        :placeholder="__('search.search_name_placeholder')"
                        required
                    />

                    <!-- Email Alerts -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('search.email_alerts') }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('search.get_notified_new_matches') }}
                            </p>
                        </div>
                        <input 
                            id="email_alerts" 
                            name="email_alerts" 
                            type="checkbox" 
                            value="1"
                            checked
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                    </div>

                    <!-- Alert Frequency -->
                    <x-ui.select
                        name="alert_frequency"
                        id="alert_frequency"
                        :label="__('search.alert_frequency')"
                        :options="[
                            'daily' => __('search.daily'),
                            'weekly' => __('search.weekly'),
                            'monthly' => __('search.monthly')
                        ]"
                        :selected="'weekly'"
                    />
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <x-ui.button 
                        type="button" 
                        variant="secondary"
                        onclick="hideSaveSearchModal()"
                    >
                        {{ __('search.cancel') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        type="submit" 
                        variant="primary"
                    >
                        {{ __('search.save_search') }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search type change handler
    const searchTypeSelect = document.getElementById('search_type');
    if (searchTypeSelect) {
        searchTypeSelect.addEventListener('change', function() {
            toggleSearchFilters(this.value);
        });
    }
    
    // Auto-submit on certain filter changes
    const autoSubmitFields = ['country_id', 'category_id', 'job_type_id'];
    autoSubmitFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() {
                if (this.value) {
                    document.getElementById('advanced-search-form').submit();
                }
            });
        }
    });
});

function toggleSearchFilters(searchType) {
    const jobFilters = document.getElementById('job-filters');
    const candidateFilters = document.getElementById('candidate-filters');
    const companyFilters = document.getElementById('company-filters');
    
    // Hide all filters first
    if (jobFilters) jobFilters.style.display = 'none';
    if (candidateFilters) candidateFilters.style.display = 'none';
    if (companyFilters) companyFilters.style.display = 'none';
    
    // Show relevant filters
    if (searchType === 'jobs' && jobFilters) {
        jobFilters.style.display = 'block';
    } else if (searchType === 'candidates' && candidateFilters) {
        candidateFilters.style.display = 'block';
    } else if (searchType === 'companies' && companyFilters) {
        companyFilters.style.display = 'block';
    } else if (searchType === 'all') {
        // Show all filters for comprehensive search
        if (jobFilters) jobFilters.style.display = 'block';
        if (candidateFilters) candidateFilters.style.display = 'block';
        if (companyFilters) companyFilters.style.display = 'block';
    }
}

function updateRadiusDisplay(value) {
    document.getElementById('radius-display').textContent = value + ' km';
}

function addSkill(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        addSkillFromInput();
    }
}

function addSkillFromInput() {
    const input = document.getElementById('skill-input');
    const skill = input.value.trim();
    
    if (skill && !isSkillAlreadyAdded(skill)) {
        const skillsContainer = document.getElementById('skills-input');
        
        const skillDiv = document.createElement('div');
        skillDiv.className = 'skill-tag flex items-center';
        skillDiv.innerHTML = `
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                ${skill}
                <button type="button" onclick="removeSkill(this)" class="ml-2 text-blue-600 hover:text-blue-500">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
            <input type="hidden" name="skills[]" value="${skill}">
        `;
        
        skillsContainer.appendChild(skillDiv);
        input.value = '';
    }
}

function removeSkill(button) {
    button.closest('.skill-tag').remove();
}

function isSkillAlreadyAdded(skill) {
    const existingSkills = document.querySelectorAll('input[name="skills[]"]');
    for (let skillInput of existingSkills) {
        if (skillInput.value.toLowerCase() === skill.toLowerCase()) {
            return true;
        }
    }
    return false;
}

function clearAllFilters() {
    if (confirm('{{ __("search.confirm_clear_all_filters") }}')) {
        const form = document.getElementById('advanced-search-form');
        
        // Clear all inputs
        form.querySelectorAll('input[type="text"], input[type="number"], input[type="email"]').forEach(input => {
            input.value = '';
        });
        
        // Reset all selects to first option
        form.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });
        
        // Uncheck all checkboxes
        form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset range sliders
        form.querySelectorAll('input[type="range"]').forEach(range => {
            range.value = range.min;
            if (range.id === 'radius') {
                updateRadiusDisplay(range.value);
            }
        });
        
        // Clear skills
        document.getElementById('skills-input').innerHTML = '';
        
        // Submit form to show all results
        form.submit();
    }
}

function saveSearch() {
    const form = document.getElementById('advanced-search-form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    
    document.getElementById('search-params-input').value = params;
    document.getElementById('save-search-modal').classList.remove('hidden');
    
    // Generate suggested name
    const keywords = formData.get('keywords');
    const searchType = formData.get('search_type');
    const city = formData.get('city');
    
    let suggestedName = '';
    if (keywords) suggestedName += keywords + ' ';
    if (searchType && searchType !== 'all') suggestedName += searchType + ' ';
    if (city) suggestedName += 'in ' + city;
    
    if (suggestedName) {
        document.getElementById('save_search_name').value = suggestedName.trim();
    }
}

function hideSaveSearchModal() {
    document.getElementById('save-search-modal').classList.add('hidden');
}

// Form submission with loading state
document.getElementById('advanced-search-form').addEventListener('submit', function() {
    const submitButton = document.getElementById('search-button');
    const originalText = submitButton.textContent;
    
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('search.searching') }}...
        </div>
    `;
});
</script>
@endpush 