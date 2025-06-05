<div class="flex flex-wrap">
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_title', __('messages.job.job_title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('job_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required', 'placeholder' => __('messages.job.job_title')]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_type_id', __('messages.job.job_type').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('job_type_id', $data['jobType'],null, ['id'=>'jobTypeId','class' => 'form-select','placeholder' => __('messages.company.select_job_type'),'data-control'=>'select2','required']) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_category_id', __('messages.job_category.job_category').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('job_category_id', $data['jobCategory'],null, ['id'=>'jobCategoryId','class' => 'form-select','placeholder' => __('messages.company.select_job_category'),'data-control'=>'select2','required']) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('skill_id', __('messages.job.job_skill').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('jobsSkill[]',$data['jobSkill'], null, ['class' => 'form-select','id'=>'SkillId','multiple'=>true,'data-control'=>'select2','required']) }}
    </div>
    <div class="col-xl-12 md:w-full flex-1 sm-12 mb-5">
        {{ Form::label('description', __('messages.job.description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        <div id="details"></div>
        {{ Form::hidden('description', null, ['id' => 'job_desc']) }}
    </div>
        <div class="col-xl-12 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('key_responsibilities', __('messages.job.key_responsibilities') . ':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}<span
            class="required"></span>
        <div id="response"></div>
        {{ Form::hidden('key_responsibilities', null, ['id' => 'key_responsibilities']) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('no_preference', __('messages.candidate.gender').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('no_preference', $data['preference'], null, ['id'=>'preferenceId','class' => 'form-select','data-control'=>'select2','placeholder' => __('messages.company.select_gender')]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_expiry_date', __('messages.job.job_expiry_date').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <input type="text" name="job_expiry_date" id="availableAt" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 expiryDatepicker  {{ (getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white' }}" autocomplete="off" value="{{ isset($job->job_expiry_date) ? $job->job_expiry_date : null, }}" placeholder="{{ __('messages.job.job_expiry_date') }}">
{{ --            {{ Form::text('job_expiry_date', isset($job->job_expiry_date) ? $job->job_expiry_date : null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm expiryDatepicker', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.job.job_expiry_date')]) }}--}}
        </div>
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('salary_from', __('messages.job.salary_from').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('salary_from', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'id' => 'fromSalary', 'required', 'placeholder' => __('messages.job.salary_from')]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('salary_to', __('messages.job.salary_to').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('salary_to', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'id' => 'toSalary', 'required', 'placeholder' =>__('messages.job.salary_to')]) }}
        <span id="salaryToErrorMsg" class="text-red-600"></span>
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('currency_id', __('messages.job.currency').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('currency_id', $data['currencies'], null,
                ['id'=>'currencyId','class' => 'form-select','placeholder' => __('messages.company.select_currency'),'data-control'=>'select2','required']) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('salary_period_id', __('messages.job.salary_period').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('salary_period_id', $data['salaryPeriods'], null, ['id'=>'salaryPeriodsId','class' => 'form-select','placeholder' => __('messages.company.select_salary_period'),'data-control'=>'select2','required']) }}
    </div>
    <div class="col-xl-4 md:w-4/12 flex-1 sm-12 mb-5">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'countryId','class' => 'form-select','placeholder' => __('messages.company.select_country'),'data-control'=>'select2','required']) }}
    </div>
    <div class="col-xl-4 md:w-4/12 flex-1 sm-12 mb-5">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('state_id', [], null, ['id'=>'stateId','class' => 'form-select','placeholder' => __('messages.company.select_state'),'data-control'=>'select2','required']) }}
    </div>
    <div class="col-xl-4 md:w-4/12 flex-1 sm-12 mb-5">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('city_id', [], null, ['id'=>'cityId','class' => 'form-select','placeholder' => __('messages.company.select_city'),'data-control'=>'select2','required']) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('career_level_id', __('messages.job.career_level').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('career_level_id', $data['careerLevels'],null, ['id'=>'careerLevelsId','class' => 'form-select','data-control'=>'select2','placeholder' => __('messages.company.select_career_level')]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_shift_id', __('messages.job.job_shift').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('job_shift_id', $data['jobShift'], null, ['id'=>'jobShiftId','class' => 'form-select','data-control'=>'select2','placeholder' => __('messages.company.select_job_shift')]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('tagId', __('messages.job_tag.show_job_tag').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('jobTag[]',$data['jobTag'], null, ['class' => 'form-select','id'=>'tagId','data-control'=>'select2','multiple'=>true]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('degree_level_id', __('messages.job.degree_level').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('degree_level_id', $data['requiredDegreeLevel'], null, ['id'=>'requiredDegreeLevelId','class' => 'form-select','data-control'=>'select2','placeholder' => __('messages.company.select_degree_level')]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('functional_area_id', __('messages.job.functional_area').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('functional_area_id', $data['functionalArea'], null, ['id'=>'functionalAreaId','class' => 'form-select','placeholder' => __('messages.company.select_functional_area'),'data-control'=>'select2','required']) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('position', __('messages.job.position').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('position',  null, ['id'=>'positionId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_position'),'required', 'min' => 0, 'max' => 255, 'placeholder' => __('messages.job.position'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('experience', __('messages.job_experience.job_experience').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('experience',  null, ['id'=>'experienceId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.enter_experience_year'),'required', 'min' => 0, 'max' => 255, 'placeholder' => __('messages.job_experience.job_experience'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="col-xl-3 md:w-3/12 flex-1 sm-12 mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.job.hide_salary').':' }}</label><br>
        <label class="flex items-center form-switch form-switch-sm">
            <input type="checkbox" name="hide_salary" class="flex items-center input"
                   value="1"
                   id="salary">
        </label>
    </div>
    <div class="col-xl-3 md:w-3/12 flex-1 sm-12 mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.job.is_freelance').':' }}</label><br>
        <label class="flex items-center form-switch form-switch-sm">
            <input type="checkbox" name="is_freelance" class="flex items-center input"
                   value="1"
                   id="freelance">
        </label>
    </div>
    <!-- Submit Field -->
    <div class="flex justify-end mt-5">
        <input name="saveAsDraft" type="hidden" value="" id="saveAsDraft">
        {{ Form::button(__('messages.common.save_as_draft'), ['type' => 'submit', 'name' => 'saveDraft', 'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3 saveDraft','id' => 'saveDraft','value'=>'draft','data-loading-text' =>"<span class="spinner-border spinner-border-sm"></span>".__('messages.common.process')]) }}
        {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'name' => 'save', 'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','id' => 'jobsSaveBtn','data-loading-text' =>"<span class="spinner-border spinner-border-sm"></span>".__('messages.common.process')]) }}
        <a href="{{ route('job.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary me-2">{{ __('messages.common.cancel') }}</a>
    </div>

</div>
