<div id="editExperienceModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center min-h-screen px-4 -lg">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3>{{ __('messages.candidate_profile.edit_experience') }}</h3>
                <button type="button" class="transition duration-150 ease-in-out flex-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'editExperienceForm'])
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hide hidden" id="editValidationErrorsBox">
                    <i class='flex-wrap fa-solid fa-face-fflex -mx-4n me-4'></i>
                </div>
                {{ Form::hidden('experienceId',null,['id'=>'experienceId']) }}
                <div class="flex-wrap flex">
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('experience_title',__('messages.candidate_profile.experience_title').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('experience_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','id' => 'editTitle','placeholder'=>__('messages.candidate_profile.experience_title')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('company',__('messages.candidate_profile.company').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('company', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'id' => 'editCompany','placeholder'=>__('messages.candidate_profile.company')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('country', __('messages.company.country').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::select('country_id',$data['countries'], null, ['id'=>'editCountry','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm countryId','placeholder' => __('messages.company.select_country'),'data-modal-type' => 'experience','data-is-edit' => 'true']) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('state', __('messages.company.state').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('state_id', [], null, ['id'=>'editState','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm stateId','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'experience','data-is-edit' => 'true']) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('city', __('messages.company.city').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('city_id', [],null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm cityId','data-modal-type' => 'experience','id'=>'editCity','placeholder' => __('messages.company.select_city'),'data-is-edit' => 'true']) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('start_date', __('messages.candidate_profile.start_date').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        <input type="text" name="start_date" id="editStartDate" class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}" autocomplete="off" placeholder="{{ __('messages.candidate_profile.start_date') }}">
{{ --  Form::text('start_date', null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'editStartDate','autocomplete' => 'off','placeholder'=>'Start Date'])  -- }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('end_date', __('messages.candidate_profile.end_date').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        <input type="text" name="end_date" id="editEndDate" class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}" autocomplete="off" placeholder="{{ __('messages.candidate_profile.end_date') }}">
{{ --  Form::text('end_date',  null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'data-modal-type' => 'experience','id' => 'editEndDate','autocomplete' => 'off','placeholder'=>'End Date'])  -- }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('currently_working', __('messages.candidate_profile.currently_working').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <div class="flex items-center form-switch">
                            <input class="flex items-center input" name="currently_working" type="checkbox"
                                   value="1" id="editWorking">
                        </div>
                    </div>
                    <div class="flex-1 sm-12">
                        {{ Form::label('description', __('messages.candidate_profile.description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::textarea('description',null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','flex flex-wrap -mx-4s'=>'5','id' => 'editDescription','placeholder'=>__('messages.candidate_profile.description')]) }}
                    </div>
                    {{-- <div class="text-right"> --}}
                    {{ --  Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200ExperienceSave','data-loading-text' =>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span> Processing..."])  -- }}
                    {{-- <button type="button" class="border border-gray-300 bg-transparent" --}}
                    {{-- id="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200Cancel" --}}
                    {{ -- data-bs-dismiss="modal"> __('messages.common.cancel') </button> -- }}
                    {{-- </div> --}}
                </div>
            </div>
            <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200EditExperienceSave',
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
