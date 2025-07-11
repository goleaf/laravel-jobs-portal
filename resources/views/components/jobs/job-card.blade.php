<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 p-6">
    @if($layout === 'list')
    <div class="flex items-start space-x-4">
        <div class="flex-shrink-0">
            @if($job->company->logo)
                <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $job->company->logo) }}" alt="{{ $job->company->name }} logo">
            @else
                <div class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 text-xl font-semibold">
                    {{ substr($job->company->name, 0, 1) }}
                </div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                <a href="{{ route('jobs.show', $job->slug) }}" class="hover:text-blue-600 transition-colors duration-200">
                    {{ $job->title }}
                </a>
            </h3>
            <p class="text-md text-gray-600 dark:text-gray-400 mb-2">
                <a href="{{ route('companies.show', $job->company->slug) }}" class="hover:text-blue-600 transition-colors duration-200">
                    {{ $job->company->name }}
                </a>
            </p>
            <div class="flex flex-wrap items-center text-sm text-gray-500 dark:text-gray-400">
                <span class="flex items-center mr-4 mb-1">
                    <x-icon name="map-pin" class="h-4 w-4 mr-1 text-gray-400" />
                    {{ $job->location }}
                </span>
                <span class="flex items-center mr-4 mb-1">
                    <x-icon name="briefcase" class="h-4 w-4 mr-1 text-gray-400" />
                    {{ $job->job_type->name }}
                </span>
                @if($job->salary)
                <span class="flex items-center mb-1">
                    <x-icon name="currency-dollar" class="h-4 w-4 mr-1 text-gray-400" />
                    {{ $job->salary }}
                </span>
                @endif
            </div>
            <p class="text-gray-700 dark:text-gray-300 mt-3 text-sm line-clamp-2">
                {{ strip_tags($job->description) }}
            </p>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('jobs.posted') }} {{ $job->created_at->diffForHumans() }}
                </span>
                <x-button href="{{ route('jobs.show', $job->slug) }}" variant="secondary" class="px-4 py-2 text-sm">
                    {{ __('jobs.view_job') }}
                </x-button>
            </div>
        </div>
    </div>
    @else {{-- Grid Layout --}}
    <div class="flex flex-col h-full">
        <div class="flex-shrink-0 text-center mb-4">
            @if($job->company->logo)
                <img class="h-20 w-20 rounded-full object-cover mx-auto" src="{{ asset('storage/' . $job->company->logo) }}" alt="{{ $job->company->name }} logo">
            @else
                <div class="h-20 w-20 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 text-2xl font-semibold mx-auto">
                    {{ substr($job->company->name, 0, 1) }}
                </div>
            @endif
        </div>
        <div class="flex-1 text-center">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                <a href="{{ route('jobs.show', $job->slug) }}" class="hover:text-blue-600 transition-colors duration-200">
                    {{ $job->title }}
                </a>
            </h3>
            <p class="text-md text-gray-600 dark:text-gray-400 mb-2">
                <a href="{{ route('companies.show', $job->company->slug) }}" class="hover:text-blue-600 transition-colors duration-200">
                    {{ $job->company->name }}
                </a>
            </p>
            <div class="flex flex-wrap justify-center items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <span class="flex items-center mx-2 mb-1">
                    <x-icon name="map-pin" class="h-4 w-4 mr-1 text-gray-400" />
                    {{ $job->location }}
                </span>
                <span class="flex items-center mx-2 mb-1">
                    <x-icon name="briefcase" class="h-4 w-4 mr-1 text-gray-400" />
                    {{ $job->job_type->name }}
                </span>
                @if($job->salary)
                <span class="flex items-center mx-2 mb-1">
                    <x-icon name="currency-dollar" class="h-4 w-4 mr-1 text-gray-400" />
                    {{ $job->salary }}
                </span>
                @endif
            </div>
            <p class="text-gray-700 dark:text-gray-300 text-sm line-clamp-3">
                {{ strip_tags($job->description) }}
            </p>
        </div>
        <div class="mt-4 flex flex-col items-center">
            <span class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                {{ __('jobs.posted') }} {{ $job->created_at->diffForHumans() }}
            </span>
            <x-button href="{{ route('jobs.show', $job->slug) }}" variant="primary" class="px-6 py-3 w-full">
                {{ __('jobs.view_job') }}
            </x-button>
        </div>
    </div>
    @endif
</div> 