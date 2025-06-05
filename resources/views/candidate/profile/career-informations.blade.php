@extends('candidate.profile.index')
@push('css')@endpush
@section('section')
    <div class="mb-xl-8">
        <div class="border border border-gray-300 -gray-300 -0">
            <div class="mb-5 md:flex items-center justify-between mx-3">
                <h1 class="mb-0">{{ __('messages.candidate_profile.experience') }}</h1>
                <div class="mt-4 text-end mt-md-0">
                    <a
                            class="border border-gray-300 bg-transparent" data-bs-toggle="modal"
                            data-bs-target="#addExperienceModal">{{ __('messages.candidate_profile.add_experience') }}  </a>
                </div>
            </div>

            <div class="pt-0 fs-6 py-8 px-3 text-gray-700">
                {{ Form::hidden(null,__('messages.candidate_profile.present'),['id' => 'candidatePresentMsg']) }}
                <div class="flex-wrap flex">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 candidate-experience- mx-auto px-4 mx-auto">
                        <div class="flex-1 -12 {{ ($data["candidateExperiences']->count()) ? 'hidden' : '' }}"
                             id="notfoundExperience">
                            <h5 class="pb-5 product-item flex justify-center text-gray-600">
                                {{ __('messages.candidate.experience_not_found') }}
                            </h5>
                        </div>
                        @php
                            /** @var \App\Models\CandidateExperience $candidateExperience */
                        @endphp
                        @foreach($data['candidateExperiences'] as $candidateExperience)
                            <div class="overflow-hidden shadow rounded p-5 mb-5 bg-white shadow rounded w-full px-4-sm-12 md:w-full flex-1 lg-12 candidate-experience -lg"
                                 data-experience-id="{{ $loop->index }}" data-id="{{ $candidateExperience->id }}">
                                <article class="article article-style-b">
                                    <div class="article-details">
                                        <div class="flex justify-between">
                                            <div class="article-title">
                                                <h4 class="text-indigo-600 -600">{{ $candidateExperience->experience_title }}</h4>
                                                <h6 class="text-gray-500">{{ $candidateExperience->company }}</h6>
                                            </div>
                                            <div class="article-cta candidate-experience-edit-delete">
                                        <a href="javascript:void(0)"
                                           class="transition duration-150 ease-in-out flex-1"
                                           title="{{ __('messages.common.edit') }}" data-bs-toggle="tooltip"
                                           data-id="{{ $candidateExperience->id }}"><i
                                                    class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="javascript:void(0)"
                                           class="transition duration-150 ease-in-out flex-1"
                                           title="{{ __('messages.common.delete') }}" data-bs-toggle="tooltip"
                                           data-id="{{ $candidateExperience->id }}"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </div>
                                <span class="text-gray-500">{{ \Carbon\Carbon::parse($candidateExperience->start_date)->translatedFormat('jS M, Y') }} - </span>

                                @if($candidateExperience->currently_working)
                                    <span class="text-gray-500">{{ __('messages.candidate_profile.present') }}</span>
                                @else
                                    <span
                                            class="text-gray-500"> {{ \Carbon\Carbon::parse($candidateExperience->end_date)->translatedFormat('jS M, Y') }} </span>
                                        @endif
                                        <span class="text-gray-500"> | {{ $candidateExperience->country }}</span>
                                        @if(!empty($candidateExperience->description))
                                            <p class="pb-4 mb-0 pb-md-0">{{ Str::limit($candidateExperience->description,225,'...') }}</p>
                                        @endif
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="border border border-gray-300 -gray-300 -0 pt-6">
            <div class="mb-5 md:flex items-center justify-between mx-3">
                <h1 class="mb-0">{{ __('messages.candidate_profile.education') }}</h1>
                <div class="mt-4 text-end mt-md-0">
                    <a
                            class="border border-gray-300 bg-transparent" data-bs-toggle="modal"
                            data-bs-target="#addEducationModal">{{ __('messages.candidate_profile.add_education') }}
                    </a>
                </div>
            </div>
            <div class="pt-0 fs-6 py-8 px-3 text-gray-700">
                <div class="flex-wrap flex">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 candidate-education- mx-auto px-4 mx-auto">
                        <div class="flex-1 -12 {{ ($data["candidateEducations']->count()) ? 'hidden' : '' }}"
                     id="notfoundEducation">
                    <h5 class="pb-5 product-item flex justify-center text-gray-600">
                        {{ __('messages.candidate.education_not_found') }}
                    </h5>
                </div>
                @php
                    /** @var \App\Models\CandidateEducation $candidateEducation */
                @endphp
                @foreach($data['candidateEducations'] as $candidateEducation)
                    <div class="overflow-hidden shadow rounded p-5 mb-5 bg-white shadow rounded w-full px-4-sm-12 md:w-full flex-1 lg-12 candidate-education -lg"
                         data-education-id="{{ $loop->index }}" data-id="{{ $candidateEducation->id }}">
                        <article class="article article-style-b">
                            <div class="article-details">
                                <div class="flex justify-between">
                                    <div class="article-title">
                                        <h4 class="text-indigo-600 -600 education-degree-level">{{ $candidateEducation->degreeLevel->name }}</h4>
                                        <h6 class="text-gray-500">{{ $candidateEducation->degree_title }}</h6>
                                    </div>
                                    <div class="article-cta candidate-education-edit-delete">
                                        <a href="javascript:void(0)"
                                           class="transition duration-150 ease-in-out flex-1"
                                           title="{{ __('messages.common.edit') }}" data-bs-toggle="tooltip"
                                           data-id="{{ $candidateEducation->id }}"><i
                                                    class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="javascript:void(0)"
                                           class="transition duration-150 ease-in-out flex-1"
                                           title="{{ __('messages.common.delete') }}" data-bs-toggle="tooltip"
                                           data-id="{{ $candidateEducation->id }}"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </div>
                                <span
                                    class="text-gray-500">{{ $candidateEducation->year }} | {{ $candidateEducation->country }}</span>
                                <p class="pb-4 mb-0 pb-md-0">{{ $candidateEducation->institute }}</p>
                            </div>
                        </article>
                    </div>
                @endforeach
                    </div>
                </div>
            </div>

        </div>
        {{-- <section class="section"> --}}
    {{-- <div class="section-header candidate-experience-header"> --}}
    {{-- <h1>{{ __('messages.candidate_profile.experience') }}</h1> --}}
    {{-- <div class="flex space-x-2 text-sm section-header- justify-end"> --}}
    {{-- <a --}}
    {{-- class="border border-gray-300 bg-transparent" data-bs-toggle="modal" --}}
    {{-- data-bs-target="#addExperienceModal">{{ __('messages.candidate_profile.add_experience') }} --}}
    {{-- <i class="fas fa-plus"></i></a> --}}
    {{-- </div> --}}
    {{-- </div> --}}
    {{-- <div class="section-body"> --}}
    {{-- <div class="flex-wrap max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex candidate-experience- mx-auto px-4 mx-auto"> --}}
    {{-- <div class="flex-1 -12 {{ ($data["candidateExperiences']->count()) ? 'hidden' : '' }}" id="notfoundExperience"> --}}
    {{-- <h4 class="pb-5 product-item flex justify-center"> --}}
    {{-- {{ __('messages.candidate.experience_not_found') }} --}}
    {{-- </h4> --}}
    {{-- </div> --}}
    {{-- @php --}}
    {{-- /** @var \App\Models\CandidateExperience $candidateExperience */ --}}
    {{-- @endphp --}}
    {{-- @foreach($data['candidateExperiences'] as $candidateExperience) --}}
    {{-- <div class="w-full px-4-sm-12 md:w-full flex-1 lg-12 candidate-experience" --}}
    {{-- data-experience-id="{{ $loop->index }}" data-id="{{ $candidateExperience->id }}"> --}}
    {{-- <article class="article article-style-b"> --}}
    {{-- <div class="article-details"> --}}
    {{-- <div class="article-title"> --}}
    {{-- <h4 class="text-indigo-600 -600">{{ $candidateExperience->experience_title }}</h4> --}}
    {{-- <h6 class="text-gray-500">{{ $candidateExperience->company }}</h6> --}}
    {{-- </div> --}}
    {{-- <span class="text-gray-500">{{ \Carbon\Carbon::parse($candidateExperience->start_date)->format('jS M, Y') }} - </span> --}}

    {{-- @if($candidateExperience->currently_working) --}}
    {{-- <span class="text-gray-500">{{ __('messages.candidate_profile.present') }}</span> --}}
    {{-- @else --}}
    {{-- <span class="text-gray-500"> {{\Carbon\Carbon::parse($candidateExperience->end_date)->format('jS M, Y') }} </span> --}}
    {{-- @endif --}}
    {{-- <span> | {{ $candidateExperience->country }}</span> --}}
    {{-- @if(!empty($candidateExperience->description)) --}}
    {{-- <p class="pb-4 mb-0 pb-md-0">{{ Str::limit($candidateExperience->description,225,'...') }}</p> --}}
    {{-- @endif --}}

    {{-- <div class="article-cta candidate-experience-edit-delete"> --}}
    {{-- <a href="javascript:void(0)" class="border border-gray-300 bg-transparent" title="Edit" --}}
    {{-- data-id="{{ $candidateExperience->id }}"><i class="p-1 fa fa-edit"></i></a> --}}
    {{-- <a href="javascript:void(0)" class="border border-gray-300 bg-transparent" title="Delete" --}}
    {{-- data-id="{{ $candidateExperience->id }}"><i class="p-1 fa fa-trash"></i></a> --}}
    {{-- </div> --}}
    {{-- </div> --}}
    {{-- </article> --}}
    {{-- </div> --}}
    {{-- @endforeach --}}
    {{-- </div> --}}
    {{-- </div> --}}
    {{-- </section> --}}
    {{-- <br> --}}
    {{-- <section class="section"> --}}
    {{-- <div class="section-header candidate-experience-header"> --}}
    {{-- <h1>{{ __('messages.candidate_profile.education') }}</h1> --}}
    {{-- <div class="flex space-x-2 text-sm section-header- justify-end"> --}}
    {{-- <a --}}
    {{-- class="border border-gray-300 bg-transparent" data-bs-toggle="modal" --}}
    {{-- data-bs-target="#addEducationModal">{{ __('messages.candidate_profile.add_education') }} --}}
    {{-- <i class="fas fa-plus"></i></a> --}}
    {{-- </div> --}}
    {{-- </div> --}}
    {{-- <div class="section-body"> --}}
    {{-- <div class="flex-wrap max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex candidate-education- mx-auto px-4 mx-auto"> --}}
    {{-- <div class="flex-1 -12 {{ ($data["candidateEducations']->count()) ? 'hidden' : '' }}" id="notfoundEducation"> --}}
    {{-- <h4 class="pb-5 product-item flex justify-center"> --}}
    {{-- {{ __('messages.candidate.education_not_found') }} --}}
    {{-- </h4> --}}
    {{-- </div> --}}
    {{-- @php --}}
    {{-- /** @var \App\Models\CandidateEducation $candidateEducation */ --}}
    {{-- @endphp --}}
    {{-- @foreach($data['candidateEducations'] as $candidateEducation) --}}
    {{-- <div class="w-full px-4-sm-12 md:w-full flex-1 lg-12 candidate-education" --}}
    {{-- data-education-id="{{ $loop->index }}" data-id="{{ $candidateEducation->id }}"> --}}
    {{-- <article class="article article-style-b"> --}}
    {{-- <div class="article-details"> --}}
    {{-- <div class="article-title"> --}}
    {{-- <h4 class="text-indigo-600 -600 education-degree-level">{{ $candidateEducation->degreeLevel->name }}</h4> --}}
    {{-- <h6 class="text-gray-500">{{ $candidateEducation->degree_title }}</h6> --}}
    {{-- </div> --}}
    {{-- <span class="text-gray-500">{{ $candidateEducation->year }} | {{ $candidateEducation->country }}</span> --}}
    {{-- <p class="pb-4 mb-0 pb-md-0">{{ $candidateEducation->institute }}</p> --}}
    {{-- <div class="article-cta candidate-education-edit-delete"> --}}
    {{-- <a href="javascript:void(0)" class="border border-gray-300 bg-transparent" title="Edit" --}}
    {{-- data-id="{{ $candidateEducation->id }}"><i class="p-1 fa fa-edit"></i></a> --}}
    {{-- <a href="javascript:void(0)" class="border border-gray-300 bg-transparent" title="Delete" --}}
    {{-- data-id="{{ $candidateEducation->id }}"><i class="p-1 fa fa-trash"></i></a> --}}
    {{-- </div> --}}
    {{-- </div> --}}
    {{-- </article> --}}
    {{-- </div> --}}
    {{-- @endforeach --}}
    {{-- </div> --}}
    {{-- </div> --}}
    {{-- </section> --}}
    @include('candidate.profile.modals.add_experience_modal')
    @include('candidate.profile.modals.add_education_modal')
    @include('candidate.profile.modals.edit_experience_modal')
    @include('candidate.profile.modals.edit_education_modal')
    
    {{ Form::hidden('indexCareerInfoData',true,['id'=>'indexCareerInfoData']) }}
@endsection
@push('scripts')
    
{{-- <script src="{{ asset('assets/js/moment.min.js') }}"></script> --}}
    {{--  --}}
    {{-- <script src="{{mix('assets/js/candidate-profile/candidate_career_informations.js') }}"></script> --}}
@endpush

@push('scripts')
    @vite('resources/js/components/career-informations.js')
@endpush
