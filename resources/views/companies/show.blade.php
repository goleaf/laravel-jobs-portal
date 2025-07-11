@extends('layouts.app')

@section('title', $company->name . ' - ' . __('company.company_profile'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Company Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <!-- Cover Photo -->
        @if($company->cover_photo)
            <div class="h-64 bg-cover bg-center relative" style="background-image: url('{{ $company->cover_photo }}')">
                <div class="absolute inset-0 bg-black bg-opacity-30"></div>
            </div>
        @else
            <div class="h-64 bg-gradient-to-r from-blue-500 to-purple-600"></div>
        @endif
        
        <!-- Company Info -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-16 sm:-mt-24">
                <div class="sm:flex sm:items-end sm:space-x-5">
                    <!-- Company Logo -->
                    <div class="flex">
                        @if($company->logo)
                            <img class="h-24 w-24 sm:h-32 sm:w-32 rounded-lg border-4 border-white dark:border-gray-800 bg-white" src="{{ $company->logo }}" alt="{{ $company->name }}">
                        @else
                            <div class="h-24 w-24 sm:h-32 sm:w-32 rounded-lg border-4 border-white dark:border-gray-800 bg-white flex items-center justify-center">
                                <x-icon name="building-office" class="h-12 w-12 sm:h-16 sm:w-16 text-gray-400" />
                            </div>
                        @endif
                    </div>
                    
                    <!-- Company Details -->
                    <div class="mt-6 sm:flex-1 sm:min-w-0 sm:flex sm:items-center sm:justify-end sm:space-x-6 sm:pb-1">
                        <div class="sm:hidden md:block mt-6 min-w-0 flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">
                                {{ $company->name }}
                            </h1>
                            
                            <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
                                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <x-icon name="building-office" class="flex-shrink-0 mr-1.5 h-5 w-5" />
                                    {{ $company->industry->name ?? __('company.industry_not_specified') }}
                                </div>
                                
                                @if($company->location)
                                    <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <x-icon name="map-pin" class="flex-shrink-0 mr-1.5 h-5 w-5" />
                                        {{ $company->location }}
                                    </div>
                                @endif
                                
                                @if($company->company_size)
                                    <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <x-icon name="users" class="flex-shrink-0 mr-1.5 h-5 w-5" />
                                        {{ $company->company_size->name }}
                                    </div>
                                @endif
                                
                                @if($company->founded_year)
                                    <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <x-icon name="calendar" class="flex-shrink-0 mr-1.5 h-5 w-5" />
                                        {{ __('company.founded_in') }} {{ $company->founded_year }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-6 flex flex-col justify-stretch space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
                            <x-ui.button 
                                href="{{ route('companies.follow', $company) }}" 
                                variant="primary"
                                icon="heart"
                                id="follow-button"
                            >
                                {{ __('company.follow') }}
                            </x-ui.button>
                            
                            @if($company->website)
                                <x-ui.button 
                                    href="{{ $company->website }}" 
                                    target="_blank"
                                    variant="secondary"
                                    icon="globe-alt"
                                >
                                    {{ __('company.visit_website') }}
                                </x-ui.button>
                            @endif
                            
                            <x-ui.button 
                                href="{{ route('companies.jobs', $company) }}" 
                                variant="secondary"
                                icon="briefcase"
                            >
                                {{ __('company.view_jobs') }} ({{ $company->active_jobs_count ?? 0 }})
                            </x-ui.button>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Company Name -->
                <div class="hidden sm:block md:hidden mt-6 min-w-0 flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">
                        {{ $company->name }}
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Stats -->
    <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $company->active_jobs_count ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('company.active_jobs') }}
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $company->followers_count ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('company.followers') }}
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $company->employees_count ?? ($company->company_size->average ?? 'N/A') }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('company.employees') }}
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $company->reviews_average ? number_format($company->reviews_average, 1) : 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('company.rating') }}
                        @if($company->reviews_count)
                            ({{ $company->reviews_count }})
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Main Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Company -->
                @if($company->description)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('company.about_company') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $company->description }}</p>
                        </div>
                    </div>
                @endif

                <!-- Mission & Values -->
                @if($company->mission || $company->values)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('company.mission_values') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6 space-y-6">
                            @if($company->mission)
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">
                                        {{ __('company.our_mission') }}
                                    </h4>
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $company->mission }}</p>
                                </div>
                            @endif
                            
                            @if($company->values)
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">
                                        {{ __('company.our_values') }}
                                    </h4>
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $company->values }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Work Environment -->
                @if($company->work_environment)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('company.work_environment') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $company->work_environment }}</p>
                            
                            @if($company->remote_policy)
                                <div class="mt-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $company->remote_policy === 'remote' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $company->remote_policy === 'hybrid' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                        {{ $company->remote_policy === 'onsite' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                        {{ $company->remote_policy === 'flexible' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                    ">
                                        <x-icon name="map-pin" class="h-4 w-4 mr-1" />
                                        {{ __('company.remote_policy.' . $company->remote_policy) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Benefits & Perks -->
                @if($company->benefits && count($company->benefits) > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('company.benefits_perks') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach($company->benefits as $benefit)
                                    <div class="flex items-center">
                                        <x-icon name="check-circle" class="h-5 w-5 text-green-500 mr-2" />
                                        <span class="text-gray-700 dark:text-gray-300">{{ $benefit }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Latest Jobs -->
                @if($latestJobs && $latestJobs->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ __('company.latest_jobs') }}
                                </h3>
                                <x-ui.button 
                                    href="{{ route('companies.jobs', $company) }}" 
                                    variant="ghost" 
                                    size="sm"
                                >
                                    {{ __('company.view_all_jobs') }}
                                </x-ui.button>
                            </div>
                        </div>
                        
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($latestJobs as $job)
                                <div class="px-6 py-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $job->title }}
                                                </a>
                                            </h4>
                                            
                                            <div class="mt-1 flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                                <span>{{ $job->location }}</span>
                                                @if($job->remote_option !== 'no')
                                                    <span>•</span>
                                                    <span class="text-green-600 dark:text-green-400">
                                                        {{ $job->remote_option === 'yes' ? __('jobs.remote') : __('jobs.hybrid') }}
                                                    </span>
                                                @endif
                                                <span>•</span>
                                                <span>{{ $job->job_type->name ?? __('jobs.full_time') }}</span>
                                            </div>
                                            
                                            @if($job->description)
                                                <p class="mt-2 text-gray-700 dark:text-gray-300 line-clamp-2">
                                                    {{ Str::limit(strip_tags($job->description), 150) }}
                                                </p>
                                            @endif
                                            
                                            <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span>{{ __('jobs.posted') }} {{ $job->created_at->diffForHumans() }}</span>
                                                
                                                @if($job->deadline)
                                                    <span>•</span>
                                                    <span class="{{ $job->deadline->isPast() ? 'text-red-600 dark:text-red-400' : '' }}">
                                                        {{ __('jobs.deadline') }}: {{ $job->deadline->format('M d, Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="ml-4 flex flex-col items-end space-y-2">
                                            @if($job->is_featured)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    <x-icon name="star" class="h-3 w-3 mr-1" />
                                                    {{ __('jobs.featured') }}
                                                </span>
                                            @endif
                                            
                                            <x-ui.button 
                                                href="{{ route('jobs.show', $job) }}" 
                                                variant="primary" 
                                                size="sm"
                                            >
                                                {{ __('jobs.view_job') }}
                                            </x-ui.button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Company Team (if available) -->
                @if($teamMembers && $teamMembers->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('company.meet_the_team') }}
                            </h3>
                        </div>
                        
                        <div class="px-6 py-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($teamMembers as $member)
                                    <div class="text-center">
                                        @if($member->avatar)
                                            <img class="mx-auto h-16 w-16 rounded-full" src="{{ $member->avatar }}" alt="{{ $member->name }}">
                                        @else
                                            <div class="mx-auto h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <x-icon name="building-office" class="h-8 w-8 text-gray-500 dark:text-gray-400" />
                                            </div>
                                        @endif
                                        
                                        <h4 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $member->name }}
                                        </h4>
                                        
                                        @if($member->position)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $member->position }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="mt-8 lg:mt-0 space-y-8">
                <!-- Company Details -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('company.company_details') }}
                        </h3>
                    </div>
                    
                    <div class="px-6 py-6 space-y-4">
                        @if($company->industry)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('company.industry') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $company->industry->name }}
                                </dd>
                            </div>
                        @endif

                        @if($company->company_size)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('company.company_size') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $company->company_size->name }}
                                </dd>
                            </div>
                        @endif

                        @if($company->founded_year)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('company.founded') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $company->founded_year }}
                                </dd>
                            </div>
                        @endif

                        @if($company->location)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('company.headquarters') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $company->location }}
                                </dd>
                            </div>
                        @endif

                        @if($company->additional_locations && count($company->additional_locations) > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('company.other_locations') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <ul class="space-y-1">
                                        @foreach($company->additional_locations as $location)
                                            <li>{{ $location }}</li>
                                        @endforeach
                                    </ul>
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Social Links -->
                @if($company->hasAnySocialLinks())
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('company.connect_with_us') }}
                            </h3>
                        </div>
                        
                        <div class="px-6 py-6">
                            <div class="flex justify-center space-x-4">
                                @if($company->linkedin_url)
                                    <a href="{{ $company->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                        <span class="sr-only">{{ __('social.linkedin') }}</span>
                                        <x-icon name="linkedin" class="h-6 w-6" />
                                    </a>
                                @endif
                                
                                @if($company->twitter_url)
                                    <a href="{{ $company->twitter_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-blue-400 dark:hover:text-blue-300">
                                        <span class="sr-only">{{ __('social.twitter') }}</span>
                                        <x-icon name="twitter" class="h-6 w-6" />
                                    </a>
                                @endif
                                
                                @if($company->facebook_url)
                                    <a href="{{ $company->facebook_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-blue-800 dark:hover:text-blue-600">
                                        <span class="sr-only">{{ __('social.facebook') }}</span>
                                        <x-icon name="facebook" class="h-6 w-6" />
                                    </a>
                                @endif
                                
                                @if($company->instagram_url)
                                    <a href="{{ $company->instagram_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-pink-600 dark:hover:text-pink-400">
                                        <span class="sr-only">{{ __('social.instagram') }}</span>
                                        <x-icon name="instagram" class="h-6 w-6" />
                                    </a>
                                @endif
                                
                                @if($company->youtube_url)
                                    <a href="{{ $company->youtube_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                                        <span class="sr-only">{{ __('social.youtube') }}</span>
                                        <x-icon name="youtube" class="h-6 w-6" />
                                    </a>
                                @endif
                                
                                @if($company->github_url)
                                    <a href="{{ $company->github_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                        <span class="sr-only">{{ __('social.github') }}</span>
                                        <x-icon name="github" class="h-6 w-6" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('company.contact_information') }}
                        </h3>
                    </div>
                    
                    <div class="px-6 py-6 space-y-4">
                        @if($company->website)
                            <div class="flex items-center">
                                <x-icon name="globe-alt" class="h-5 w-5 text-gray-400 mr-3" />
                                <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                    {{ $company->website }}
                                </a>
                            </div>
                        @endif
                        
                        @if($company->phone)
                            <div class="flex items-center">
                                <x-icon name="phone" class="h-5 w-5 text-gray-400 mr-3" />
                                <a href="tel:{{ $company->phone }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 text-sm">
                                    {{ $company->phone }}
                                </a>
                            </div>
                        @endif
                        
                        @if($company->address)
                            <div class="flex items-start">
                                <x-icon name="map-pin" class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                <span class="text-gray-700 dark:text-gray-300 text-sm">
                                    {{ $company->address }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Similar Companies -->
                @if($similarCompanies && $similarCompanies->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('company.similar_companies') }}
                            </h3>
                        </div>
                        
                        <div class="px-6 py-6 space-y-4">
                            @foreach($similarCompanies as $similarCompany)
                                <div class="flex items-center space-x-3">
                                    @if($similarCompany->logo)
                                        <img class="h-10 w-10 rounded-lg" src="{{ $similarCompany->logo }}" alt="{{ $similarCompany->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <x-icon name="building-office" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            <a href="{{ route('companies.show', $similarCompany) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $similarCompany->name }}
                                            </a>
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $similarCompany->active_jobs_count ?? 0 }} {{ __('company.open_positions') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Follow/Unfollow functionality
    const followButton = document.getElementById('follow-button');
    
    if (followButton) {
        followButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.href;
            const isFollowing = this.textContent.trim() === '{{ __("company.unfollow") }}';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button text and icon
                    if (data.following) {
                        this.innerHTML = '<x-icon name="heart" class="h-4 w-4 mr-2" />{{ __("company.unfollow") }}';
                        this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        this.classList.add('bg-red-600', 'hover:bg-red-700');
                    } else {
                        this.innerHTML = '<x-icon name="heart" class="h-4 w-4 mr-2" />{{ __("company.follow") }}';
                        this.classList.remove('bg-red-600', 'hover:bg-red-700');
                        this.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    }
                    
                    // Update followers count
                    const followersCount = document.querySelector('[data-followers-count]');
                    if (followersCount) {
                        const currentCount = parseInt(followersCount.textContent);
                        followersCount.textContent = data.following ? currentCount + 1 : currentCount - 1;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("company.follow_error") }}');
            });
        });
    }
    
    // Track company profile view
    fetch('{{ route("companies.track-view", $company) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).catch(error => console.error('Error tracking profile view:', error));
});
</script>
@endpush 