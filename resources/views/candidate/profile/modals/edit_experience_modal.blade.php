<div id="editExperienceModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 modal-lg">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3>{{ __('messages.candidate_profile.edit_experience') }}</h3>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'editExperienceForm'])
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger  hide hidden" id="editValidationErrorsBox">
                    <i class='fa-solid fa-face-frown me-4'></i>
                </div>
                {{ Form::hidden('experienceId',null,['id'=>'experienceId']) }}
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('experience_title',__('messages.candidate_profile.experience_title').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('experience_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required','id' => 'editTitle','placeholder'=>__('messages.candidate_profile.experience_title')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('company',__('messages.candidate_profile.company').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('company', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required', 'id' => 'editCompany','placeholder'=>__('messages.candidate_profile.company')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('country', __('messages.company.country').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::select('country_id',$data['countries'], null, ['id'=>'editCountry','class' => 'form-select countryId','placeholder' => __('messages.company.select_country'),'data-modal-type' => 'experience','data-is-edit' => 'true']) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('state', __('messages.company.state').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('state_id', [], null, ['id'=>'editState','class' => 'form-select stateId','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'experience','data-is-edit' => 'true']) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('city', __('messages.company.city').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('city_id', [],null, ['class' => 'form-select cityId','data-modal-type' => 'experience','id'=>'editCity','placeholder' => __('messages.company.select_city'),'data-is-edit' => 'true']) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('start_date', __('messages.candidate_profile.start_date').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        <input type="text" name="start_date" id="editStartDate" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white' }}" autocomplete="off" placeholder="{{ __('messages.candidate_profile.start_date') }}">
{{ --                        {{ Form::text('start_date', null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'editStartDate','autocomplete' => 'off','placeholder'=>'Start Date']) }}--}}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('end_date', __('messages.candidate_profile.end_date').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        <input type="text" name="end_date" id="editEndDate" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white' }}" autocomplete="off" placeholder="{{ __('messages.candidate_profile.end_date') }}">
{{ --                        {{ Form::text('end_date',  null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'data-modal-type' => 'experience','id' => 'editEndDate','autocomplete' => 'off','placeholder'=>'End Date']) }}--}}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('currently_working', __('messages.candidate_profile.currently_working').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <div class="flex items-center form-switch">
                            <input class="flex items-center input" name="currently_working" type="checkbox"
                                   value="1" id="editWorking">
                        </div>
                    </div>
                    <div class="flex-1 sm-12">
                        {{ Form::label('description', __('messages.candidate_profile.description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::textarea('description',null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','rows'=>'5','id' => 'editDescription','placeholder'=>__('messages.candidate_profile.description')]) }}
                    </div>
                    {{ --                    <div class="text-right">-- }}
                    {{ --                        {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','id' => 'btnExperienceSave','data-loading-text' =>"<span class="spinner-border spinner-border-sm"></span> Processing..."]) }}--}}
                    {{ --                        <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-gray-100 text-gray-800 hover:bg-gray-200 px-4 py-2 rounded font-medium transition-colors active-light-primary me-2"-- }}
                    {{ --                                id="btnCancel"-- }}
                    {{ --                                data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>--}}
                    {{ --                    </div>-- }}
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3',
                    'id' => 'btnEditExperienceSave',
                    'data-loading-text' =>"<span class="spinner-border spinner-border-sm"></span>".__('messages.common.process')
                ]) }}
                {{ Form::button(__('messages.common.cancel'), [
                    'type' => 'button',
                    'class' => 'rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors',
                    'data-bs-dismiss' => 'modal'
                ]) }}
            </div>
            @formClose()
        </div>
    </div>
</div>
