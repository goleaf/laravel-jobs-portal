<x-frontend.layouts.app 
    title="{{ __('messages.welcome_to') }} {{ config('app.name') }}" 
    :seoTitle="__('messages.find_your_dream_job')"
    :seoDescription="__('messages.home_meta_description')"
    :seoKeywords="__('messages.home_meta_keywords')">
    
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    {{ __('messages.find_your_dream_job') }}
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    {{ __('messages.home_hero_subtitle') }}
                </p>
                
                <!-- Search Form -->
                <div class="max-w-4xl mx-auto">
                    <form action="{{ route('jobs.index') }}" method="GET" class="bg-white rounded -lg p-4 shadow-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-shared.components.forms.input
                                name="keyword"
                                placeholder="{{ __('messages.job_title_keyword') }}"
                                :icon="'<svg class=\'w-5 h-5 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z\'></path></svg>'"
                                class="text-gray-900"
                            />
                            
                            <x-shared.components.forms.select
                                name="location"
                                :placeholder="__('messages.select_location')"
                                :options="$locations ?? []"
                                class="text-gray-900"
                            />
                            
                            <x-shared.components.forms.button
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                {{ __('messages.search_jobs') }}
                            </x-shared.components.forms.button>
                        </div>
                    </form>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($stats['jobs'] ?? 0) }}</div>
                        <div class="text-blue-100">{{ __('messages.active_jobs') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($stats['companies'] ?? 0) }}</div>
                        <div class="text-blue-100">{{ __('messages.companies') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($stats['candidates'] ?? 0) }}</div>
                        <div class="text-blue-100">{{ __('messages.active_candidates') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Featured Jobs -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ __('messages.featured_jobs') }}</h2>
                <p class="text-lg text-gray-600">{{ __('messages.featured_jobs_subtitle') }}</p>
            </div>
            
            @if(isset($featuredJobs) && $featuredJobs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredJobs as $job)
                        <div class="rounded-md transition">
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    <img src="{{ $job->company->logo_url ?? asset('images/default-company.png') }}" 
                                         alt="{{ $job->company->name }}" 
                                         class="w-12 h-12 rounded -lg object-cover">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            <a href="{{ route('jobs.show', $job->slug) }}" class="hover:text-blue-600">
                                                {{ $job->title }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600">{{ $job->company->name }}</p>
                                        <div class="flex items-center mt-2 text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $job->location }}
                                        </div>
                                        @if($job->salary_range)
                                            <div class="flex items-center mt-1 text-sm text-green-600 font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                                {{ $job->salary_range }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $job->type }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $job->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="text-center mt-12">
                    <x-shared.components.forms.button
                        href="{{ route('jobs.index') }}"
                        variant="outline"
                        size="lg">
                        {{ __('messages.view_all_jobs') }}
                    </x-shared.components.forms.button>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded -full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.no_featured_jobs') }}</h3>
                    <p class="text-gray-500">{{ __('messages.check_back_soon') }}</p>
                </div>
            @endif
        </div>
    </section>
    
    <!-- Job Categories -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ __('messages.browse_by_category') }}</h2>
                <p class="text-lg text-gray-600">{{ __('messages.browse_categories_subtitle') }}</p>
            </div>
            
            @if(isset($categories) && $categories->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($categories as $category)
                        <a href="{{ route('jobs.index', ['category' => $category->slug]) }}" 
                           class="rounded-md transition">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded -lg mb-4 group-hover:bg-blue-200 transition-colors">
                                    @if($category->icon)
                                        {!! $category->icon !!}
                                    @else
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ $category->name }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ trans_choice('messages.job_count', $category->jobs_count, ['count' => $category->jobs_count]) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-16 bg-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">{{ __('messages.ready_to_get_started') }}</h2>
            <p class="text-xl text-blue-100 mb-8">{{ __('messages.cta_subtitle') }}</p>
            
            <div class="flex flex- flex-1 sm:flex- flex flex-wrap justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <x-shared.components.forms.button
                    href="{{ route('candidate.register') }}"
                    variant="secondary"
                    size="lg">
                    {{ __('messages.find_jobs') }}
                </x-shared.components.forms.button>
                
                <x-shared.components.forms.button
                    href="{{ route('employer.register') }}"
                    variant="outline"
                    size="lg"
                    class="border border-gray-300 -white text-white hover: bg-white hover:text-blue-600">
                    {{ __('messages.post_jobs') }}
                </x-shared.components.forms.button>
            </div>
        </div>
    </section>
</x-frontend.layouts.app> 