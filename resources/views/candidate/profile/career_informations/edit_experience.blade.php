@formOpen(['id' => 'editCVExperienceForm'])
<div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger hidden" id="editValidationErrorsBox">
    <i class='fa-solid fa-face-frown me-4'></i>
</div>
<input type="hidden" id="experienceId">
<div class="flex flex-wrap">
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('experience_title',__('messages.candidate_profile.experience_title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('experience_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required', 'id' => 'editTitle']) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('company',__('messages.candidate_profile.company').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('company', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required', 'id' => 'editCompany']) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'editCvCountry','class' => 'form-select','placeholder' => __('messages.company.select_country'), 'data-modal-type' => 'experience', 'data-is-edit' => 'true']) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('state_id', [], null, ['id'=>'editCvState','class' => 'form-select','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'experience', 'data-is-edit' => 'true']) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('city_id', [], null, ['id'=>'editCvCity','class' => 'form-select','placeholder' => __('messages.company.select_city'), 'data-is-edit' => 'true']) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('start_date', __('messages.candidate_profile.start_date').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        <input type="text" name="start_date" id="editStartDate"
               class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white' }}" autocomplete="off"
               placeholder="{{ __('messages.candidate.available_at') }}">
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('end_date', __('messages.candidate_profile.end_date').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required hidden" id="editRequiredText"></span>
        <input type="text" name="available_at" id="editEndDate"
               class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white' }}" autocomplete="off"
               placeholder="{{ __('messages.candidate_profile.end_date') }}">
    </div>
    <div class="flex-1 sm-6 mb-0">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.candidate_profile.currently_working') }}</label>
        <label class="flex items-center form-switch form-switch-sm">
            <input type="checkbox" name="currently_working" class="flex items-center input"
                   value="1" id="editWorking">
        </label>
    </div>
    <div class="flex-1 sm-12 mb-5">
        {{ Form::label('description', __('messages.candidate_profile.description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::textarea('description', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','rows'=>'5','id' => 'editDescription']) }}
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','id'=>'btnEditExperienceSave','data-loading-text'=>"<span class="spinner-border spinner-border-sm"></span> Processing..."]) }}
    <button type="button" id="btnEditExperienceCancel"
            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary me-2">{{ __('messages.common.cancel') }}</button>
</div>
@formClose()
