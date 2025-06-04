<div class="fixed inset-0 z-50 overflow-y-auto fade" id="editProfileModal"  tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 modal-lg">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.user.edit_profile')  }}</h3>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'editCandidateProfileForm', 'files' => true])
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hidden" id="validationErrorsBoxCandidate"></div>
                {{ Form::hidden('user_id',null,['id'=>'editUserId'])  }}
                {{ csrf_field() }}
                <div class="flex flex-wrap">
                    <div class="form-group flex-1 -sm-6 mb-5">
                        {{ Form::label('first_name', __('messages.candidate.first_name').(':'), ['class' => 'form-label required'])  }}
                        {{ Form::text('first_name', null, ['id'=>'firstName','class' => 'form-control','required', 'placeholder' => __('messages.candidate.first_name')])  }}
                    </div>
                    <div class="form-group flex-1 -sm-6 mb-5">
                        {{ Form::label('last_name',__('messages.candidate.last_name').(':'),['class' => 'form-label required'])  }}
                        {{ Form::text('last_name', null, ['id'=>'lastName','class' => 'form-control', 'placeholder' => __('messages.candidate.last_name')])  }}
                    </div>
                </div>
                <div class="flex flex-wrap">
                    <div class="form-group flex-1 -sm-6 mb-5">
                        {{ Form::label('email', __('messages.candidate.email').(':'),['class' => 'required form-label'])  }}
                        {{ Form::text('email', null, ['id'=>'editEmail','class' => 'form-control','required', 'placeholder' => __('messages.candidate.email')])  }}
                    </div>
                    <div class="form-group flex-1 -sm-6 mb-5">
                    <div io-image-input="true">
                    {{ Form::label('', __('messages.candidate.profile').':', ['class' => 'form-label'])  }}
                    <span data-bs-toggle="tooltip"
                          data-placement="top"
                          data-bs-original-title="{{ __('messages.setting.image_validation')   }}">
                                <i class="fas fa-question-circle ml-1general-question-mark"></i>
                        </span>
                    <div class="block">
                        <div class="image-picker">
                            <div class="image previewImage" id="profilePicturePreview"></div>
                            <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                                  data-placement="top" data-bs-original-title="{{ __('messages.tooltip.upload_profile')  }}">
                        <label>
                            <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                            <input type="file" name="image" id="profilePicture" class="image-upload hidden" accept="image/*"/>
                        </label>
                    </span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ __('messages.settings.allowed_file_types')  }}</div>
                </div>
                </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary m-0','id'=>'btnPrEditSave','data-loading-text'=>"<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')])  }}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel')  }}</button>
            </div>
            @formClose()
        </div>
    </div>
</div>



<div class="fixed inset-0 z-50 overflow-y-auto fade" id="changeLanguageModal"  tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.user_language.change_language')  }}</h3>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            @formOpen(['id'=>'changeCandidateLanguageForm'])
                <div class="px-6 py-4">
                    <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger  hide hidden" id="editProfileValidationErrorsBox"></div>
                    {{ csrf_field() }}
                    <div class="">
                        {{ Form::label('language',__('messages.user_language.language').(':'), ['class' => 'required form-label'])  }}
                        {{ Form::select('language', getUserLanguages(), getLoggedInUser()->language, ['id'=>'language','class' => 'form-select','required'])  }}
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                    {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0', 'id'=>'btnLanguageChange'])  }}
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                            data-bs-dismiss="modal">{{ __('messages.common.cancel')  }}
                    </button>
                </div>
            @formClose()
        </div>
    </div>
</div>

