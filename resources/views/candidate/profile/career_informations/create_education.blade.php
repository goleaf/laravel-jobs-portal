@formOpen(['id' => 'addCVEducationForm'])
<div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger hidden" id="validationErrorsBox">
    <i class='fa-solid fa-face-frown me-4'></i>
</div>
<div class="flex flex-wrap">
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('degree_level_id', __('messages.candidate_profile.degree_level').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('degree_level_id', $data['degreeLevels'], null, ['class' => 'form-select','required','id' => 'degreeLevelId','placeholder'=> __('messages.company.select_degree_level')]) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('degree_title', __('messages.candidate_profile.degree_title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('degree_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder'=>__('messages.candidate_profile.degree_title')]) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'cvEducationCountryId','required','class' => 'form-select','placeholder' => __('messages.company.select_country'), 'data-modal-type' => 'education']) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('state_id', [], null, ['id'=>'cvEducationStateId','class' => 'form-select','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'education']) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('city_id', [], null, ['id'=>'cvEducationCityId','class' => 'form-select','placeholder' => __('messages.company.select_city')]) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('institute',__('messages.candidate_profile.institute').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('institute', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required','placeholder'=> __('messages.candidate_profile.institute')]) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('result', __('messages.candidate_profile.result').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('result', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'required','placeholder'=> __('messages.candidate_profile.result')]) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('year', __('messages.candidate_profile.year').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::selectYear('year', date('Y'), 2000, null, ['class' => 'form-select','id'=>'educationYearId']) }}
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','id'=>'btnEducationSave','data-loading-text'=>"<span class="spinner-border spinner-border-sm"></span> Processing..."]) }}
    <button type="button" id="btnEducationCancel"
            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary me-2">{{ __('messages.common.cancel') }}</button>
</div>
@formClose()
