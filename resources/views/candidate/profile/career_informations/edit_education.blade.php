@formOpen(['id' => 'editCVEducationForm'])
<div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hidden" id="editValidationErrorsBox">
    <i class='flex-wrap fa-solid fa-face-fflex -mx-4n me-4'></i>
</div>
<input type="hidden" id="educationId">
<div class="flex-wrap flex">
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('degree_level_id', __('messages.candidate_profile.degree_level').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::select('degree_level_id', $data['degreeLevels'], null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','placeholder'=> __('messages.company.select_degree_level'),'id' => 'editDegreeLevel']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('degree_title', __('messages.candidate_profile.degree_title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('degree_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'editDegreeTitle']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'editCvEducationCountry','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_country'), 'data-modal-type' => 'education', 'data-is-edit' => 'true']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('state_id', [], null, ['id'=>'editCvEducationState','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'education', 'data-is-edit' => 'true']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('city_id', [], null, ['id'=>'editCvEducationCity','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_city'), 'data-is-edit' => 'true']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('institute',__('messages.candidate_profile.institute').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('institute', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','text-red-500', 'id' => 'editInstitute']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('result', __('messages.candidate_profile.result').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('result', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ', 'text-red-500', 'id' => 'editResult']) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('year', __('messages.candidate_profile.year').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::selectYear('year', date('Y'), 2000, null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'id' => 'editYear']) }}
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200EditEducationSave','data-loading-text'=>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span> Processing..."]) }}
    <button type="button" id="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200EditEducationCancel"
            class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</button>
</div>
@formClose()
