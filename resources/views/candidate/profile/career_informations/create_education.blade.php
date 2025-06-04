@formOpen(['id' => 'addCVEducationForm'])
<div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hidden" id="validationErrorsBox">
    <i class='fa-solid fa-face-frown me-4'></i>
</div>
<div class="flex flex-wrap">
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('degree_level_id', __('messages.candidate_profile.degree_level').':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::select('degree_level_id', $data['degreeLevels'], null, ['class' => 'form-select','required','id' => 'degreeLevelId','placeholder'=> __('messages.company.select_degree_level')]) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('degree_title', __('messages.candidate_profile.degree_title').':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::text('degree_title', null, ['class' => 'form-control','placeholder'=>__('messages.candidate_profile.degree_title')]) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'cvEducationCountryId','required','class' => 'form-select','placeholder' => __('messages.company.select_country'), 'data-modal-type' => 'education']) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'form-label']) }}
        {{ Form::select('state_id', [], null, ['id'=>'cvEducationStateId','class' => 'form-select','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'education']) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'form-label']) }}
        {{ Form::select('city_id', [], null, ['id'=>'cvEducationCityId','class' => 'form-select','placeholder' => __('messages.company.select_city')]) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('institute',__('messages.candidate_profile.institute').':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::text('institute', null, ['class' => 'form-control','required','placeholder'=> __('messages.candidate_profile.institute')]) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('result', __('messages.candidate_profile.result').':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::text('result', null, ['class' => 'form-control', 'required','placeholder'=> __('messages.candidate_profile.result')]) }}
    </div>
    <div class="flex-1 -sm-6 mb-5">
        {{ Form::label('year', __('messages.candidate_profile.year').':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::selectYear('year', date('Y'), 2000, null, ['class' => 'form-select','id'=>'educationYearId']) }}
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary me-3','id'=>'btnEducationSave','data-loading-text'=>"<span class="spinner-border spinner-border-sm"></span> Processing..."]) }}
    <button type="button" id="btnEducationCancel"
            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary me-2">{{ __('messages.common.cancel') }}</button>
</div>
@formClose()
