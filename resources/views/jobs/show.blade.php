@extends('layouts.app')

@section('title', $job->title . ' - ' . $job->company->name)
@section('description', Str::limit(strip_tags($job->description), 160))
@section('keywords', implode(', ', array_merge([$job->title, $job->company->name], $job->skills->pluck('name')->toArray())))

@push('styles')
<style>
    .job-description h1, .job-description h2, .job-description h3 { @apply font-bold mb-4; }
    .job-description h1 { @apply text-xl; }
    .job-description h2 { @apply text-lg; }
    .job-description h3 { @apply text-base; }
    .job-description p { @apply mb-4; }
    .job-description ul, .job-description ol { @apply mb-4 pl-6; }
    .job-description ul { @apply list-disc; }
    .job-description ol { @apply list-decimal; }
    .job-description li { @apply mb-2; }
    .job-description a { @apply text-blue-600 hover:text-blue-800 underline; }
    .job-description strong { @apply font-semibold; }
    .job-description em { @apply italic; }
</style>
@endpush

@section('content')
<!-- Job Header -->
<div class="bg-white dark:bg-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:flex lg:items-start lg:justify-between">
            <div class="flex-1 min-w-0">
                <!-- Company Info -->
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <img 
                            class="h-20 w-20 rounded-full object-cover shadow-sm border border-gray-200 dark:border-gray-700" 
                            src="{{ $job->company->logo_url }}" 
                            alt="{{ $job->company->name }}"
                            onerror="this.src='{{ asset('images/default-company.png') }}'"
                        >
                    </div>
                    <div class="ml-5">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight mb-1">
                            <a href="{{ route('companies.show', $job->company) }}" class="hover:text-blue-600 transition-colors duration-200">
                                {{ $job->company->name }}
                            </a>
                        </h2>
                        <p class="text-lg text-gray-500 dark:text-gray-400 flex items-center">
                            <x-icon name="map-pin" class="inline h-5 w-5 mr-1 text-gray-400" />
                            {{ $job->location_display }}
                        </p>
                    </div>
                </div>

                <!-- Job Title -->
                <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl leading-tight mb-4">
                    {{ $job->title }}
                </h1>

                <!-- Job Meta -->
                <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3 text-base text-gray-600 dark:text-gray-400">
                    <div class="flex items-center">
                        <x-icon name="briefcase" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                        {{ $job->job_type->name }}
                    </div>
                    
                    @if($job->salary_display)
                        <div class="flex items-center">
                            <x-icon name="currency-dollar" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                            {{ $job->salary_display }}
                        </div>
                    @endif

                    <div class="flex items-center">
                        <x-icon name="calendar" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                        {{ __('jobs.posted') }} {{ $job->created_at->diffForHumans() }}
                    </div>

                    @if($job->deadline && $job->deadline->isFuture())
                        <div class="flex items-center">
                            <x-icon name="clock" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                            {{ __('jobs.deadline') }} {{ $job->deadline->format('M j, Y') }}
                        </div>
                    @endif

                    <div class="flex items-center">
                        <x-icon name="eye" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                        {{ number_format($job->views_count) }} {{ __('jobs.views') }}
                    </div>
                </div>

                <!-- Job Tags -->
                @if($job->skills->count() > 0)
                    <div class="mt-6">
                        <div class="flex flex-wrap gap-3">
                            @foreach($job->skills->take(8) as $skill)
                                <x-ui.badge variant="info" size="md">
                                    {{ $skill->name }}
                                </x-ui.badge>
                            @endforeach
                            @if($job->skills->count() > 8)
                                <x-ui.badge variant="outline" size="md">
                                    +{{ $job->skills->count() - 8 }} {{ __('jobs.more') }}
                                </x-ui.badge>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 lg:mt-0 lg:ml-10 flex-shrink-0">
                <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4 lg:flex-col lg:space-x-0 lg:space-y-4">
                    <x-button 
                        variant="primary" 
                        size="lg"
                        class="w-full sm:w-auto lg:w-full py-3"
                        onclick="window.JobApplication.apply({{ $job->id }})"
                    >
                        <x-icon name="paper-airplane" class="mr-2 h-5 w-5" />
                        {{ __('jobs.apply_now') }}
                    </x-button>

                    <x-button 
                        variant="outline" 
                        size="lg"
                        class="w-full sm:w-auto lg:w-full py-3"
                        onclick="window.JobActions.toggleSave({{ $job->id }})"
                    >
                        <x-icon name="heart" class="mr-2 h-5 w-5" />
                        <span>{{ __('jobs.save_job') }}</span>
                    </x-button>

                    <x-button 
                        variant="secondary" 
                        size="lg"
                        class="w-full sm:w-auto lg:w-full py-3"
                        onclick="window.JobActions.share({{ $job->id }})"
                    >
                        <x-icon name="share" class="mr-2 h-5 w-5" />
                        {{ __('jobs.share_job') }}
                    </x-button>

                    <x-button 
                        variant="danger" 
                        size="lg"
                        class="w-full sm:w-auto lg:w-full py-3"
                        onclick="window.JobActions.report({{ $job->id }})"
                    >
                        <x-icon name="flag" class="mr-2 h-5 w-5" />
                        {{ __('jobs.report_job') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Job Description -->
            <x-ui.card class="p-6">
                <x-ui.card-header class="border-b pb-4 mb-4">
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ __('jobs.job_description') }}</h3>
                </x-ui.card-header>
                <x-ui.card-content class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                    <div class="job-description prose dark:prose-invert max-w-none">
                        {!! $job->description !!}
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            <!-- Requirements -->
            @if($job->requirements)
                <x-ui.card class="p-6">
                    <x-ui.card-header class="border-b pb-4 mb-4">
                        <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ __('jobs.requirements') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                        <div class="job-description prose dark:prose-invert max-w-none">
                            {!! $job->requirements !!}
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endif

            <!-- Benefits -->
            @if($job->benefits)
                <x-ui.card class="p-6">
                    <x-ui.card-header class="border-b pb-4 mb-4">
                        <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ __('jobs.benefits') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                        <div class="job-description prose dark:prose-invert max-w-none">
                            {!! $job->benefits !!}
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endif

            <!-- Skills Required -->
            @if($job->skills->count() > 0)
                <x-ui.card class="p-6">
                    <x-ui.card-header class="border-b pb-4 mb-4">
                        <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ __('jobs.skills_required') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="flex flex-wrap gap-3">
                            @foreach($job->skills as $skill)
                                <x-ui.badge variant="primary" size="lg">
                                    {{ $skill->name }}
                                </x-ui.badge>
                            @endforeach
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="mt-8 lg:mt-0 space-y-6">
            <!-- Job Summary -->
            <x-ui.card class="p-6">
                <x-ui.card-header class="border-b pb-4 mb-4">
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ __('jobs.job_summary') }}</h3>
                </x-ui.card-header>
                <x-ui.card-content>
                    <dl class="space-y-4 text-base">
                        <div>
                            <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.job_type') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $job->job_type->name }}</dd>
                        </div>

                        @if($job->category)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.category') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $job->category->name }}</dd>
                            </div>
                        @endif

                        @if($job->experience_level)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.experience_level') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $job->experience_level }}</dd>
                            </div>
                        @endif

                        @if($job->education_level)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.education_level') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $job->education_level }}</dd>
                            </div>
                        @endif

                        @if($job->vacancies)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.vacancies') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $job->vacancies }}</dd>
                            </div>
                        @endif

                        @if($job->apply_type)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.apply_type') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $job->apply_type_display }}</dd>
                            </div>
                        @endif

                        @if($job->gender)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.gender') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $job->gender_display }}</dd>
                            </div>
                        @endif

                        @if($job->industry)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.industry') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $job->industry->name }}</dd>
                            </div>
                        @endif

                        @if($job->company_id)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.company') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    <a href="{{ route('companies.show', $job->company) }}" class="hover:text-blue-600 transition-colors duration-200">
                                        {{ $job->company->name }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        @if($job->contact_email)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.contact_email') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    <a href="mailto:{{ $job->contact_email }}" class="hover:text-blue-600 transition-colors duration-200">
                                        {{ $job->contact_email }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        @if($job->contact_phone)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.contact_phone') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $job->contact_phone }}</dd>
                            </div>
                        @endif

                        @if($job->website)
                            <div>
                                <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ __('jobs.website') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    <a href="{{ $job->website }}" target="_blank" class="hover:text-blue-600 transition-colors duration-200">
                                        {{ $job->website }}
                                        <x-icon name="external-link" class="inline-block h-4 w-4 ml-1" />
                                    </a>
                                </dd>
                            </div>
                        @endif

                    </dl>
                </x-ui.card-content>
            </x-ui.card>

            <!-- Company Overview (if available) -->
            @if($job->company && $job->company->description)
            <x-ui.card class="p-6">
                <x-ui.card-header class="border-b pb-4 mb-4">
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ __('jobs.company_overview') }}</h3>
                </x-ui.card-header>
                <x-ui.card-content class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                    <div class="prose dark:prose-invert max-w-none">
                        {!! Str::limit($job->company->description, 300) !!}
                    </div>
                    <div class="mt-4">
                        <x-button href="{{ route('companies.show', $job->company) }}" variant="secondary" class="text-sm">
                            {{ __('jobs.view_company_profile') }}
                            <x-icon name="arrow-right" class="ml-2 h-4 w-4" />
                        </x-button>
                    </div>
                </x-ui.card-content>
            </x-ui.card>
            @endif

            <!-- Similar Jobs -->
            @if($similarJobs->count() > 0)
                <x-ui.card class="p-6">
                    <x-ui.card-header class="border-b pb-4 mb-4">
                        <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ __('jobs.similar_jobs') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="space-y-4">
                            @foreach($similarJobs as $similarJob)
                                <x-jobs.job-card-mini :job="$similarJob" />
                            @endforeach
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endif
        </div>
    </div>
</div>

<!-- Application Modal -->
<x-jobs.application-modal :job="$job" />

<!-- Share Modal -->
<x-ui.share-modal 
    :url="route('jobs.show', $job)" 
    :title="$job->title . ' at ' . $job->company->name"
    :description="Str::limit(strip_tags($job->description), 200)"
/>

<!-- Report Modal -->
<x-ui.report-modal 
    type="job" 
    :id="$job->id" 
    :title="__('jobs.report_job_title')"
/>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize job application functionality
    window.JobApplication?.init();
    
    // Initialize job actions (save, share, report)
    window.JobActions?.init();

    // Track job view
    if (window.Analytics) {
        window.Analytics.track('job_view', {
            job_id: {{ $job->id }},
            job_title: '{{ $job->title }}',
            company_name: '{{ $job->company->name }}'
        });
    }
});
</script>
@endpush 