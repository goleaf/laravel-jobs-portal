@extends('candidate.layouts.app')
@section('title')
    {{ __('messages.candidate_profile.cv_template') }}
@endsection
@push('css')
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 py-8">
        <div class="bg-white overflow-hidden shadow-xl rounded-lg">
            <div class="px-6 py-8">
                <div class="flex flex-wrap">
                    <!-- Left Column - Personal Info -->
                    <div class="w-full lg:w-1/3 mb-8 lg:mb-0">
                        <div class="text-center mb-6">
                            <img src="{{ $user->profile_image_url }}" 
                                 alt="{{ $user->full_name }}" 
                                 class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-indigo-100">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->full_name }}</h1>
                            @isset($user->candidate->full_location)
                                <div class="flex justify-center items-center mt-2">
                                    <i class="fas fa-map-marker-alt text-gray-500 mr-2"></i>
                                    <span class="text-gray-600">{{ $user->candidate->full_location }}</span>
                                </div>
                            @endisset
                            <div class="flex justify-center items-center mt-2">
                                <i class="fas fa-envelope text-gray-500 mr-2"></i>
                                <span class="text-gray-600">{{ $user->email }}</span>
                            </div>
                            @if($user->phone)
                                <div class="flex justify-center items-center mt-2">
                                    <i class="fas fa-phone text-gray-500 mr-2"></i>
                                    <span class="text-gray-600">{{ $user->phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column - Details -->
                    <div class="w-full lg:w-2/3 lg:pl-8">
                        <!-- Skills Section -->
                        @if($user->candidateSkill->count())
                            <div class="mb-8">
                                <h2 class="text-xl font-bold text-blue-600 border-b-2 border-blue-600 pb-2 mb-4">
                                    {{ __('messages.candidate.skills') }}
                                </h2>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->candidateSkill as $skill)
                                        <span class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Education Section -->
                        @if($candidateEducations->count())
                            <div class="mb-8">
                                <h2 class="text-xl font-bold text-blue-600 border-b-2 border-blue-600 pb-2 mb-4">
                                    {{ __('messages.candidate.education') }}
                                </h2>
                                <div class="space-y-4">
                                    @foreach($candidateEducations as $candidateEducation)
                                        <div class="border-l-4 border-blue-200 pl-4">
                                            <h3 class="font-semibold text-gray-900">{{ $candidateEducation->degree_title }}</h3>
                                            <p class="text-gray-600">{{ $candidateEducation->institute }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($candidateEducation->year)->translatedFormat('Y') }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Experience Section -->
                        @if($candidateExperiences->count())
                            <div class="mb-8">
                                <h2 class="text-xl font-bold text-blue-600 border-b-2 border-blue-600 pb-2 mb-4">
                                    {{ __('messages.candidate.experience') }}
                                </h2>
                                <div class="space-y-4">
                                    @foreach($candidateExperiences as $candidateExperience)
                                        <div class="border-l-4 border-blue-200 pl-4">
                                            <h3 class="font-semibold text-gray-900">{{ $candidateExperience->experience_title }}</h3>
                                            <p class="text-gray-600">{{ $candidateExperience->company }}</p>
                                            <p class="text-sm text-gray-500">
                                                <span>{{ \Carbon\Carbon::parse($candidateExperience->start_date)->translatedFormat('jS M, Y') }} - </span>
                                                @if($candidateExperience->currently_working)
                                                    <span>{{ __('messages.candidate_profile.present') }}</span>
                                                @else
                                                    <span>{{ \Carbon\Carbon::parse($candidateExperience->end_date)->translatedFormat('jS M, Y') }}</span>
                                                @endif
                                            </p>
                                            @if($candidateExperience->description)
                                                <p class="text-gray-700 mt-2">{{ $candidateExperience->description }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Online Profile Section -->
                        @if($user->is_online_profile_availbal)
                            <div class="mb-8">
                                <h2 class="text-xl font-bold text-blue-600 border-b-2 border-blue-600 pb-2 mb-4">
                                    {{ __('messages.candidate_profile.online_profile') }}
                                </h2>
                                <div class="space-y-2">
                                    @if(isset($user->facebook_url))
                                        <a class="flex items-center text-blue-600 hover:text-blue-800" 
                                           href="{{ $user->facebook_url }}" 
                                           target="_blank">
                                            <i class="fab fa-facebook-f mr-2"></i>
                                            {{ __('messages.candidate_profile.facebook_url') }}
                                        </a>
                                    @endif
                                    @if(isset($user->twitter_url))
                                        <a class="flex items-center text-blue-600 hover:text-blue-800" 
                                           href="{{ $user->twitter_url }}" 
                                           target="_blank">
                                            <i class="fab fa-twitter mr-2"></i>
                                            {{ __('messages.candidate_profile.twitter_url') }}
                                        </a>
                                    @endif
                                    @if(isset($user->linkedin_url))
                                        <a class="flex items-center text-blue-600 hover:text-blue-800" 
                                           href="{{ $user->linkedin_url }}" 
                                           target="_blank">
                                            <i class="fab fa-linkedin-in mr-2"></i>
                                            {{ __('messages.candidate_profile.linkedin_url') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
