<div class="flex-wrap flex">
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('first_name',__('messages.candidate.first_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('first_name', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' =>__('messages.candidate.first_name')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('last_name',__('messages.candidate.last_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('last_name', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' => __('messages.candidate.last_name')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('email',__('messages.candidate.email').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('email', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' => __('messages.candidate.email')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('father_name',__('messages.candidate.father_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        {{ Form::text('father_name', (isset($candidate) ? $candidate->father_name : ''), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => __('messages.candidate.father_name')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('dob', __('messages.candidate.birth_date').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        {{ --  Form::text('dob', (isset($candidate) ? $candidate->$user->dob : null), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'birthDate','autocomplete' => 'off', 'placeholder' => __('messages.candidate.birth_date')])  -- }}
        <input type="text" name="dob" id="birthDate"
               class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}"
               value="{{ (isset($candidate) ? $candidate->$user->dob : null) }}" autocomplete="off"
               placeholder="{{ __('messages.candidate.birth_date') }}">
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('gender', __('messages.candidate.gender').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        <br>
        <span class="flex items-center is-valid flex items-center sm">
                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.common.male') }}</label>&nbsp;&nbsp;
                {{ Form::radio('gender', '0', isset($candidate) ? $candidate->$user->gender == 0 : true, ['class' => 'flex items-center-input']) }} &nbsp;
                <br>
                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.common.female') }}</label>
                {{ Form::radio('gender', '1', isset($candidate) ? $candidate->$user->gender == 1 : true, ['class' => 'flex items-center-input']) }}
            </span>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('skill_id', __('messages.candidate.candidate_skill').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        <div class="flex-nowrap flex">
            {{ Form::select('candidateSkills[]',$data['skills'], (count($data['candidateSkills']) > 0)?$data['candidateSkills']:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm custom-select2','id'=>'skillId','multiple'=>true,'text-red-500']) }}
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0 justify-center">
                <a href="javascript:void(0)" class="text-gray-500 createCandidateSkillModal"><i
                            class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('language_id', __('messages.candidate.candidate_language').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        <div class="flex-nowrap flex">
            {{ Form::select('candidateLanguage[]',$data['language'], (count($data['candidateLanguage']) > 0) ? $data['candidateLanguage'] : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm custom-select2','id'=>'languageId','multiple'=>true,'text-red-500']) }}
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0 justify-center">
                <a href="javascript:void(0)" class="text-gray-500 createCandidateLanguageModal"><i
                            class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('marital_status', __('messages.candidate.marital_status').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        <div class="flex-nowrap flex">
            {{ Form::select('marital_status_id', $data['maritalStatus'], isset($candidate)?$candidate->marital_status_id:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','id' => 'maritalStatusId','placeholder'=>__('messages.candidate.marital_status')]) }}
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <a href="javascript:void(0)" class="text-gray-500 createCandidateMaritalStatusModal"><i
                            class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('nationality', __('messages.candidate.nationality').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        {{ Form::text('nationality', isset($candidate) ? $candidate->nationality : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder'=> __('messages.candidate.nationality')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('national_id_bg-white overflow-hidden shadow rounded-lg', __('messages.candidate.national_id_bg-white overflow-hidden shadow rounded-lg').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        {{ Form::text('national_id_bg-white overflow-hidden shadow rounded-lg', isset($candidate) ? $candidate->national_id_bg-white overflow-hidden shadow rounded-lg : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => __('messages.candidate.national_id_bg-white overflow-hidden shadow rounded-lg')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <div class="flex-nowrap flex">
            {{ Form::select('country_id', $data['countries'], null, ['id'=>'countryId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_country')]) }}
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <a href="javascript:void(0)" class="text-gray-500 createCandidateCountryModal"><i
                            class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <div class="flex-nowrap flex">
            {{ Form::select('state_id', (isset($userStates) && $userStates!=null?$userStates:[]), null, ['id'=>'stateId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_state')]) }}
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <a href="javascript:void(0)" class="text-gray-500 createCandidateStateModal"><i
                            class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <div class="flex-nowrap flex">
            {{ Form::select('city_id', (isset($userCities) && $userCities!=null?$userCities:[]), null, ['id'=>'cityId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_city')]) }}
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <a href="javascript:void(0)" class="text-gray-500 createCandidateCityModal"><i
                            class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('phone', __('messages.candidate.phone').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::tel('phone', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','id'=>'phoneNumber','placeholder' => __('messages.inquiry.phone_no')]) }}
        {{ Form::hidden('region_code',null,['id'=>'prefix_code']) }}
        <br>
        <p id="valid-msg" class="mt-2 text-green-600 hidden fw-400 fs-small">{{ __('messages.phone.valid_number') }}</p>
        <p id="error-msg" class="mt-2 text-red-600 hidden fw-400 fs-small">{{ __('messages.phone.invalid_number') }}</p>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('experience', __('messages.candidate.experience').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span>({{ __('messages.candidate.in_years') }})</span>
        {{ Form::text('experience', isset($candidate) ? $candidate->experience : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','min' => '0', 'max' => '15', 'oninput'=>"validity.valid||(value='');", 'placeholder' => __('messages.candidate.experience'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('career_level', __('messages.candidate.career_level').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex-nowrap flex">
            {{ Form::select('career_level_id', $data['careerLevel'], isset($candidate)?$candidate->career_level_id:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'careerLevelId','placeholder'=>__('messages.company.select_career_level')]) }}
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <a href="javascript:void(0)" class="text-gray-500 createCandidateCareerLevelModal"><i
                            class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('industry', __('messages.candidate.industry').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <div class="flex-nowrap flex">
            {{ Form::select('industry_id', $data['industry'], isset($candidate)?$candidate->industry_id:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'industryId','placeholder'=> __('messages.company.select_industry')]) }}
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <a href="javascript:void(0)" class="text-gray-500 createCandidateIndustryModal"><i
                            class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('functional_area', __('messages.candidate.functional_area').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <div class="flex-nowrap flex">
            {{ Form::select('functional_area_id', $data['functionalArea'], isset($candidate)?$candidate->functional_area_id:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'functionalAreaId','placeholder'=> __('messages.company.select_functional_area')]) }}
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <a href="javascript:void(0)" class="text-gray-500 createCandidateFunctionalAreaModal"><i
                            class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('current_salary', __('messages.candidate.current_salary').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        {{ Form::text('current_salary', isset($candidate) ? number_format($candidate->current_salary) : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm price-input', 'min' => 0, 'max' => 999999999,'placeholder' => __('messages.candidate.current_salary')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('expected_salary', __('messages.candidate.expected_salary').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        {{ Form::text('expected_salary', isset($candidate) ? number_format($candidate->expected_salary) : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm price-input', 'min' => 0, 'max' => 999999999, 'placeholder' => __('messages.candidate.expected_salary')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('salary_currency', __('messages.candidate.salary_currency').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        {{ Form::select('salary_currency', $data['currency'],isset($candidate) && isset($candidate->salary_currency) ? $candidate->salary_currency : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'id' => 'salaryCurrencyId', 'placeholder' => __('messages.candidate.salary_currency')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('facebook_url', __('messages.company.facebook_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="text-indigo-600 fab fa-facebook-f facebook-fa-icon -600"></i>
            </div>
            {{ Form::text('facebook_url',null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'facebookUrl','placeholder'=>'https://www.facebook.com']) }}
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('twitter_url', __('messages.company.twitter_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="text-indigo-600 fab fa-twitter twitter-fa-icon -600"></i>
            </div>
            {{ Form::text('twitter_url', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'twitterUrl','placeholder'=>'https://www.twitter.com']) }}
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('linkedin_url', __('messages.company.linkedin_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="text-indigo-600 fab fa-linkedin-in linkedin-fa-icon -600"></i>
            </div>
            {{ Form::text('linkedin_url', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'linkedInUrl','placeholder'=>'https://www.linkedin.com']) }}
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('google_plus_url', __('messages.company.google_plus_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="fab fa-google-plus-g google-plus-fa-icon text-red-600"></i>
            </div>
            {{ Form::text('google_plus_url', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'googlePlusUrl','placeholder'=>'https://www.plus.google.com']) }}
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('pinterest_url', __('messages.company.pinterest_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="fab fa-pinterest-p pinterest-fa-icon text-red-600"></i>
            </div>
            {{ Form::text('pinterest_url', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'pinterestUrl','placeholder'=>'https://www.pinterest.com']) }}
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        <div class="flex-wrap flex">
            <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                {{ Form::label('immediate_available', __('messages.candidate.immediate_available').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
                <br>
                {{ Form::radio('immediate_available', '1', isset($candidate) ? $candidate->immediate_available == 1 : true, ['class' => 'flex items-center-input']) }}
                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.candidate.immediate_available') }}</label>
                <br>
                {{ Form::radio('immediate_available', '0', isset($candidate) ? $candidate->immediate_available == 0 : true,['class' => 'flex items-center-input']) }}
                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.candidate.not_immediate_available') }}</label>
            </div>
            <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
                <div class="flex-wrap flex">
                    <div class="pt-1 mb-0 md:w-4/12 flex-1 sm-12">
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.common.status').':' }}</label><br>
                        <label class="flex items-center form-switch  form-switch-sm">
                            <input type="checkbox" name="is_active" class="flex items-center input isActive"
                                   value="1"
                                   id="active" {{ (isset($candidate) && ($candidate->$user->is_active)) ? 'checked' : '' }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </div>
                    <div class="pt-1 mb-0 md:w-8/12 flex-1 sm-12">
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.candidate.is_verified').':' }}</label><br>
                        <label class="flex items-center form-switch  form-switch-sm">
                            <input type="checkbox" name="is_verified" class="flex items-center input"
                                   value="1"
                                   id="verified" {{ (isset($candidate) && ($candidate->$user->is_verified) && ($candidate->$user->email_verified_at)) ? 'checked disabled' : '' }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('available_at', __('messages.candidate.available_at').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <input type="text" name="available_at" id="availableAt"
               class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}" autocomplete="off"
               placeholder="{{ __('messages.candidate.available_at') }}"
               value="{{ isset($user->$candidate->available_at) ? $user->$candidate->available_at : null }}">
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('address', __('messages.candidate.address').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        {{ Form::textarea('address', isset($candidate) ? $candidate->address : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm address-height','flex flex-wrap -mx-4s'=>'5', 'placeholder' => __('messages.candidate.address')]) }}
    </div>
    <div class="flex justify-content-endmt-5">
        {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-flex-1 px-4ors me-3']) }}
        <a href="{{ route('candidates.index') }}"
           class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
    </div>
</div>
