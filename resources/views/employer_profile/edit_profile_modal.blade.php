<div id="editEmployerProfileModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 fixed inset-0 z-50 overflow-y-auto -lg">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border border-gray-300 -gray-300 -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.user.edit_profile') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id' => 'editEmployerProfileForm', 'files' => true])
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border border border-gray-300 -gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger hidden" id="validationErrorsBoxCandidate">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ Form::hidden('user_id', null, ['id' => 'editUserId']) }}
                {{ Form::hidden('company_id', null, ['id' => 'companyId']) }}
                {{ csrf_field() }}
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('first_name', __('messages.candidate.first_name').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('first_name', null, ['id' => 'firstName', 'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'text-red-500', 'placeholder' => __('messages.candidate.first_name')]) }}
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('email', __('messages.candidate.email').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::text('email', null, ['id' => 'editEmail', 'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'text-red-500', 'placeholder' => __('messages.candidate.email')]) }}
                    </div>

                    <div class="flex-1 sm-6 mb-5" io-image-input="true">
                        {{ Form::label('profilePicture', __('messages.candidate.profile').':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
                            <i class="fas fa-question-circle ml-1 general-question-mark"></i>
                        </span>
                        <div class="block">
                            <div class="image-picker">
                                <div class="image previewImage" id="profilePicturePreview"
                                     style="background-image: url({{ asset('assets/img/infyom-logo.png') }})">
                                </div>
                                <span class="picker-edit rounded -circle text-gray-500 fs-small"
                                      data-bs-toggle="tooltip"
                                      data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_profile') }}">
                                    <label>
                                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                        {{ Form::file('image', ['id' => 'profilePicture', 'class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border border border-gray-300 -gray-300 -gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0', 'id' => 'btnEditSave', 'data-loading-text' =>"<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            @formClose()
        </div>
    </div>
</div>

<div id="changeEmployerLanguageModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border border-gray-300 -gray-300 -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.user_language.change_language') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id' => 'changeEmployerLanguageForm'])
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border border border-gray-300 -gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger hide hidden" id="editProfileValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ csrf_field() }}
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-12">
                        {{ Form::label('language', __('messages.user_language.language').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::select('language', getUserLanguages(), getLoggedInUser()->language, ['id' => 'employerLanguage', 'class' => 'form-select', 'text-red-500']) }}
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border border border-gray-300 -gray-300 -gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0', 'id' => 'btnLanguageChange', 'data-loading-text' =>"<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent"
                        id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            @formClose()
        </div>
    </div>
</div>
