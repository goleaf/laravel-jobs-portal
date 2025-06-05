@props([
    'job',
    'featured' => false,
    'layout' => 'default' // default, compact, detailed
])

@php
    $cardClasses = [
        'default' => 'bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700',
        'compact' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 dark:border-gray-700',
        'detailed' => 'bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700'
    ];
    
    $cardClass = $cardClasses[$layout] ?? $cardClasses['default'];
@endphp

<div class="{{ $cardClass }}">
    <div class="p-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-start space-x-4 flex-1">
                <!-- Company Logo -->
                @if($job->company && $job->company->company_url)
                    <div class="flex-shrink-0">
                        <img 
                            src="{{ $job->company->company_url }}" 
                            alt="{{ $job->company->name ?? __('jobs.company_logo') }}"
                            class="w-12 h-12 rounded -lg object-cover ring-2 ring-gray-100 dark:ring-gray-700"
                            loading="lazy"
                        >
                    </div>
                @endif
                
                <!-- Job Title and Company -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-gray-900 dark: text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors line-clamp-2">
                        <a href="{{ route('front.', $job->job_id) }}" 
                           class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 rounded">
                            {{ html_entity_decode($job->job_title) }}
                        </a>
                    </h3>
                    
                    @if($job->company)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $job->company->name }}
                        </p>
                    @endif
                    
                    <!-- Job Type Badge -->
                    @if($job->jobShift && $job->jobShift->shift)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mt-2">
                            {{ $job->jobShift->shift }}
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Featured Badge -->
            @if($featured || ($job->activeFeatured ?? false))
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        {{ __('jobs.featured') }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Job Details -->
        <div class="space-y-3 mb-4">
            <!-- Location -->
            @if($job->full_location)
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="truncate">{{ $job->full_location }}</span>
                </div>
            @endif

            <!-- Salary Range -->
            @if($job->salary_from && $job->salary_to && $job->currency)
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <span class="font-medium text-green-600 dark:text-green-400">
                        {{ $job->currency->currency_icon }}{{ number_format($job->salary_from) }} - {{ $job->currency->currency_icon }}{{ number_format($job->salary_to) }}
                    </span>
                </div>
            @endif

            <!-- Job Category -->
            @if($job->jobCategory)
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                    </svg>
                    <span>{{ $job->jobCategory->name }}</span>
                </div>
            @endif

            <!-- Skills -->
            @if($job->jobsSkill && $job->jobsSkill->count() > 0)
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <div class="flex flex-wrap gap-1">
                        @foreach($job->jobsSkill->take(2) as $skill)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                {{ $skill->name }}
                            </span>
                        @endforeach
                        @if($job->jobsSkill->count() > 2)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                +{{ $job->jobsSkill->count() - 2 }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark: border border border-gray-300 -gray-300 -gray-700">
            <!-- Posted Time -->
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $job->created_at->diffForHumans() }}
            </span>
            
            <!-- Action Button -->
            <a href="{{ route('front.', $job->job_id) }}" 
               class="border border-gray-300 bg-transparent">
                {{ __('jobs.view_details') }}
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</div> 