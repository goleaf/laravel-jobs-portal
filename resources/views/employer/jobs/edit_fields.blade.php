<div class="flex flex-wrap">
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_title', __('messages.job.job_title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('job_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' => __('messages.job.job_title')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_type_id', __('messages.job.job_type').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('job_type_id', $data['jobType'],null, ['id'=>'jobTypeId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' => __('messages.company.select_job_type'),'text-red-500','placeholder' => __('messages.job.job_type')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_category_id', __('messages.job_category.job_category').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('job_category_id', $data['jobCategory'],null, ['id'=>'jobCategoryId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' => __('messages.company.select_job_category'),'text-red-500']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('skill_id', __('messages.job.job_skill').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('jobsSkill[]',$data['jobSkill'], null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','id'=>'SkillId','multiple'=>true,'text-red-500']) }}
    </div>
    <div class="flex-1 -xl-12 md:w-full flex-1 sm-12 mb-5">
        {{ Form::label('description', __('messages.job.description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ --  Form::textarea('description', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm' , 'id' => 'details', 'flex flex-wrap -mx-4s' => '5'])  -- }}
        <div id="editDetails"></div>
        {{ Form::hidden('description',$job->description, ['id' => 'editJobDescription']) }}
    </div>
    <div class="flex-1 -xl-12 md:w-6/12 flex-1 sm-12 mb-5">
         {{ Form::label('key_responsibilities', __('messages.job.key_responsibilities') . ':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}<span
             class="required"></span>
         <div id="editResponse"></div>
         {{ Form::hidden('key_responsibilities', $job->key_responsibilities, ['id' => 'edit_responsibilities']) }}
     </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('no_preference', __('messages.candidate.gender').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('no_preference', $data['preference'], null, ['id'=>'preferenceId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' => __('messages.company.select_gender')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_expiry_date', __('messages.job.job_expiry_date').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded -l-md text-gray-500 border border border-gray-300 -gray-300 -0">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <input type="text" name="job_expiry_date" id="availableAt" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 expiryDatepicker {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}" autocomplete="off" value="{{ isset($job->job_expiry_date) ? $job->job_expiry_date : null, }}" placeholder="{{ __('messages.job.job_expiry_date') }}">
        </div>
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('salary_from', __('messages.job.salary_from').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('salary_from', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'id' => 'fromSalary', 'text-red-500', 'autocomplete' => 'off', 'placeholder' => __('messages.job.salary_from')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('salary_to', __('messages.job.salary_to').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('salary_to', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'id' => 'toSalary', 'text-red-500', 'autocomplete' => 'off', 'placeholder' => __('messages.job.salary_to')]) }}
        <span id="salaryToErrorMsg" class="text-red-600"></span>
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('currency_id', __('messages.job.currency').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('currency_id', $data['currencies'], null, ['id'=>'currencyId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' => __('messages.company.select_currency'),'text-red-500']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('salary_period_id', __('messages.job.salary_period').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('salary_period_id', $data['salaryPeriods'], null, ['id'=>'salaryPeriodsId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' => __('messages.company.select_salary_period'),'text-red-500']) }}
    </div>
    <div class="flex-1 -xl-4 md:w-4/12 flex-1 sm-12 mb-5">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'countryId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' => __('messages.company.select_country')]) }}
    </div>
    <div class="flex-1 -xl-4 md:w-4/12 flex-1 sm-12 mb-5">
        {{ Form::label('state', __('messages.job.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('state_id', (isset($states) && $states!=null?$states:[]), null, ['id'=>'stateId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' => __('messages.company.select_state')]) }}
    </div>
    <div class="flex-1 -xl-4 md:w-4/12 flex-1 sm-12 mb-5">
        {{ Form::label('city', __('messages.job.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('city_id', (isset($cities) && $cities!=null?$cities:[]), null, ['id'=>'cityId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_city'),'data-control'=>'select2','text-red-500']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('career_level_id', __('messages.job.career_level').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('career_level_id', $data['careerLevels'],null, ['id'=>'careerLevelsId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' =>__('messages.company.select_career_level')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('job_shift_id', __('messages.job.job_shift').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('job_shift_id', $data['jobShift'], null, ['id'=>'jobShiftId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' => __('messages.company.select_job_shift')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('tagId', __('messages.job_tag.show_job_tag').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('jobTag[]',$data['jobTag'], (count($data['jobTags']) > 0)?$data['jobTags']:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'tagId','data-control'=>'select2','multiple'=>true]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('degree_level_id', __('messages.job.degree_level').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('degree_level_id', $data['requiredDegreeLevel'], null, ['id'=>'requiredDegreeLevelId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-control'=>'select2','placeholder' => __('messages.company.select_degree_level')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('functional_area_id', __('messages.job.functional_area').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('functional_area_id', $data['functionalArea'], null, ['id'=>'functionalAreaId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_functional_area'),'data-control'=>'select2','text-red-500']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('position', __('messages.job.position').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('position',  null, ['id'=>'positionId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_position'),'text-red-500', 'min' => 0, 'max' => 255, 'placeholder' => __('messages.job.position'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('experience', __('messages.job_experience.job_experience').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('experience',  null, ['id'=>'experienceId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.enter_experience_year'),'text-red-500', 'min' => 0, 'max' => 255, 'placeholder' => __('messages.job_experience.job_experience'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="flex-1 -xl-3 md:w-3/12 flex-1 sm-12 mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.job.hide_salary').':' }}</label>
        <label class="flex items-center form-switch form-switch-sm">
            <input type="checkbox" name="hide_salary" class="flex items-center input"
                   value="1"
                   id="salary" {{ $job->hide_salary == 1? 'checked' : '' }}>
        </label>
    </div>
    <div class="flex-1 -xl-3 md:w-3/12 flex-1 sm-12 mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.job.is_freelance').':' }}</label>
        <label class="flex items-center form-switch form-switch-sm">
            <input type="checkbox" name="is_freelance" class="flex items-center input"
                   value="1"
                   id="freelance" {{ $job->is_freelance == 1? 'checked' : '' }}>
        </label>
    </div>

    <!-- Submit Field -->
    <div class="flex justify-end mt-5">
        {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-flex-1 px-4ors me-3','id' => 'editJobsSaveBtn','data-loading-text' =>"<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span>".__('messages.common.process')]) }}
        <a href="{{ route('employer.jobs.index') }}"
           class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
    </div>

</div>
