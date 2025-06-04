<div id="addExperienceModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 modal-lg">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.candidate_profile.add_experience') }}</h3>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'addNewExperienceForm'])
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hide hidden" id="validationErrorsBox">
                    <i class='fa-solid fa-face-frown me-4'></i>
                </div>
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('experience_title',__('messages.candidate_profile.experience_title').(':'), ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::text('experience_title', null, ['class' => 'form-control','required', 'placeholder'=>__('messages.candidate_profile.experience_title')]) }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('company',__('messages.candidate_profile.company').(':'),['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::text('company', null, ['class' => 'form-control','required', 'placeholder'=>__('messages.candidate_profile.company')]) }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('country', __('messages.company.country').(':'),['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::select('country_id',$data['countries'], null, ['id'=>'countryId','class' => 'form-select','placeholder' => __('messages.company.select_country'),'data-modal-type' => 'experience','required']) }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('state', __('messages.company.state').(':'),['class' => 'form-label']) }}
                        {{ Form::select('state_id', [], null, ['id'=>'stateId','class' => 'form-select','placeholder' => __('messages.company.select_state'), 'data-modal-type' => 'experience']) }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('city', __('messages.company.city').(':'),['class' => 'form-label']) }}
                        {{ Form::select('city_id', [], null, ['class' => 'form-select','id'=>'cityId','placeholder' => __('messages.company.select_city')]) }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('start_date', __('messages.candidate_profile.start_date').(':'),['class' => 'form-label']) }}
                        <span class="required"></span>
                        <input type="text" name="start_date" id="startDateExperience"
                               class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{(getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white'}}"
                               autocomplete="off" placeholder="{{__('messages.candidate_profile.start_date')}}">

                    </div>
                    <div class="flex-1 -sm-6 mb-5 end-date-ele">
                        {{ Form::label('end_date', __('messages.candidate_profile.end_date').(':'),['class' => 'form-label']) }}
                        <span class="required"></span>
                        <input type="text" name="end_date" id="endDateExperience"
                               class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{(getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white'}}"
                               autocomplete="off" placeholder="{{__('messages.candidate_profile.end_date')}}">
                        {{--                        {{ Form::text('end_date',  null, ['class' => 'form-control', 'data-modal-type' => 'experience','id' => 'endDateExperience','placeholder'=>'End Date','required']) }}--}}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('currently_working', __('messages.candidate_profile.currently_working').(':'),['class' => 'form-label']) }}
                        <div class="flex items-center form-switch">
                            <input class="flex items-center -input" name="currently_working" type="checkbox"
                                   value="1" id="default">
                        </div>
                    </div>
                    <div class="flex-1 -sm-12">
                        {{ Form::label('description', __('messages.candidate_profile.description').':', ['class' => 'form-label']) }}
                        {{ Form::textarea('description',null, ['class' => 'form-control','rows'=>'5','placeholder'=>__('messages.candidate_profile.description')]) }}
                    </div>
                </div>

            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary me-3',
                    'id' => 'btnExperienceSave',
                    'data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')
                ]) }}
                {{ Form::button(__('messages.common.cancel'), [
                    'type' => 'button',
                    'class' => 'btn btn-secondary',
                    'data-bs-dismiss' => 'modal'
                ]) }}
            </div>
            @formClose()
        </div>
    </div>
</div>
