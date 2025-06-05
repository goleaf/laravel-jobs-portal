<div id="addEducationModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center min-h-screen px-4 -lg">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.candidate_profile.add_education') }}</h3>
                <button type="button" class="transition duration-150 ease-in-out flex-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'addNewEducationForm'])
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hide hidden" id="validationErrorsBox">
                    <i class='flex-wrap fa-solid fa-face-fflex -mx-4n me-4'></i>
                </div>
                <div class="flex-wrap flex">
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('degree_level_id', __('messages.candidate_profile.degree_level').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
                        <span class="required"></span>
                        {{ Form::select('degree_level_id', $data['degreeLevels'], null ,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','id' => 'degreeLevelId','placeholder'=>__('messages.company.select_degree_level')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('degree_title', __('messages.candidate_profile.degree_title').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('degree_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder'=>__('messages.candidate_profile.degree_title')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('country', __('messages.company.country').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::select('country_id',$data['countries'], null, ['id'=>'educationCountryId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-modal-type' => 'education','placeholder' => __('messages.company.select_country')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('state', __('messages.company.state').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('state_id', [], null, ['id'=>'educationStateId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm stateId','placeholder' => __('messages.company.select_state')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('city', __('messages.company.city').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('city_id', [], null, ['id'=>'educationCityId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm cityId','placeholder' => __('messages.company.select_city')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('institute', __('messages.candidate_profile.institute').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('institute', null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder'=>__('messages.candidate_profile.institute')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('result',__('messages.candidate_profile.result').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('result',  null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder'=>__('messages.candidate_profile.result')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('year', __('messages.candidate_profile.year').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1  required']) }}
                        {{ Form::selectRange('year', date('Y'), 2000, null, ['id'=>'educationYearId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','data-modal-type' => 'education','placeholder' => __('messages.candidate_profile.select_year')]) }}
                    </div>
                </div>
            </div>
            <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200EducationSave',
                    'data-loading-text' =>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span>".__('messages.common.process')
                ]) }}
                {{ Form::button(__('messages.common.cancel'), [
                    'type' => 'button',
                    'class' => 'rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-flex-1 px-4ors',
                    'data-bs-dismiss' => 'modal'
                ]) }}
            </div>
            @formClose()
        </div>
    </div>
</div>
