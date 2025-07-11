{{-- Placeholder for jobs sidebar filters --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
        {{ __('jobs.filter_by') }}
    </h3>

    <form action="{{ route('jobs.index') }}" method="GET" id="sidebar-filter-form">
        @foreach($filters as $key => $value)
            @if(!in_array($key, ['category', 'location', 'company', 'salary_min', 'salary_max', 'job_type']))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <!-- Category Filter -->
        <div class="mb-6">
            <label for="category-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('jobs.category') }}
            </label>
            <x-ui.select name="category" id="category-filter" onchange="document.getElementById('sidebar-filter-form').submit()">
                <option value="">{{ __('jobs.select_category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </x-ui.select>
        </div>

        <!-- Location Filter -->
        <div class="mb-6">
            <label for="location-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('jobs.location') }}
            </label>
            <x-ui.select name="location" id="location-filter" onchange="document.getElementById('sidebar-filter-form').submit()">
                <option value="">{{ __('jobs.select_location') }}</option>
                @foreach($locations as $location)
                    <option value="{{ $location->slug }}" {{ request('location') === $location->slug ? 'selected' : '' }}>
                        {{ $location->name }}
                    </option>
                @endforeach
            </x-ui.select>
        </div>

        <!-- Company Filter -->
        <div class="mb-6">
            <label for="company-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('jobs.company') }}
            </label>
            <x-ui.select name="company" id="company-filter" onchange="document.getElementById('sidebar-filter-form').submit()">
                <option value="">{{ __('jobs.select_company') }}</option>
                @foreach($companies as $company)
                    <option value="{{ $company->slug }}" {{ request('company') === $company->slug ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </x-ui.select>
        </div>

        <!-- Salary Range Filter (Conceptual) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('jobs.salary_range') }}
            </label>
            <div class="flex items-center space-x-2">
                <x-input 
                    type="number" 
                    name="salary_min"
                    placeholder="{{ __('jobs.min') }}"
                    value="{{ request('salary_min') }}"
                    class="w-1/2"
                />
                <span class="text-gray-500 dark:text-gray-400">-</span>
                <x-input 
                    type="number" 
                    name="salary_max"
                    placeholder="{{ __('jobs.max') }}"
                    value="{{ request('salary_max') }}"
                    class="w-1/2"
                />
            </div>
        </div>

        <!-- Job Type Filter (Checkboxes) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('jobs.job_type') }}
            </label>
            @foreach($jobTypes as $jobType)
                <div class="flex items-center mb-2">
                    <input 
                        type="checkbox" 
                        name="job_type[]"
                        value="{{ $jobType->slug }}"
                        id="job-type-{{ $jobType->slug }}"
                        class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out rounded dark:bg-gray-700 dark:border-gray-600"
                        {{ in_array($jobType->slug, request('job_type', [])) ? 'checked' : '' }}
                        onchange="document.getElementById('sidebar-filter-form').submit()"
                    >
                    <label for="job-type-{{ $jobType->slug }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        {{ $jobType->name }}
                    </label>
                </div>
            @endforeach
        </div>

        <x-button type="submit" variant="primary" class="w-full">
            {{ __('jobs.apply_filters') }}
        </x-button>
        <x-button type="button" variant="secondary" class="w-full mt-2" onclick="window.location.href='{{ route('jobs.index') }}'">
            {{ __('jobs.clear_filters') }}
        </x-button>
    </form>
</div> 