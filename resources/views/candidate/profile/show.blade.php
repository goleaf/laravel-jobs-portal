@extends('layouts.app')

@section('title', $candidate->full_name . ' - ' . __('profile.profile'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8 overflow-hidden">
            <!-- Cover Photo Area -->
            <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600"></div>
            
            <div class="px-6 py-6">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <div class="sm:flex sm:space-x-5">
                        <div class="flex-shrink-0">
                            @if($candidate->avatar)
                                <img class="mx-auto h-20 w-20 rounded-full border-4 border-white dark:border-gray-800 -mt-10" src="{{ $candidate->avatar }}" alt="{{ $candidate->full_name }}">
                            @else
                                <div class="mx-auto h-20 w-20 rounded-full border-4 border-white dark:border-gray-800 -mt-10 bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                    <x-icon name="user" class="h-10 w-10 text-gray-500 dark:text-gray-400" />
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left">
                            <p class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
                                {{ $candidate->full_name }}
                            </p>
                            
                            @if($candidate->professional_title)
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    {{ $candidate->professional_title }}
                                </p>
                            @endif
                            
                            <div class="mt-2 flex flex-wrap items-center justify-center sm:justify-start gap-2 text-sm text-gray-500 dark:text-gray-400">
                                @if($candidate->city || $candidate->country)
                                    <div class="flex items-center">
                                        <x-icon name="map-pin" class="h-4 w-4 mr-1" />
                                        {{ collect([$candidate->city, $candidate->country?->name])->filter()->join(', ') }}
                                    </div>
                                @endif
                                
                                @if($candidate->show_contact_info && $candidate->email)
                                    <div class="flex items-center">
                                        <x-icon name="envelope" class="h-4 w-4 mr-1" />
                                        <a href="mailto:{{ $candidate->email }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $candidate->email }}
                                        </a>
                                    </div>
                                @endif
                                
                                @if($candidate->show_contact_info && $candidate->phone)
                                    <div class="flex items-center">
                                        <x-icon name="phone" class="h-4 w-4 mr-1" />
                                        <a href="tel:{{ $candidate->phone }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $candidate->phone }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5 flex justify-center sm:mt-0">
                        <x-ui.button 
                            href="{{ route('contact') }}" 
                            variant="primary"
                            icon="envelope"
                        >
                            {{ __('profile.contact_candidate') }}
                        </x-ui.button>
                    </div>
                </div>
                
                <!-- Social Links -->
                @if($candidate->linkedin_url || $candidate->github_url || $candidate->portfolio_url || $candidate->website_url)
                    <div class="mt-6 flex justify-center sm:justify-start space-x-4">
                        @if($candidate->linkedin_url)
                            <a href="{{ $candidate->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                <span class="sr-only">{{ __('social.linkedin') }}</span>
                                <x-icon name="linkedin" class="h-6 w-6" />
                            </a>
                        @endif
                        
                        @if($candidate->github_url)
                            <a href="{{ $candidate->github_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                <span class="sr-only">{{ __('social.github') }}</span>
                                <x-icon name="github" class="h-6 w-6" />
                            </a>
                        @endif
                        
                        @if($candidate->portfolio_url)
                            <a href="{{ $candidate->portfolio_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-600 dark:hover:text-purple-400">
                                <span class="sr-only">{{ __('profile.portfolio') }}</span>
                                <x-icon name="globe-alt" class="h-6 w-6" />
                            </a>
                        @endif
                        
                        @if($candidate->website_url)
                            <a href="{{ $candidate->website_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-green-600 dark:hover:text-green-400">
                                <span class="sr-only">{{ __('profile.website') }}</span>
                                <x-icon name="link" class="h-6 w-6" />
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Professional Summary -->
                @if($candidate->bio)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('profile.professional_summary') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $candidate->bio }}</p>
                        </div>
                    </div>
                @endif

                <!-- Work Experience -->
                @if($candidate->experiences && $candidate->experiences->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('profile.work_experience') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6">
                            <div class="space-y-6">
                                @foreach($candidate->experiences as $experience)
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            @if($experience->company_logo)
                                                <img class="h-12 w-12 rounded-lg object-cover" src="{{ $experience->company_logo }}" alt="{{ $experience->company }}">
                                            @else
                                                <div class="h-12 w-12 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                    <x-icon name="building-office" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ $experience->position }}
                                            </h4>
                                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                {{ $experience->company }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $experience->start_date->format('M Y') }} - 
                                                {{ $experience->end_date ? $experience->end_date->format('M Y') : __('profile.present') }}
                                                @if($experience->location)
                                                    • {{ $experience->location }}
                                                @endif
                                            </p>
                                            @if($experience->description)
                                                <p class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                                    {{ $experience->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Education -->
                @if($candidate->educations && $candidate->educations->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('profile.education') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6">
                            <div class="space-y-6">
                                @foreach($candidate->educations as $education)
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <x-icon name="academic-cap" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ $education->degree }}
                                                @if($education->field_of_study)
                                                    in {{ $education->field_of_study }}
                                                @endif
                                            </h4>
                                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                {{ $education->institution }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $education->start_date->format('Y') }} - 
                                                {{ $education->end_date ? $education->end_date->format('Y') : __('profile.present') }}
                                                @if($education->grade)
                                                    • {{ __('profile.grade') }}: {{ $education->grade }}
                                                @endif
                                            </p>
                                            @if($education->description)
                                                <p class="mt-2 text-gray-700 dark:text-gray-300">
                                                    {{ $education->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Certifications -->
                @if($candidate->certifications && $candidate->certifications->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('profile.certifications') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                @foreach($candidate->certifications as $certification)
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            {{ $certification->name }}
                                        </h4>
                                        <p class="text-sm text-blue-600 dark:text-blue-400">
                                            {{ $certification->issuing_organization }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $certification->issue_date->format('M Y') }}
                                            @if($certification->expiry_date)
                                                - {{ $certification->expiry_date->format('M Y') }}
                                            @endif
                                        </p>
                                        @if($certification->credential_url)
                                            <a href="{{ $certification->credential_url }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                                {{ __('profile.view_credential') }}
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Info -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('profile.quick_info') }}
                        </h3>
                    </div>
                    <div class="px-6 py-6 space-y-4">
                        @if($candidate->career_level)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('profile.career_level') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $candidate->career_level->name }}
                                </dd>
                            </div>
                        @endif

                        @if($candidate->expected_salary_min || $candidate->expected_salary_max)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('profile.expected_salary') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    @if($candidate->expected_salary_min && $candidate->expected_salary_max)
                                        ${{ number_format($candidate->expected_salary_min) }} - ${{ number_format($candidate->expected_salary_max) }}
                                    @elseif($candidate->expected_salary_min)
                                        ${{ number_format($candidate->expected_salary_min) }}+
                                    @else
                                        ${{ number_format($candidate->expected_salary_max) }}
                                    @endif
                                </dd>
                            </div>
                        @endif

                        @if($candidate->preferred_job_type)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('profile.preferred_job_type') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $candidate->preferred_job_type->name }}
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('profile.work_preferences') }}
                            </dt>
                            <dd class="mt-1">
                                <div class="flex flex-wrap gap-2">
                                    @if($candidate->remote_work)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('profile.remote_work') }}
                                        </span>
                                    @endif
                                    
                                    @if($candidate->part_time)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ __('profile.part_time') }}
                                        </span>
                                    @endif
                                    
                                    @if($candidate->freelance)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                            {{ __('profile.freelance') }}
                                        </span>
                                    @endif
                                    
                                    @if($candidate->willing_to_relocate)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            {{ __('profile.willing_to_relocate') }}
                                        </span>
                                    @endif
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Skills -->
                @if($candidate->skills && $candidate->skills->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('profile.skills') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6">
                            <div class="flex flex-wrap gap-2">
                                @foreach($candidate->skills as $skill)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $skill->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Languages -->
                @if($candidate->languages && $candidate->languages->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('profile.languages') }}
                            </h3>
                        </div>
                        <div class="px-6 py-6 space-y-3">
                            @foreach($candidate->languages as $language)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $language->name }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $language->pivot->proficiency ?? __('profile.conversational') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Resume Download -->
                @if($candidate->resume_path)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-6 text-center">
                            <x-icon name="document-text" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('profile.resume') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('profile.download_full_resume') }}
                            </p>
                            <div class="mt-6">
                                <x-ui.button 
                                    href="{{ route('candidate.resume.download', $candidate) }}" 
                                    variant="primary"
                                    icon="arrow-down-tray"
                                >
                                    {{ __('profile.download_resume') }}
                                </x-ui.button>
                            </div>
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
// Track profile views (if not the profile owner)
@if(auth()->id() !== $candidate->user_id)
document.addEventListener('DOMContentLoaded', function() {
    // Track profile view
    fetch('{{ route("candidate.profile.track-view", $candidate) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).catch(error => console.error('Error tracking profile view:', error));
});
@endif
</script>
@endpush 