@extends('candidate.profile.index')
@push('css')@endpush
@section('section')
    <div class="mb-xl-8">
        <div class="border-0">
            <div class="d-md-flex items-center justify-between mb-5 mx-3">
                <h1 class="mb-0">{{ __('messages.candidate_profile.experience') }}</h1>
                <div class="text-end mt-4 mt-md-0">
                    <a
                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 form- px-4 py-2 rounded font-medium transition-colors addExperienceModal" data-bs-toggle="modal"
                            data-bs-target="#addExperienceModal">{{ __('messages.candidate_profile.add_experience') }}  </a>
                </div>
            </div>

            <div class="pt-0 fs-6 py-8 px-3 text-gray-700">
                {{ Form::hidden(null,__('messages.candidate_profile.present'),['id' => 'candidatePresentMsg']) }}
                <div class="flex flex-wrap">
                    <div class="candidate-experience- container mx-auto px-4 mx-auto">
                        <div class="flex-1 -12 {{ ($data["candidateExperiences']->count()) ? 'd-none' : '' }}"
                             id="notfoundExperience">
                            <h5 class="product-item pb-5 flex justify-center text-gray-600">
                                {{ __('messages.candidate.experience_not_found') }}
                            </h5>
                        </div>
                        @php
                            /** @var \App\Models\CandidateExperience $candidateExperience */
                        @endphp
                        @foreach($data['candidateExperiences'] as $candidateExperience)
                            <div class="w-full col-sm-12 md:w-full flex-1 lg-12 candidate-experience rounded shadow p-5 mb-5 bg-white shadow rounded-lg overflow-hidden"
                                 data-experience-id="{{ $loop->index }}" data-id="{{ $candidateExperience->id }}">
                                <article class="article article-style-b">
                                    <div class="article-details">
                                        <div class="flex justify-between">
                                            <div class="article-title">
                                                <h4 class="text-primary-600">{{ $candidateExperience->experience_title }}</h4>
                                                <h6 class="text-gray-500">{{ $candidateExperience->company }}</h6>
                                            </div>
                                            <div class="article-cta candidate-experience-edit-delete">
                                        <a href="javascript:void(0)"
                                           class="edit-candidate-experience px-4 py-2 rounded font-medium transition-colors px-2 text-primary-600 fs-3 ps-0"
                                           title="{{ __('messages.common.edit') }}" data-bs-toggle="tooltip"
                                           data-id="{{ $candidateExperience->id }}"><i
                                                    class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="javascript:void(0)"
                                           class="delete-experience px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0"
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
                                            <p class="mb-0 pb-md-0 pb-4">{{ Str::limit($candidateExperience->description,225,'...') }}</p>
                                        @endif
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="border-0 pt-6">
            <div class="d-md-flex items-center justify-between mb-5 mx-3">
                <h1 class="mb-0">{{ __('messages.candidate_profile.education') }}</h1>
                <div class="text-end mt-4 mt-md-0">
                    <a
                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 form- px-4 py-2 rounded font-medium transition-colors addEducationModal" data-bs-toggle="modal"
                            data-bs-target="#addEducationModal">{{ __('messages.candidate_profile.add_education') }}
                    </a>
                </div>
            </div>
            <div class="pt-0 fs-6 py-8 px-3 text-gray-700">
                <div class="flex flex-wrap">
                    <div class="candidate-education- container mx-auto px-4 mx-auto">
                        <div class="flex-1 -12 {{ ($data["candidateEducations']->count()) ? 'd-none' : '' }}"
                     id="notfoundEducation">
                    <h5 class="product-item pb-5 flex justify-center text-gray-600">
                        {{ __('messages.candidate.education_not_found') }}
                    </h5>
                </div>
                @php
                    /** @var \App\Models\CandidateEducation $candidateEducation */
                @endphp
                @foreach($data['candidateEducations'] as $candidateEducation)
                    <div class="w-full col-sm-12 md:w-full flex-1 lg-12 candidate-education shadow rounded p-5 mb-5 bg-white shadow rounded-lg overflow-hidden"
                         data-education-id="{{ $loop->index }}" data-id="{{ $candidateEducation->id }}">
                        <article class="article article-style-b">
                            <div class="article-details">
                                <div class="flex justify-between">
                                    <div class="article-title">
                                        <h4 class="text-primary-600 education-degree-level">{{ $candidateEducation->degreeLevel->name }}</h4>
                                        <h6 class="text-gray-500">{{ $candidateEducation->degree_title }}</h6>
                                    </div>
                                    <div class="article-cta candidate-education-edit-delete">
                                        <a href="javascript:void(0)"
                                           class="px-4 py-2 rounded font-medium transition-colors px-2 text-primary-600 fs-3 ps-0 edit-candidate-education"
                                           title="{{ __('messages.common.edit') }}" data-bs-toggle="tooltip"
                                           data-id="{{ $candidateEducation->id }}"><i
                                                    class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="javascript:void(0)"
                                           class="delete-education px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0"
                                           title="{{ __('messages.common.delete') }}" data-bs-toggle="tooltip"
                                           data-id="{{ $candidateEducation->id }}"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </div>
                                <span
                                    class="text-gray-500">{{ $candidateEducation->year }} | {{ $candidateEducation->country }}</span>
                                <p class="mb-0 pb-md-0 pb-4">{{ $candidateEducation->institute }}</p>
                            </div>
                        </article>
                    </div>
                @endforeach
                    </div>
                </div>
            </div>

        </div>
        {{ --    <section class="section">-- }}
    {{ --        <div class="section-header candidate-experience-header">-- }}
    {{ --            <h1>{{ __('messages.candidate_profile.experience') }}</h1>--}}
    {{ --            <div class="section-header-breadcrumb justify-end">-- }}
    {{ --                <a-- }}
    {{ --                   class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 form- px-4 py-2 rounded font-medium transition-colors addExperienceModal" data-bs-toggle="modal"-- }}
    {{ --                   data-bs-target="#addExperienceModal">{{ __('messages.candidate_profile.add_experience') }}--}}
    {{ --                    <i class="fas fa-plus"></i></a>-- }}
    {{ --            </div>-- }}
    {{ --        </div>-- }}
    {{ --        <div class="section-body">-- }}
    {{ --            <div class="flex flex-wrap candidate-experience- container mx-auto px-4 mx-auto">-- }}
    {{ --                <div class="flex-1 -12 {{ ($data["candidateExperiences']->count()) ? 'd-none' : '' }}" id="notfoundExperience">--}}
    {{ --                    <h4 class="product-item pb-5 flex justify-center">-- }}
    {{ --                        {{ __('messages.candidate.experience_not_found') }}--}}
    {{ --                    </h4>-- }}
    {{ --                </div>-- }}
    {{ --                @php-- }}
    {{ --                /** @var \App\Models\CandidateExperience $candidateExperience */-- }}
    {{ --                @endphp-- }}
    {{ --                @foreach($data['candidateExperiences'] as $candidateExperience)-- }}
    {{ --                    <div class="w-full col-sm-12 md:w-full flex-1 lg-12 candidate-experience"-- }}
    {{ --                         data-experience-id="{{ $loop->index }}" data-id="{{ $candidateExperience->id }}">--}}
    {{ --                        <article class="article article-style-b">-- }}
    {{ --                            <div class="article-details">-- }}
    {{ --                                <div class="article-title">-- }}
    {{ --                                    <h4 class="text-primary-600">{{ $candidateExperience->experience_title }}</h4>--}}
    {{ --                                    <h6 class="text-gray-500">{{ $candidateExperience->company }}</h6>--}}
    {{ --                                </div>-- }}
    {{ --                                <span class="text-gray-500">{{ \Carbon\Carbon::parse($candidateExperience->start_date)->format('jS M, Y') }} - </span>--}}

    {{ --                                @if($candidateExperience->currently_working)-- }}
    {{ --                                    <span class="text-gray-500">{{ __('messages.candidate_profile.present') }}</span>--}}
    {{ --                                @else-- }}
    {{ --                                    <span class="text-gray-500"> {{\Carbon\Carbon::parse($candidateExperience->end_date)->format('jS M, Y') }} </span>--}}
    {{ --                                @endif-- }}
    {{ --                                <span> | {{ $candidateExperience->country }}</span>--}}
    {{ --                                @if(!empty($candidateExperience->description))-- }}
    {{ --                                    <p class="mb-0 pb-md-0 pb-4">{{ Str::limit($candidateExperience->description,225,'...') }}</p>--}}
    {{ --                                @endif-- }}

    {{ --                                <div class="article-cta candidate-experience-edit-delete">-- }}
    {{ --                                    <a href="javascript:void(0)" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-yellow-500 text-white hover:bg-yellow-600 action- px-4 py-2 rounded font-medium transition-colors edit-experience" title="Edit"-- }}
    {{ --                                       data-id="{{ $candidateExperience->id }}"><i class="fa fa-edit p-1"></i></a>--}}
    {{ --                                    <a href="javascript:void(0)" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-red-600 text-white hover:bg-red-700 action- px-4 py-2 rounded font-medium transition-colors delete-experience" title="Delete"-- }}
    {{ --                                       data-id="{{ $candidateExperience->id }}"><i class="fa fa-trash p-1"></i></a>--}}
    {{ --                                </div>-- }}
    {{ --                            </div>-- }}
    {{ --                        </article>-- }}
    {{ --                    </div>-- }}
    {{ --                @endforeach-- }}
    {{ --            </div>-- }}
    {{ --        </div>-- }}
    {{ --    </section>-- }}
    {{ --    <br>-- }}
    {{ --    <section class="section">-- }}
    {{ --        <div class="section-header candidate-experience-header">-- }}
    {{ --            <h1>{{ __('messages.candidate_profile.education') }}</h1>--}}
    {{ --            <div class="section-header-breadcrumb justify-end">-- }}
    {{ --                <a-- }}
    {{ --                   class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 form- px-4 py-2 rounded font-medium transition-colors addEducationModal" data-bs-toggle="modal"-- }}
    {{ --                   data-bs-target="#addEducationModal">{{ __('messages.candidate_profile.add_education') }}--}}
    {{ --                    <i class="fas fa-plus"></i></a>-- }}
    {{ --            </div>-- }}
    {{ --        </div>-- }}
    {{ --        <div class="section-body">-- }}
    {{ --            <div class="flex flex-wrap candidate-education- container mx-auto px-4 mx-auto">-- }}
    {{ --                <div class="flex-1 -12 {{ ($data["candidateEducations']->count()) ? 'd-none' : '' }}" id="notfoundEducation">--}}
    {{ --                    <h4 class="product-item pb-5 flex justify-center">-- }}
    {{ --                        {{ __('messages.candidate.education_not_found') }}--}}
    {{ --                    </h4>-- }}
    {{ --                </div>-- }}
    {{ --                @php-- }}
    {{ --                    /** @var \App\Models\CandidateEducation $candidateEducation */-- }}
    {{ --                @endphp-- }}
    {{ --                @foreach($data['candidateEducations'] as $candidateEducation)-- }}
    {{ --                    <div class="w-full col-sm-12 md:w-full flex-1 lg-12 candidate-education"-- }}
    {{ --                         data-education-id="{{ $loop->index }}" data-id="{{ $candidateEducation->id }}">--}}
    {{ --                        <article class="article article-style-b">-- }}
    {{ --                            <div class="article-details">-- }}
    {{ --                                <div class="article-title">-- }}
    {{ --                                    <h4 class="text-primary-600 education-degree-level">{{ $candidateEducation->degreeLevel->name }}</h4>--}}
    {{ --                                    <h6 class="text-gray-500">{{ $candidateEducation->degree_title }}</h6>--}}
    {{ --                                </div>-- }}
    {{ --                                <span class="text-gray-500">{{ $candidateEducation->year }} | {{ $candidateEducation->country }}</span>--}}
    {{ --                                <p class="mb-0 pb-md-0 pb-4">{{ $candidateEducation->institute }}</p>--}}
    {{ --                                <div class="article-cta candidate-education-edit-delete">-- }}
    {{ --                                    <a href="javascript:void(0)" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-yellow-500 text-white hover:bg-yellow-600 action- px-4 py-2 rounded font-medium transition-colors edit-education" title="Edit"-- }}
    {{ --                                       data-id="{{ $candidateEducation->id }}"><i class="fa fa-edit p-1"></i></a>--}}
    {{ --                                    <a href="javascript:void(0)" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-red-600 text-white hover:bg-red-700 action- px-4 py-2 rounded font-medium transition-colors delete-education" title="Delete"-- }}
    {{ --                                       data-id="{{ $candidateEducation->id }}"><i class="fa fa-trash p-1"></i></a>--}}
    {{ --                                </div>-- }}
    {{ --                            </div>-- }}
    {{ --                        </article>-- }}
    {{ --                    </div>-- }}
    {{ --                @endforeach-- }}
    {{ --            </div>-- }}
    {{ --        </div>-- }}
    {{ --    </section>-- }}
    @include('candidate.profile.modals.add_experience_modal')
    @include('candidate.profile.modals.add_education_modal')
    @include('candidate.profile.modals.edit_experience_modal')
    @include('candidate.profile.modals.edit_education_modal')
    
    {{ Form::hidden('indexCareerInfoData',true,['id'=>'indexCareerInfoData']) }}
@endsection
@push('scripts')
    <script>
        {{ --let addExperienceUrl ="{{ route('candidate.experience.create') }}";--}}
        {{ --let experienceUrl ="{{ url('candidate/candidate-experience') }}/";--}}
        {{ --let addEducationUrl ="{{ route('candidate.education.create') }}";--}}
        {{ --let candidateUrl ="{{ url('candidate') }}/";--}}
        {{ --let educationUrl ="{{ url('candidate/candidate-education') }}/";--}}
        {{ --let present ="{{ __('messages.candidate_profile.present') }}";--}}
        // let isEdit = false;
    </script>
{{ --    <script src="{{ asset('assets/js/moment.min.js') }}"></script>--}}
    {{ ---- }}
    {{ --    <script src="{{mix('assets/js/candidate-profile/candidate_career_informations.js') }}"></script>--}}
@endpush
