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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:flex lg:items-start lg:justify-between">
            <div class="flex-1 min-w-0">
                <!-- Company Info -->
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <img 
                            class="h-12 w-12 rounded-lg object-cover" 
                            src="{{ $job->company->logo_url }}" 
                            alt="{{ $job->company->name }}"
                            onerror="this.src='{{ asset('images/default-company.png') }}'"
                        >
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                            <a href="{{ route('companies.show', $job->company) }}" class="hover:text-blue-600">
                                {{ $job->company->name }}
                            </a>
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <x-icon name="location" class="inline h-4 w-4 mr-1" />
                            {{ $job->location_display }}
                        </p>
                    </div>
                </div>

                <!-- Job Title -->
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                    {{ $job->title }}
                </h1>

                <!-- Job Meta -->
                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <x-icon name="briefcase" class="h-4 w-4 mr-1" />
                        {{ $job->job_type->name }}
                    </div>
                    
                    @if($job->salary_display)
                        <div class="flex items-center">
                            <x-icon name="currency-dollar" class="h-4 w-4 mr-1" />
                            {{ $job->salary_display }}
                        </div>
                    @endif

                    <div class="flex items-center">
                        <x-icon name="calendar" class="h-4 w-4 mr-1" />
                        {{ __('jobs.posted') }} {{ $job->created_at->diffForHumans() }}
                    </div>

                    @if($job->deadline && $job->deadline->isFuture())
                        <div class="flex items-center">
                            <x-icon name="clock" class="h-4 w-4 mr-1" />
                            {{ __('jobs.deadline') }} {{ $job->deadline->format('M j, Y') }}
                        </div>
                    @endif

                    <div class="flex items-center">
                        <x-icon name="eye" class="h-4 w-4 mr-1" />
                        {{ number_format($job->views_count) }} {{ __('jobs.views') }}
                    </div>
                </div>

                <!-- Job Tags -->
                @if($job->skills->count() > 0)
                    <div class="mt-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($job->skills->take(8) as $skill)
                                <x-ui.badge variant="secondary" size="sm">
                                    {{ $skill->name }}
                                </x-ui.badge>
                            @endforeach
                            @if($job->skills->count() > 8)
                                <x-ui.badge variant="outline" size="sm">
                                    +{{ $job->skills->count() - 8 }} {{ __('jobs.more') }}
                                </x-ui.badge>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 lg:mt-0 lg:ml-8 flex-shrink-0">
                <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3 lg:flex-col lg:space-x-0 lg:space-y-3">
                    @auth
                        @if(auth()->user()->isCandidate())
                            @if(!$job->hasApplied(auth()->user()))
                                <x-ui.button 
                                    variant="primary" 
                                    size="lg"
                                    class="w-full sm:w-auto lg:w-full"
                                    onclick="window.JobApplication.apply({{ $job->id }})"
                                >
                                    <x-icon name="paper-airplane" class="mr-2 h-5 w-5" />
                                    {{ __('jobs.apply_now') }}
                                </x-ui.button>
                            @else
                                <x-ui.button 
                                    variant="success" 
                                    size="lg"
                                    disabled
                                    class="w-full sm:w-auto lg:w-full"
                                >
                                    <x-icon name="check" class="mr-2 h-5 w-5" />
                                    {{ __('jobs.already_applied') }}
                                </x-ui.button>
                            @endif

                            <x-ui.button 
                                variant="outline" 
                                size="lg"
                                class="w-full sm:w-auto lg:w-full"
                                onclick="window.JobActions.toggleSave({{ $job->id }})"
                                data-saved="{{ $job->isSaved(auth()->user()) ? 'true' : 'false' }}"
                            >
                                <x-icon name="heart" class="mr-2 h-5 w-5 {{ $job->isSaved(auth()->user()) ? 'fill-current text-red-500' : '' }}" />
                                <span>{{ $job->isSaved(auth()->user()) ? __('jobs.saved') : __('jobs.save_job') }}</span>
                            </x-ui.button>
                        @endif
                    @else
                        <x-ui.button 
                            href="{{ route('login', ['redirect' => request()->url()]) }}" 
                            variant="primary" 
                            size="lg"
                            class="w-full sm:w-auto lg:w-full"
                        >
                            <x-icon name="paper-airplane" class="mr-2 h-5 w-5" />
                            {{ __('jobs.apply_now') }}
                        </x-ui.button>
                    @endauth

                    <x-ui.button 
                        variant="ghost" 
                        size="lg"
                        class="w-full sm:w-auto lg:w-full"
                        onclick="window.JobActions.share({{ $job->id }})"
                    >
                        <x-icon name="share" class="mr-2 h-5 w-5" />
                        {{ __('jobs.share_job') }}
                    </x-ui.button>

                    <x-ui.button 
                        variant="ghost" 
                        size="lg"
                        class="w-full sm:w-auto lg:w-full text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                        onclick="window.JobActions.report({{ $job->id }})"
                    >
                        <x-icon name="flag" class="mr-2 h-5 w-5" />
                        {{ __('jobs.report_job') }}
                    </x-ui.button>
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
            <x-ui.card>
                <x-ui.card-header>
                    <h3 class="text-lg font-medium">{{ __('jobs.job_description') }}</h3>
                </x-ui.card-header>
                <x-ui.card-content>
                    <div class="job-description prose dark:prose-invert max-w-none">
                        {!! $job->description !!}
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            <!-- Requirements -->
            @if($job->requirements)
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="text-lg font-medium">{{ __('jobs.requirements') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="job-description prose dark:prose-invert max-w-none">
                            {!! $job->requirements !!}
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endif

            <!-- Benefits -->
            @if($job->benefits)
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="text-lg font-medium">{{ __('jobs.benefits') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="job-description prose dark:prose-invert max-w-none">
                            {!! $job->benefits !!}
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endif

            <!-- Skills Required -->
            @if($job->skills->count() > 0)
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="text-lg font-medium">{{ __('jobs.skills_required') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="flex flex-wrap gap-2">
                            @foreach($job->skills as $skill)
                                <x-ui.badge variant="primary">
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
            <x-ui.card>
                <x-ui.card-header>
                    <h3 class="text-lg font-medium">{{ __('jobs.job_summary') }}</h3>
                </x-ui.card-header>
                <x-ui.card-content>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.job_type') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $job->job_type->name }}</dd>
                        </div>

                        @if($job->category)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.category') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $job->category->name }}</dd>
                            </div>
                        @endif

                        @if($job->experience_level)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.experience_level') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $job->experience_level }}</dd>
                            </div>
                        @endif

                        @if($job->education_level)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.education_level') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $job->education_level->name }}</dd>
                            </div>
                        @endif

                        @if($job->salary_display)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.salary') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $job->salary_display }}</dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.location') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $job->location_display }}</dd>
                        </div>

                        @if($job->deadline)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.application_deadline') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $job->deadline->format('F j, Y') }}
                                    @if($job->deadline->isPast())
                                        <span class="text-red-600 dark:text-red-400">({{ __('jobs.expired') }})</span>
                                    @endif
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.posted_date') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $job->created_at->format('F j, Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.applications') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ number_format($job->applications_count) }} {{ __('jobs.applicants') }}
                            </dd>
                        </div>
                    </dl>
                </x-ui.card-content>
            </x-ui.card>

            <!-- Company Info -->
            <x-companies.company-sidebar :company="$job->company" />

            <!-- Similar Jobs -->
            @if($similarJobs && $similarJobs->count() > 0)
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="text-lg font-medium">{{ __('jobs.similar_jobs') }}</h3>
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
    window.JobApplication?.init({
        jobId: {{ $job->id }},
        isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
        hasApplied: {{ auth()->check() && $job->hasApplied(auth()->user()) ? 'true' : 'false' }}
    });

    // Initialize job actions (save, share, report)
    window.JobActions?.init();

    // Track job view
    if (window.Analytics) {
        window.Analytics.track('job_view', {
            job_id: {{ $job->id }},
            job_title: '{{ $job->title }}',
            company_name: '{{ $job->company->name }}',
            category: '{{ $job->category?->name }}',
            location: '{{ $job->location_display }}'
        });
    }

    // Increment view count
    fetch('{{ route('jobs.track-view', $job) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    });
});
</script>
@endpush 