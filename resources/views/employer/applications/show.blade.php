@extends('layouts.app')

@section('title', $application->candidate->full_name . ' - ' . __('applications.application_review'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <x-ui.button 
                        href="{{ route('employer.applications.index') }}" 
                        variant="ghost"
                        icon="arrow-left"
                    >
                        {{ __('applications.back_to_applications') }}
                    </x-ui.button>
                    
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $application->candidate->full_name }}
                        </h1>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            {{ __('applications.applied_for') }} 
                            <a href="{{ route('jobs.show', $application->job) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $application->job->title }}
                            </a>
                        </p>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    @if($application->candidate->resume_url)
                        <x-ui.button 
                            href="{{ $application->candidate->resume_url }}" 
                            target="_blank"
                            variant="secondary"
                            icon="document-text"
                        >
                            {{ __('applications.view_resume') }}
                        </x-ui.button>
                    @endif
                    
                    <x-ui.button 
                        href="{{ route('candidate.profile.show', $application->candidate) }}" 
                        target="_blank"
                        variant="secondary"
                        icon="user"
                    >
                        {{ __('applications.view_profile') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('employer.applications.contact', $application) }}" 
                        variant="primary"
                        icon="envelope"
                    >
                        {{ __('applications.contact_candidate') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Application Overview -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('applications.application_overview') }}
                        </h3>
                    </div>
                    
                    <div class="px-6 py-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('applications.application_date') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $application->created_at->format('F d, Y \a\t h:i A') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('applications.current_status') }}
                                </dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                        {{ $application->status === 'reviewing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                        {{ $application->status === 'shortlisted' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                        {{ $application->status === 'interview' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : '' }}
                                        {{ $application->status === 'hired' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                    ">
                                        {{ __('applications.status.' . $application->status) }}
                                    </span>
                                </dd>
                            </div>

                            @if($application->expected_salary)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('applications.expected_salary') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($application->expected_salary) }}
                                    </dd>
                                </div>
                            @endif

                            @if($application->candidate->availability)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('applications.availability') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $application->candidate->availability }}
                                    </dd>
                                </div>
                            @endif
                        </div>

                        @if($application->notes)
                            <div class="mt-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    {{ __('applications.cover_letter') }}
                                </dt>
                                <dd class="text-sm text-gray-900 dark:text-white whitespace-pre-line bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    {{ $application->notes }}
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Candidate Information -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('applications.candidate_information') }}
                        </h3>
                    </div>
                    
                    <div class="px-6 py-6">
                        <div class="flex items-start space-x-6">
                            <!-- Candidate Avatar -->
                            <div class="flex-shrink-0">
                                @if($application->candidate->avatar)
                                    <img class="h-20 w-20 rounded-full" src="{{ $application->candidate->avatar }}" alt="{{ $application->candidate->full_name }}">
                                @else
                                    <div class="h-20 w-20 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <x-icon name="user" class="h-10 w-10 text-gray-500 dark:text-gray-400" />
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xl font-medium text-gray-900 dark:text-white">
                                    {{ $application->candidate->full_name }}
                                </h4>
                                
                                @if($application->candidate->professional_title)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $application->candidate->professional_title }}
                                    </p>
                                @endif
                                
                                <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($application->candidate->location)
                                        <div class="flex items-center">
                                            <x-icon name="map-pin" class="h-4 w-4 mr-1" />
                                            {{ $application->candidate->location }}
                                        </div>
                                    @endif
                                    
                                    @if($application->candidate->email)
                                        <div class="flex items-center">
                                            <x-icon name="envelope" class="h-4 w-4 mr-1" />
                                            <a href="mailto:{{ $application->candidate->email }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $application->candidate->email }}
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @if($application->candidate->phone)
                                        <div class="flex items-center">
                                            <x-icon name="phone" class="h-4 w-4 mr-1" />
                                            <a href="tel:{{ $application->candidate->phone }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $application->candidate->phone }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($application->candidate->bio)
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ Str::limit($application->candidate->bio, 200) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Skills & Experience -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('applications.skills_experience') }}
                        </h3>
                    </div>
                    
                    <div class="px-6 py-6 space-y-6">
                        <!-- Skills -->
                        @if($application->candidate->skills && $application->candidate->skills->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                                    {{ __('applications.skills') }}
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($application->candidate->skills as $skill)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Experience -->
                        @if($application->candidate->experiences && $application->candidate->experiences->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                                    {{ __('applications.work_experience') }}
                                </h4>
                                <div class="space-y-4">
                                    @foreach($application->candidate->experiences->take(3) as $experience)
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                @if($experience->company_logo)
                                                    <img class="h-10 w-10 rounded-lg" src="{{ $experience->company_logo }}" alt="{{ $experience->company }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                        <x-icon name="building-office" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $experience->position }}
                                                </h5>
                                                <p class="text-sm text-blue-600 dark:text-blue-400">
                                                    {{ $experience->company }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $experience->start_date->format('M Y') }} - 
                                                    {{ $experience->end_date ? $experience->end_date->format('M Y') : __('applications.present') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Education -->
                        @if($application->candidate->educations && $application->candidate->educations->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                                    {{ __('applications.education') }}
                                </h4>
                                <div class="space-y-3">
                                    @foreach($application->candidate->educations->take(2) as $education)
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $education->degree }}
                                                @if($education->field_of_study)
                                                    in {{ $education->field_of_study }}
                                                @endif
                                            </h5>
                                            <p class="text-sm text-blue-600 dark:text-blue-400">
                                                {{ $education->institution }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $education->start_date->format('Y') }} - 
                                                {{ $education->end_date ? $education->end_date->format('Y') : __('applications.present') }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Application Timeline -->
                @if($application->timeline && count($application->timeline) > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('applications.application_timeline') }}
                            </h3>
                        </div>
                        
                        <div class="px-6 py-6">
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach($application->timeline as $index => $event)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                                @endif
                                                
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full {{ $event['type'] === 'positive' ? 'bg-green-500' : ($event['type'] === 'negative' ? 'bg-red-500' : 'bg-blue-500') }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                            <x-icon name="{{ $event['icon'] ?? 'check' }}" class="h-4 w-4 text-white" />
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-900 dark:text-white">
                                                                {{ $event['title'] }}
                                                            </p>
                                                            @if(isset($event['description']))
                                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ $event['description'] }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            {{ $event['date'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Application Rating -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('applications.rate_candidate') }}
                        </h3>
                    </div>
                    
                    <div class="px-6 py-6">
                        <form action="{{ route('employer.applications.rate', $application) }}" method="POST" id="rating-form">
                            @csrf
                            
                            <!-- Star Rating -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('applications.overall_rating') }}
                                </label>
                                <div class="flex items-center space-x-1" id="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button 
                                            type="button" 
                                            class="star-button text-gray-300 hover:text-yellow-400 focus:outline-none"
                                            data-rating="{{ $i }}"
                                            onclick="setRating({{ $i }})"
                                        >
                                            <x-icon name="star" class="h-8 w-8" />
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="{{ $application->rating ?? 0 }}">
                            </div>
                            
                            <!-- Notes -->
                            <x-ui.textarea
                                name="notes"
                                id="notes"
                                :label="__('applications.private_notes')"
                                :placeholder="__('applications.notes_placeholder')"
                                :value="old('notes', $application->employer_notes ?? '')"
                                rows="4"
                                :hint="__('applications.notes_hint')"
                            />
                            
                            <div class="mt-4">
                                <x-ui.button 
                                    type="submit" 
                                    variant="primary"
                                    class="w-full justify-center"
                                >
                                    {{ __('applications.save_rating_notes') }}
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('applications.quick_actions') }}
                        </h3>
                    </div>
                    
                    <div class="px-6 py-6 space-y-3">
                        @if($application->status === 'pending')
                            <form action="{{ route('employer.applications.update-status', $application) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="reviewing">
                                <x-ui.button 
                                    type="submit" 
                                    variant="primary"
                                    class="w-full justify-center"
                                    icon="eye"
                                >
                                    {{ __('applications.mark_as_reviewing') }}
                                </x-ui.button>
                            </form>
                        @endif

                        @if(in_array($application->status, ['pending', 'reviewing']))
                            <form action="{{ route('employer.applications.update-status', $application) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="shortlisted">
                                <x-ui.button 
                                    type="submit" 
                                    variant="secondary"
                                    class="w-full justify-center"
                                    icon="star"
                                >
                                    {{ __('applications.shortlist_candidate') }}
                                </x-ui.button>
                            </form>
                        @endif

                        @if(in_array($application->status, ['reviewing', 'shortlisted']))
                            <form action="{{ route('employer.applications.update-status', $application) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="interview">
                                <x-ui.button 
                                    type="submit" 
                                    variant="secondary"
                                    class="w-full justify-center"
                                    icon="user-group"
                                >
                                    {{ __('applications.schedule_interview') }}
                                </x-ui.button>
                            </form>
                        @endif

                        @if($application->status === 'interview')
                            <form action="{{ route('employer.applications.update-status', $application) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="hired">
                                <x-ui.button 
                                    type="submit" 
                                    variant="primary"
                                    class="w-full justify-center"
                                    icon="check-circle"
                                    onclick="return confirm('{{ __('applications.confirm_hire') }}')"
                                >
                                    {{ __('applications.hire_candidate') }}
                                </x-ui.button>
                            </form>
                        @endif

                        @if(!in_array($application->status, ['rejected', 'hired']))
                            <form action="{{ route('employer.applications.update-status', $application) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <x-ui.button 
                                    type="submit" 
                                    variant="ghost"
                                    class="w-full justify-center text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                                    icon="x-circle"
                                    onclick="return confirm('{{ __('applications.confirm_reject') }}')"
                                >
                                    {{ __('applications.reject_application') }}
                                </x-ui.button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Application Details -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('applications.application_details') }}
                        </h3>
                    </div>
                    
                    <div class="px-6 py-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('applications.job_position') }}
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="{{ route('jobs.show', $application->job) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $application->job->title }}
                                </a>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('applications.application_source') }}
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $application->source ?? __('applications.direct_application') }}
                            </dd>
                        </div>

                        @if($application->candidate->linkedin_url)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('applications.linkedin_profile') }}
                                </dt>
                                <dd class="mt-1 text-sm">
                                    <a href="{{ $application->candidate->linkedin_url }}" target="_blank" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ __('applications.view_linkedin') }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        @if($application->candidate->portfolio_url)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('applications.portfolio') }}
                                </dt>
                                <dd class="mt-1 text-sm">
                                    <a href="{{ $application->candidate->portfolio_url }}" target="_blank" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ __('applications.view_portfolio') }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('applications.application_id') }}
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                #{{ $application->id }}
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Similar Candidates -->
                @if($similarCandidates && $similarCandidates->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('applications.similar_candidates') }}
                            </h3>
                        </div>
                        
                        <div class="px-6 py-6 space-y-4">
                            @foreach($similarCandidates as $candidate)
                                <div class="flex items-center space-x-3">
                                    @if($candidate->avatar)
                                        <img class="h-10 w-10 rounded-full" src="{{ $candidate->avatar }}" alt="{{ $candidate->full_name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <x-icon name="user" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            <a href="{{ route('candidate.profile.show', $candidate) }}" target="_blank" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $candidate->full_name }}
                                            </a>
                                        </h4>
                                        @if($candidate->professional_title)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                {{ $candidate->professional_title }}
                                            </p>
                                        @endif
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
    // Initialize star rating
    const currentRating = {{ $application->rating ?? 0 }};
    setRating(currentRating);
});

function setRating(rating) {
    const stars = document.querySelectorAll('.star-button');
    const ratingInput = document.getElementById('rating-input');
    
    stars.forEach((star, index) => {
        const starIcon = star.querySelector('svg');
        if (index < rating) {
            starIcon.classList.remove('text-gray-300');
            starIcon.classList.add('text-yellow-400');
        } else {
            starIcon.classList.remove('text-yellow-400');
            starIcon.classList.add('text-gray-300');
        }
    });
    
    ratingInput.value = rating;
}

// Auto-save rating and notes
let autoSaveTimer;
const autoSaveDelay = 3000; // 3 seconds

function autoSave() {
    const form = document.getElementById('rating-form');
    const formData = new FormData(form);
    
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
            console.log('Rating and notes auto-saved');
        }
    })
    .catch(error => console.error('Auto-save error:', error));
}

// Auto-save on rating change
document.querySelectorAll('.star-button').forEach(button => {
    button.addEventListener('click', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSave, autoSaveDelay);
    });
});

// Auto-save on notes change
document.getElementById('notes').addEventListener('input', function() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(autoSave, autoSaveDelay);
});

// Form submission with loading state
document.getElementById('rating-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('applications.saving') }}...
        </div>
    `;
    
    // Submit form
    autoSave();
    
    setTimeout(() => {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }, 2000);
});
</script>
@endpush 