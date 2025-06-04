@formOpen(['id' => 'addCVExperienceForm'])
<div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hidden" id="validationErrorsBox">
    <i class='fa-solid fa-face-frown me-4'></i>
</div>
<div class="flex flex-wrap">
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('experience_title',__('messages.candidate_profile.experience_title').':', ['class' => 'form-label ']) }}
        <span class="required"></span>
        {{ Form::text('experience_title', null, ['class' => 'form-control','required','placeholder'=>__('messages.candidate_profile.experience_title')]) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('company',__('messages.candidate_profile.company').':', ['class' => 'form-label ']) }}
        <span class="required"></span>
        {{ Form::text('company', null, ['class' => 'form-control','required','placeholder'=>__('messages.candidate_profile.company')]) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'form-label ']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'countryId','required','class' => 'form-select','placeholder' => __('messages.company.select_country'), 'data-modal-type' => 'experience']) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'form-label']) }}
        {{ Form::select('state_id', [], null, ['id'=>'stateId','class' => 'form-select','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'experience']) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'form-label']) }}
        {{ Form::select('city_id', [], null, ['id'=>'cityId','class' => 'form-select ','placeholder' => __('messages.company.select_city')]) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('start_date', __('messages.candidate_profile.start_date').':', ['class' => 'form-label ']) }}
        <span class="required"></span>
        <input type="text" name="start_date" id="startDate" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{(getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white'}}" autocomplete="off" placeholder="{{__('messages.candidate_profile.start_date')}}">
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('end_date', __('messages.candidate_profile.end_date').':', ['class' => 'form-label ']) }}
        <span class="required" id="requiredText"></span>

        <input type="text" name="end_date" id="endDate" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{(getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white'}}" autocomplete="off" placeholder="{{__('messages.candidate_profile.end_date')}}">
  
    </div>
    <div class="flex-1 -sm-6 mb-0 pt-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.candidate_profile.currently_working') }}</label>
        <div class="flex-1 -6 pl-0">
            <label class="flex items-center form-switch form-switch-sm">
                <input type="checkbox" name="currently_working" class="flex items-center -input"
                       value="1" id="default">
            </label>
        </div>
    </div>
    <div class="flex-1 -sm-12 mb-5">
        {{ Form::label('description', __('messages.candidate_profile.description').':', ['class' => 'form-label ']) }}
        {{ Form::textarea('description', null, ['class' => 'form-control','rows'=>'5','placeholder'=>__('messages.candidate_profile.description')]) }}
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary me-3','id'=>'btnExperienceSave','data-loading-text'=>"<span class="spinner-border spinner-border-sm"></span> Processing..."]) }}
    <button type="button" id="btnExperienceCancel"
            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary me-2">{{ __('messages.common.cancel') }}</button>
</div>
@formClose()
