@extends('candidate.profile.index')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/inttel/css/intlTelInput.css') }}">@endpush
@section('section')
    <div class="overflow-hidden shadow rounded bg-white -lg">
        <div class="overflow-hidden shadow rounded bg-white -lg body">
            @formOpen(['route' => 'candidate-profile.update', 'files' => true, 'id' => 'candidateProfileUpdate', 'method' => 'put'])
            <div class="mt-5">
                <div class="rounded border p-4 mb-4 rounded border mb-4 px-4 py-3 -md -gray-300 -md danger hidden" id="validationErrors">
                    <i class='flex-wrap fa-solid fa-face-fflex -mx-4n me-4'></i>
                </div>
                <div class="flex-wrap mb-5 flex">
                    {{ Form::hidden('isEdit',true,['id' => 'isEdit']) }}
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-6">
                        {{ Form::label('first_name',__('messages.candidate.first_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('first_name', $user->first_name, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','placeholder'=> __('messages.candidate.first_name')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-6">
                        {{ Form::label('last_name', __('messages.candidate.last_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('last_name', $user->last_name,['class' =>'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','text-red-500','placeholder'=> __('messages.candidate.last_name')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-6">
                        {{ Form::label('email', __('messages.company.email').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::email('email', isset($user)?$user->email:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ', 'text-red-500','placeholder'=> __('messages.company.email')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-6">
                        {{ Form::label('father_name', __('messages.candidate.father_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('father_name', $user->$candidate->father_name, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','placeholder'=> __('messages.candidate.father_name')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-6">
                        {{ Form::label('dob',__('messages.candidate.birth_date').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <input type="text" name="dob" id="birthDate"
                               class="rounded border border border w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}"
                               autocomplete="off" placeholder="{{ __('messages.candidate.birth_date') }}"
                               value="{{ $user->dob }}">
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('gender', __('messages.candidate.gender').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        <br>
                        <span class="flex items-center is-valid flex items-center sm">
                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.common.male') }}</label>&nbsp;&nbsp;
                {{ Form::radio('gender', '0', isset($user->gender) ? $user->gender == 0 : true, ['class' => 'flex items-center-input','id'=>'male']) }} &nbsp;
                <br>
                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.common.female') }}</label>
                {{ Form::radio('gender', '1', isset($user->gender) ? $user->gender == 1 : true, ['class' => 'flex items-center-input','id'=>'female']) }}
            </span>

                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('skill_id', __('messages.candidate.candidate_skill').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::select('candidateSkills[]',$data['skills'], (count($candidateSkills) > 0)?$candidateSkills:null,  ['id'=>'skillId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','multiple'=>true,'text-red-500']) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('language_id', __('messages.candidate.candidate_language').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::select('candidateLanguage[]',$data['language'], (count($candidateLanguage) > 0) ? $candidateLanguage : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ', 'id'=>'languageId','multiple'=>true,'text-red-500']) }}
                    </div>

                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('marital_status', __('messages.candidate.marital_status').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::select('marital_status_id', $data['maritalStatus'], isset($user->$candidate->marital_status_id) ? $user->$candidate->marital_status_id : null,  ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ', 'id'=>'maritalStatusId','text-red-500']) }}

                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('nationality', __('messages.candidate.nationality').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::text('nationality', isset($user->$candidate->nationality) ? $user->$candidate->nationality : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','placeholder' => __('messages.candidate.nationality')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('national_id_bg-white overflow-hidden shadow rounded-lg', __('messages.candidate.national_id_bg-white overflow-hidden shadow rounded-lg').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::text('national_id_bg-white overflow-hidden shadow rounded-lg', isset($user->$candidate->national_id_bg-white overflow-hidden shadow rounded-lg) ? $user->$candidate->national_id_bg-white overflow-hidden shadow rounded-lg : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ', 'placeholder' => __('messages.candidate.national_id_bg-white overflow-hidden shadow rounded-lg') ]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            {{ Form::select('country_id',  $data['countries'], null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','id'=>'countryId','placeholder' => __('messages.company.select_country')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            {{ Form::select('state_id', (isset($states) && $states!=null?$states:[]), null, ['id'=>'stateId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_state')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            {{ Form::select('city_id',(isset($cities) && $cities!=null?$cities:[]), null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','id'=>'cityId','placeholder' => __('messages.company.select_city')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6 mobile-itel-width">
                        {{ Form::label('phone',__('messages.candidate.phone').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <div class="mb-5 flex-1 sm-12">
                            {{ Form::tel('phone', isset($user->phone) ? $user->phone : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','id'=>'phoneNumber']) }}
                </div>
                {{ Form::hidden('region_code',null,['id'=>'prefix_code']) }}
                        <span id="valid-msg" class="mt-2 text-green-600 block fw-400 fs-small hidden">{{ __('messages.phone.valid_number') }}</span>
                        <span id="error-msg" class="mt-2 text-red-600 block fw-400 fs-small hidden"></span>
                    </div>

                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('experience', __('messages.candidate.experience').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::text('experience', isset($user->$candidate->experience) ? $user->$candidate->experience : null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','min' => '0', 'max' => '15','placeholder'=>__('messages.candidate.experience'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('career_level', __('messages.candidate.career_level').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                {{ Form::select('career_level_id',  $data['careerLevel'], isset($user->$candidate->career_level_id) ? $user->$candidate->career_level_id : null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'careerLevelId', 'placeholder'=> __('messages.company.select_career_level')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('industry', __('messages.candidate.industry').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                {{ Form::select('industry_id',  $data['industry'], isset($user->$candidate->industry_id) ? $user->$candidate->industry_id : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'industryId','placeholder'=>__('messages.company.select_industry')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('functional_area', __('messages.candidate.functional_area').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                {{ Form::select('functional_area_id', $data['functionalArea'], isset($user->$candidate->functional_area_id) ? $user->$candidate->functional_area_id : null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'functionalAreaId', 'placeholder'=> __('messages.company.select_functional_area')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('current_salary', __('messages.candidate.current_salary').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                {{ Form::text('current_salary',  isset($user->$candidate->current_salary) ? $user->$candidate->current_salary : null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder'=> __('messages.candidate.current_salary')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('expected_salary',  __('messages.candidate.expected_salary').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                {{ Form::text('expected_salary', isset($user->$candidate->expected_salary) ? $user->$candidate->expected_salary : null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder'=>__('messages.candidate.expected_salary')]) }}
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('salary_currency', __('messages.candidate.salary_currency').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                {{ Form::select('salary_currency',   $data['currency'], isset($user->$candidate->salary_currency) ? $user->$candidate->salary_currency : null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'salaryCurrencyId','placeholder'=> __('messages.company.select_currency')]) }}
                    </div>

                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('immediate_available', __('messages.candidate.immediate_available').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                <br>
                <span class="flex items-center is-valid flex items-center sm">
                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.candidate.immediate_available') }}</label>&nbsp;&nbsp;
                {{ Form::radio('immediate_available', '1', isset($user->$candidate->immediate_available) ? $user->$candidate->immediate_available == 1 : true, ['class' => 'flex items-center-input','id'=>'available']) }} &nbsp;
                <br>
                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.candidate.not_immediate_available') }}</label>
                {{ Form::radio('immediate_available', '0', isset($user->$candidate->immediate_available) ? $user->$candidate->immediate_available == 0 : true, ['class' => 'flex items-center-input','id'=>'not_available']) }}
            </span>


                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12 available-at">
                        {{ Form::label('available_at', __('messages.candidate.available_at').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1  mb-3']) }}
                        <input type="text" name="available_at" id="availableAt"
                               class="rounded border border border w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}"
                               placeholder="{{ __('messages.candidate.available_at') }}"
                               value="{{ isset($user->$candidate->available_at) ? $user->$candidate->available_at : null }}">
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('facebook_url', __('messages.company.facebook_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <div class="flex">
                            <div class="rounded border border border border border px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                                <i class="text-indigo-600 fab fa-facebook-f facebook-fa-icon -600"></i>
                            </div>
                            {{ Form::text('facebook_url', $user->facebook_url, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'facebookUrl','placeholder'=>'https://www.facebook.com']) }}
                        </div>
                    </div>

                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('twitter_url', __('messages.company.twitter_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                <div class="flex">
                    <div class="rounded border border border border border px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                        <i class="text-indigo-600 fab fa-twitter twitter-fa-icon -600"></i>
                    </div>
                    {{ Form::text('twitter_url', $user->twitter_url, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'twitterUrl','placeholder'=>'https://www.twitter.com']) }}
                </div>
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('linkedin_url', __('messages.company.linkedin_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                <div class="flex">
                    <div class="rounded border border border border border px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                        <i class="text-indigo-600 fab fa-linkedin-in linkedin-fa-icon -600"></i>
                    </div>
                    {{ Form::text('linkedin_url', $user->linkedin_url, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'linkedInUrl','placeholder'=>'https://www.linkedin.com']) }}
                </div>
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('google_plus_url', __('messages.company.google_plus_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                <div class="flex">
                    <div class="rounded border border border border border px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                        <i class="fab fa-google-plus-g google-plus-fa-icon text-red-600"></i>
                    </div>
                    {{ Form::text('google_plus_url', $user->google_plus_url ,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'googlePlusUrl','placeholder'=>'https://www.plus.google.com']) }}
                </div>
                    </div>

                    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('pinterest_url', __('messages.company.pinterest_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                <div class="flex">
                    <div class="rounded border border border border border px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                        <i class="fab fa-pinterest-p pinterest-fa-icon text-red-600"></i>
                    </div>
                    {{ Form::text('pinterest_url', $user->pinterest_url, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'pinterestUrl','placeholder'=>'https://www.pinterest.com']) }}
                </div>
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        <div class="mb-3" io-image-input="true">
                            <label for="exampleInputImage" class="mb-1 block text-sm font-medium text-gray-700"> {{ __('messages.candidate.profile') }}:</label>
                    <span data-bs-toggle="tooltip"
                          data-placement="top"
                          data-bs-original-title="{{ __('messages.setting.image_validation') }}">
                                <i class="ml-1 fas fa-question-circle general-question-mark"></i>
                        </span>
                    <div class="block">
                        <div class="image-picker">
                            <div class="image previewImage" id="exampleInputImage" style="background-image: url({{ (!empty($user->media[0]))? $user->media[0]->getFullUrl() : asset('assets/img/infyom-logo.png') }})">
                            </div>
                            <span class="rounded picker-edit -full text-gray-500 fs-small" data-bs-toggle="tooltip"
                                  data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_profile') }}">
                        <label> 
                            <i class="fa-solid fa-pen" id="profileImageIcon"></i> 
                            <input type="file" name="image" class="image-upload hidden" accept="image/*"/> 
                        </label> 
                    </span>
                        </div>
                    </div>
                        </div>
                    </div>
                    <div class="mb-5 flex-1 px-4 -xl-12 md:w-6/12 flex-1 sm-12">
                        {{ Form::label('address',__('messages.candidate.address').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            {{ Form::textarea('address', isset($user->$candidate->address) ? $user->$candidate->address : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','flex flex-wrap -mx-4s'=>'5','placeholder'=>__('messages.candidate.address')]) }}
        </div>

        <!-- Submit Field -->
        <div class="flex justify-end">
            {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200Save']) }}
            {{-- <a href="" --}}
            {{ -- class="border border-gray-300 bg-transparent">__('messages.common.cancel') </a> -- }}
        </div>
    </div>
</div>
    </div>
</div>
    @formClose()
@endsection
@push('scripts')
    
    {{ -- <script src="mix('assets/js/custom/input_price_format.js') "></script> -- }}
    {{ -- <script src="mix('assets/js/candidate-profile/candidate-general.js') "></script> -- }}
    {{ -- <script src=" mix('assets/js/custom/phone-number-country-code.js') "></script> -- }}
@endpush

@push('scripts')
    @vite('resources/js/components/general.js')
@endpush
