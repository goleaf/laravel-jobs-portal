<div id="editEducationModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 modal-lg">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2>{{ __('messages.candidate_profile.edit_education')  }}</h2>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'editCareerEducationForm'])
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hide hidden" id="editValidationErrorsBox">
                    <i class='fa-solid fa-face-frown me-4'></i>
                </div>
                {{ Form::hidden('educationId', null, ['id' => 'educationId'])  }}
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('degree_level_id', __('messages.candidate_profile.degree_level').(':'), ['class' => 'form-label'])  }}
                        <span class="required"></span>
                        {{ Form::select('degree_level_id', $data['degreeLevels'], null ,['class' => 'form-select','required','id' => 'editDegreeLevel'])  }}
                    </div>

                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('degree_title', __('messages.candidate_profile.degree_title').(':'),['class' => 'form-label'])  }}
                        <span class="required"></span>
                        {{ Form::text('degree_title', null, ['class' => 'form-control','required','id'=>'editDegreeTitle','placeholder'=>__('messages.candidate_profile.degree_title')])  }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('country', __('messages.company.country').(':'),['class' => 'form-label'])  }}
                        <span class="required"></span>
                        {{ Form::select('country_id',$data['countries'], null, ['id'=>'editEducationCountry','class' => 'form-select','data-modal-type' => 'education','placeholder' => __('messages.company.select_country'),'data-is-edit' => 'true'])  }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('state', __('messages.company.state').(':'),['class' => 'form-label'])  }}
                        {{ Form::select('state_id', [], null, ['id'=>'editEducationState','class' => 'form-select','placeholder' => __('messages.company.select_state'),'data-modal-type' => 'education','data-is-edit' => 'true'])  }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('city', __('messages.company.city').(':'),['class' => 'form-label'])  }}
                        {{ Form::select('city_id', [],  null, ['id'=>'editEducationCity','class' => 'form-select','placeholder' => __('messages.company.select_city')])  }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('institute', __('messages.candidate_profile.institute').(':'),['class' => 'form-label',])  }}
                        <span class="required"></span>
                        {{ Form::text('institute', null,['class' => 'form-control','id'=>'editInstitute','placeholder'=>__('messages.candidate_profile.institute')])  }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('result',__('messages.candidate_profile.result').(':'),['class' => 'form-label'])  }}
                        <span class="required"></span>
                        {{ Form::text('result',  null, ['class' => 'form-control','id'=>'editResult','placeholder'=>__('messages.candidate_profile.result')])  }}
                    </div>
                    <div class="flex-1 -sm-6 mb-5">
                        {{ Form::label('year', __('messages.candidate_profile.year').(':'),['class' => 'form-label'])  }}
                        <span class="required"></span>
                        {{ Form::selectRange('year', date('Y'), 2000, null, ['class' => 'form-select','placeholder' => __('messages.candidate_profile.select_year'),'id' => 'editYear']) }}
                    </div>

                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary me-3',
                    'id' => 'btnEditEducationSave',
                    'data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')
                ])  }}
                {{ Form::button(__('messages.common.cancel'), [
                    'type' => 'button',
                    'class' => 'btn btn-secondary',
                    'data-bs-dismiss' => 'modal'
                ])  }}
            </div>
            @formClose()
        </div>
    </div>
</div>
