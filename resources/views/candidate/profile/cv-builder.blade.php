@extends('candidate.profile.index')
@push('css')@endpush
@section('section')
    
            <div class="flex-wrap flex">
                <div class="flex justify-end">
                    <a href="#cvModal" role="button" class="border border-gray-300 bg-transparent"
                       data-bs-toggle="modal">{{ __('messages.common.preview') }}</a>
                </div>
                <div class="overflow-hidden shadow rounded mt-5 bg-white shadow flex-1 md-12 p-9 -lg">
                    {{-- General Section --}}
                    <div id="candidateGeneralDiv">
                        @include('candidate.profile.career_informations.show_general')
                    </div>
                    <div class="hidden" id="editGeneralDiv">
                        @include('candidate.profile.career_informations.edit_general')
                    </div>
                    {{-- Education Section --}}
                    <div class="border border border -b -red-600 my-5 -2 flex justify-between">
                        <h5 class="mt-2 fs-2 text-blue-500"><i
                                class="rounded border p-3 border border fas fa-user-graduate text-blue-500 -gray-300 -circle -info me-3"></i>{{ __('messages.candidate_profile.education') }}
                        </h5>
                        <a href="javascript:void(0)" class="addEducationBtn">
                            <i class="text-indigo-600 fas fa-plus-circle fa-2x -600"></i>
                        </a>
                    </div>
                    <div class="section-body">
                        <div class="flex-wrap max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex candidate-education- mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto" id="candidateEducationsDiv">
                            @include('candidate.profile.career_informations.show_education')
                        </div>
                        <div class="hidden" id="createEducationsDiv">
                            @include('candidate.profile.career_informations.create_education')
                        </div>
                        <div class="hidden" id="editEducationsDiv">
                            @include('candidate.profile.career_informations.edit_education')
                        </div>
                    </div>
                    {{-- Experience Section --}}
                    <div class="border border border -b my-5 -red-600 -2 flex justify-between">
                        <h5 class="mt-2 fs-2 text-blue-500"><i
                                class="rounded border p-3 border border fas fa-briefcase text-blue-500 -gray-300 -circle -info me-3"></i>{{ __('messages.candidate_profile.experience') }}
                        </h5>
                        <a href="javascript:void(0)" class="addExperienceBtn">
                            <i class="text-indigo-600 fas fa-plus-circle fa-2x -600"></i>
                        </a>
                    </div>
                    <div class="section-body">
                        <div class="flex-wrap max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex candidate-experience- mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto" id="candidateExperienceDiv">
                            @include('candidate.profile.career_informations.show_experience')
                        </div>
                        <div class="hidden" id="createExperienceDiv">
                            @include('candidate.profile.career_informations.create_experience')
                        </div>
                        <div class="hidden" id="editExperienceDiv">
                            @include('candidate.profile.career_informations.edit_experience')
                        </div>
                    </div>
                    {{-- Online Profile Section --}}
                    <div class="border border border -b my-5 -red-600 -2 flex justify-between">
                        <h5 class="mt-2 fs-2 text-blue-500"><i
                                class="rounded border p-3 border border fas fa-link text-blue-500 -gray-300 -circle -info me-3"></i>{{ __('messages.candidate_profile.online_profile') }}
                        </h5>
                        <a href="javascript:void(0)" class="addOnlineProfileBtn">
                            <i class="text-indigo-600 fas fa-plus-circle fa-2x -600"></i>
                        </a>
                    </div>
                    <div class="section-body">
                        <div class="flex-wrap flex" id="candidateOnlineProfileDiv">
                            @include('candidate.profile.career_informations.show_online_profile')
                        </div>
                        <div class="hidden" id="addOnlineProfileDiv">
                            @include('candidate.profile.career_informations.edit_online_profile')
                        </div>
                    </div>
                </div>
            </div>
            @include('candidate.profile.modals.cv_preview_model')
            
            {{ Form::hidden('plugin-url', asset('css/plugins.css'), ['id' => 'pluginUrl']) }}
            {{ Form::hidden('style-css-url', asset('assets/css/style.css'), ['id' => 'styleCssUrl']) }}
            {{ Form::hidden('font-css-url', asset('assets/css/font-awesome.min.css'), ['id' => 'fontCssUrl']) }}
            {{ Form::hidden('isEditProfile', true, ['id' => 'isEditProfile']) }}
            {{ Form::hidden('countryId', $user->country_id, ['id' => 'countryId']) }}
            {{ Form::hidden('stateId', $user->state_id, ['id' => 'stateId']) }}
            {{ Form::hidden('cityId', $user->city_id, ['id' => 'cityId']) }}
            {{ Form::hidden('present', __('messages.candidate_profile.present'), ['id' => 'cvPresent']) }}
            {{ Form::hidden('cvBuilderData',true, ['id' => 'indexCvBuilderData']) }}
@endsection
@push('scripts')
    
{{ -- <script src=" asset('assets/js/moment.min.js') "></script> -- }}
    {{--  --}}
{{ -- <script src=" asset('js/html2pdf.bundle.min.js') "></script> -- }}
    {{ -- <script src="mix('assets/js/candidate-profile/candidate-education-experience.js') "></script> -- }}
    {{ -- <script src="mix('assets/js/candidate-profile/cv-builder.js') "></script> -- }}
@endpush

@push('scripts')
    @vite('resources/js/components/cv-builder.js')
@endpush
