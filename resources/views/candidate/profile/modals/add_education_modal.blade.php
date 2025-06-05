<div id="addEducationModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 modal-lg">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.candidate_profile.add_education') }}</h3>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'addNewEducationForm'])
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger  hide hidden" id="validationErrorsBox">
                    <i class='fa-solid fa-face-frown me-4'></i>
                </div>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('degree_level_id', __('messages.candidate_profile.degree_level').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
                        <span class="required"></span>
                        {{ Form::select('degree_level_id', $data['degreeLevels'], null ,['class' => 'form-select','required','id' => 'degreeLevelId','placeholder'=>__('messages.company.select_degree_level')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('degree_title', __('messages.candidate_profile.degree_title').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('degree_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required', 'placeholder'=>__('messages.candidate_profile.degree_title')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('country', __('messages.company.country').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::select('country_id',$data['countries'], null, ['id'=>'educationCountryId','class' => 'form-select','data-modal-type' => 'education','placeholder' => __('messages.company.select_country')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('state', __('messages.company.state').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('state_id', [], null, ['id'=>'educationStateId','class' => 'form-select stateId','placeholder' => __('messages.company.select_state')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('city', __('messages.company.city').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('city_id', [], null, ['id'=>'educationCityId','class' => 'form-select cityId','placeholder' => __('messages.company.select_city')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('institute', __('messages.candidate_profile.institute').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('institute', null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder'=>__('messages.candidate_profile.institute')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('result',__('messages.candidate_profile.result').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('result',  null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder'=>__('messages.candidate_profile.result')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('year', __('messages.candidate_profile.year').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1  required']) }}
                        {{ Form::selectRange('year', date('Y'), 2000, null, ['id'=>'educationYearId','class' => 'form-select','data-modal-type' => 'education','placeholder' => __('messages.candidate_profile.select_year')]) }}
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3',
                    'id' => 'btnEducationSave',
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
