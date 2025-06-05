@formOpen(['id' => 'editCVExperienceForm'])
<div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hidden" id="editValidationErrorsBox">
    <i class='flex-wrap fa-solid fa-face-fflex -mx-4n me-4'></i>
</div>
<input type="hidden" id="experienceId">
<div class="flex-wrap flex">
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('experience_title',__('messages.candidate_profile.experience_title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('experience_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'id' => 'editTitle']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('company',__('messages.candidate_profile.company').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('company', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'id' => 'editCompany']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'editCvCountry','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_country'), 'data-modal-type' => 'experience', 'data-is-edit' => 'true']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('state_id', [], null, ['id'=>'editCvState','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'experience', 'data-is-edit' => 'true']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('city_id', [], null, ['id'=>'editCvCity','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_city'), 'data-is-edit' => 'true']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('start_date', __('messages.candidate_profile.start_date').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        <input type="text" name="start_date" id="editStartDate"
               class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}" autocomplete="off"
               placeholder="{{ __('messages.candidate.available_at') }}">
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('end_date', __('messages.candidate_profile.end_date').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required hidden" id="editRequiredText"></span>
        <input type="text" name="available_at" id="editEndDate"
               class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}" autocomplete="off"
               placeholder="{{ __('messages.candidate_profile.end_date') }}">
    </div>
    <div class="mb-0 flex-1 sm-6">
        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.candidate_profile.currently_working') }}</label>
        <label class="flex items-center form-switch form-switch-sm">
            <input type="checkbox" name="currently_working" class="flex items-center input"
                   value="1" id="editWorking">
        </label>
    </div>
    <div class="mb-5 flex-1 sm-12">
        {{ Form::label('description', __('messages.candidate_profile.description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::textarea('description', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','flex flex-wrap -mx-4s'=>'5','id' => 'editDescription']) }}
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200EditExperienceSave','data-loading-text'=>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span> Processing..."]) }}
    <button type="button" id="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200EditExperienceCancel"
            class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</button>
</div>
@formClose()
