<div id="addExperienceModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center min-h-screen px-4 -lg">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.candidate_profile.add_experience') }}</h3>
                <button type="button" class="transition duration-150 ease-in-out flex-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'addNewExperienceForm'])
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hide hidden" id="validationErrorsBox">
                    <i class='flex-wrap fa-solid fa-face-fflex -mx-4n me-4'></i>
                </div>
                <div class="flex-wrap flex">
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('experience_title',__('messages.candidate_profile.experience_title').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('experience_title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder'=>__('messages.candidate_profile.experience_title')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('company',__('messages.candidate_profile.company').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('company', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder'=>__('messages.candidate_profile.company')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('country', __('messages.company.country').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::select('country_id',$data['countries'], null, ['id'=>'countryId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_country'),'data-modal-type' => 'experience','text-red-500']) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('state', __('messages.company.state').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('state_id', [], null, ['id'=>'stateId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'experience']) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('city', __('messages.company.city').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::select('city_id', [], null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'cityId','placeholder' => __('messages.company.select_city')]) }}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('start_date', __('messages.candidate_profile.start_date').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        <input type="text" name="start_date" id="startDateExperience"
                               class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}"
                               autocomplete="off" placeholder="{{ __('messages.candidate_profile.start_date') }}">

                    </div>
                    <div class="mb-5 flex-1 sm-6 end-date-ele">
                        {{ Form::label('end_date', __('messages.candidate_profile.end_date').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        <input type="text" name="end_date" id="endDateExperience"
                               class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-gray-100' : 'bg-white' }}"
                               autocomplete="off" placeholder="{{ __('messages.candidate_profile.end_date') }}">
                        {{-- {{ Form::text('end_date',  null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'data-modal-type' => 'experience','id' => 'endDateExperience','placeholder'=>'End Date','text-red-500']) }} --}}
                    </div>
                    <div class="mb-5 flex-1 sm-6">
                        {{ Form::label('currently_working', __('messages.candidate_profile.currently_working').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <div class="flex items-center form-switch">
                            <input class="flex items-center input" name="currently_working" type="checkbox"
                                   value="1" id="default">
                        </div>
                    </div>
                    <div class="flex-1 sm-12">
                        {{ Form::label('description', __('messages.candidate_profile.description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        {{ Form::textarea('description',null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','flex flex-wrap -mx-4s'=>'5','placeholder'=>__('messages.candidate_profile.description')]) }}
                    </div>
                </div>

            </div>
            <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200ExperienceSave',
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
