@extends('layouts.app')

@section('title', $candidate->name . ' - ' . __('candidates.profile'))
@section('description', Str::limit(strip_tags($candidate->bio), 160))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <!-- Left Sidebar: Profile Summary & Contact -->
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card>
                <x-ui.card-content class="text-center p-6">
                    @if($candidate->profile_picture)
                        <img class="h-24 w-24 rounded-full object-cover mx-auto mb-4 border-2 border-blue-400 shadow-sm" src="{{ asset('storage/' . $candidate->profile_picture) }}" alt="{{ $candidate->name }}">
                    @else
                        <div class="h-24 w-24 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 text-4xl font-semibold mx-auto mb-4">
                            {{ substr($candidate->name ?? '?', 0, 1) }}
                        </div>
                    @endif
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $candidate->name ?? __('candidates.no_name') }}</h2>
                    @if($candidate->headline)
                        <p class="text-gray-600 dark:text-gray-400 text-lg mt-1">{{ $candidate->headline }}</p>
                    @endif
                    <div class="mt-4">
                        <x-button href="#" variant="primary" class="w-full">{{ __('candidates.contact') }}</x-button>
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            <!-- Contact Information -->
            <x-ui.card>
                <x-ui.card-header>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('candidates.contact_information') }}</h3>
                </x-ui.card-header>
                <x-ui.card-content class="space-y-3">
                    <div class="flex items-center text-gray-700 dark:text-gray-300">
                        <x-icon name="envelope" class="h-5 w-5 mr-3 text-gray-500" />
                        <span>{{ $candidate->email ?? __('candidates.not_provided') }}</span>
                    </div>
                    @if($candidate->phone_number)
                        <div class="flex items-center text-gray-700 dark:text-gray-300">
                            <x-icon name="phone" class="h-5 w-5 mr-3 text-gray-500" />
                            <span>{{ $candidate->phone_number }}</span>
                        </div>
                    @endif
                    @if($candidate->location)
                        <div class="flex items-center text-gray-700 dark:text-gray-300">
                            <x-icon name="map-pin" class="h-5 w-5 mr-3 text-gray-500" />
                            <span>{{ $candidate->location }}</span>
                        </div>
                    @endif
                    @if($candidate->website)
                        <div class="flex items-center text-gray-700 dark:text-gray-300">
                            <x-icon name="globe-alt" class="h-5 w-5 mr-3 text-gray-500" />
                            <a href="{{ $candidate->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $candidate->website }}</a>
                        </div>
                    @endif
                </x-ui.card-content>
            </x-ui.card>
        </div>

        <!-- Main Content: About, Experience, Education, Skills -->
        <div class="lg:col-span-2 space-y-6 mt-6 lg:mt-0">
            <!-- About Me -->
            @if($candidate->bio)
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('candidates.about_me') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! $candidate->bio !!}
                    </x-ui.card-content>
                </x-ui.card>
            @endif

            <!-- Experience -->
            @if($candidate->experiences->count() > 0)
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('candidates.work_experience') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="space-y-6">
                            @foreach($candidate->experiences as $experience)
                                <div>
                                    <h4 class="text-md font-semibold text-gray-900 dark:text-white">{{ $experience->job_title }} at {{ $experience->company_name }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $experience->start_date->format('M Y') }} - {{ $experience->end_date ? $experience->end_date->format('M Y') : __('candidates.present') }}</p>
                                    <p class="text-gray-700 dark:text-gray-300 mt-2 text-sm">{!! nl2br(e($experience->description)) !!}</p>
                                </div>
                            @endforeach
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endif

            <!-- Education -->
            @if($candidate->educations->count() > 0)
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('candidates.education') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="space-y-6">
                            @foreach($candidate->educations as $education)
                                <div>
                                    <h4 class="text-md font-semibold text-gray-900 dark:text-white">{{ $education->degree }} in {{ $education->field_of_study }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $education->university_name }} ({{ $education->start_date->format('Y') }} - {{ $education->end_date ? $education->end_date->format('Y') : __('candidates.present') }})</p>
                                    @if($education->description)
                                        <p class="text-gray-700 dark:text-gray-300 mt-2 text-sm">{!! nl2br(e($education->description)) !!}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endif

            <!-- Skills -->
            @if($candidate->skills->count() > 0)
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('candidates.skills') }}</h3>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="flex flex-wrap gap-2">
                            @foreach($candidate->skills as $skill)
                                <x-ui.badge variant="primary" size="lg">{{ $skill->name }}</x-ui.badge>
                            @endforeach
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            @endif

            <!-- Portfolio (Conceptual) -->
            @if($candidate->portfolio_url)
            <x-ui.card>
                <x-ui.card-header>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('candidates.portfolio') }}</h3>
                </x-ui.card-header>
                <x-ui.card-content>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">{{ __('candidates.portfolio_description') }}</p>
                    <x-button href="{{ $candidate->portfolio_url }}" target="_blank" variant="secondary">
                        <x-icon name="arrow-top-right-on-square" class="h-5 w-5 mr-2" />
                        {{ __('candidates.view_portfolio') }}
                    </x-button>
                </x-ui.card-content>
            </x-ui.card>
            @endif

            <!-- Resume (Conceptual) -->
            @if($candidate->resume_url)
            <x-ui.card>
                <x-ui.card-header>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('candidates.resume') }}</h3>
                </x-ui.card-header>
                <x-ui.card-content>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">{{ __('candidates.resume_description') }}</p>
                    <x-button href="{{ $candidate->resume_url }}" target="_blank" variant="primary">
                        <x-icon name="arrow-down-tray" class="h-5 w-5 mr-2" />
                        {{ __('candidates.download_resume') }}
                    </x-button>
                </x-ui.card-content>
            </x-ui.card>
            @endif
        </div>
    </div>
</div>
@endsection 