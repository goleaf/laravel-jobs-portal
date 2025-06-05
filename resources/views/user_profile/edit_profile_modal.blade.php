<div id="editAdminProfileModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 fixed inset-0 z-50 overflow-y-auto -lg">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border border-gray-300 -gray-300 -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.user.edit_profile') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id'=>'editAdminProfileForm','files'=>true])
            <div class="px-6 py-4">
                {{-- <div class="px-4 py-3 rounded-md border border border border-gray-300 -gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger  hide hidden" id="profileErrorMsg"></div> --}}
                {{ Form::hidden('user_id',null,['id'=>'editUserId']) }}
                {{ csrf_field() }}
                <div class="flex flex-wrap">
                <div class="mb-4 flex-1 sm-6 mb-5">
                    {{ Form::label('first_name', __('messages.candidate.first_name').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1 required']) }}
                    {{ Form::text('first_name', null, ['id'=>'firstName','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid','text-red-500', 'placeholder' => __('messages.candidate.first_name')]) }}
                </div>
                <div class="mb-4 flex-1 sm-6 mb-5">
                    {{ Form::label('last_name',__('messages.candidate.last_name').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    {{ Form::text('last_name', null, ['id'=>'lastName','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid', 'placeholder' => __('messages.candidate.last_name')]) }}
                </div>
            </div>
            <div class="flex flex-wrap">
                <div class="mb-4 flex-1 sm-6 mb-5">
                    {{ Form::label('email', __('messages.candidate.email').(':'),['class' => 'required block text-sm font-medium text-gray-700 mb-1']) }}
                    {{ Form::text('email', null, ['id'=>'userEmail','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid','text-red-500', 'placeholder' => __('messages.candidate.email')]) }}
                </div>
                <div class="mb-4 flex-1 sm-6 mb-5 mobile-itel-width">
                    {{ Form::label('phone',__('messages.candidate.phone').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
                    <div class="mb-4 flex-1 sm-12 mb-5">
                        {{ Form::tel('phone', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','id'=>'phoneNumber']) }}
                    </div>
                    {{ Form::hidden('region_code',null,['id'=>'profilePrefixCode']) }}
                    <p id="valid-msg" class="text-green-600 hidden fw-400 fs-small mt-2">{{ __('messages.phone.valid_number') }}</p>
                    <p id="error-msg" class="text-red-600 hidden fw-400 fs-small mt-2">{{ __('messages.phone.invalid_number') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap">
                <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5" io-image-input="true">
                    <div class="block mb-2">
                        {{ __('messages.candidate.profile').':' }}
                        <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="fas fa-question-circle ml-1 general-question-mark"></i>
</span>
                    </div>
                    <div class="block">
                        <div class="image-picker">
                            <div class="image previewImage" id="profilePicturePreview"
                                 style="background-image: url({{ asset('assets/img/infyom-logo.png') }})">
                            </div>
                            <span class="picker-edit rounded -circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                                  data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_profile') }}">
                                <label>
                                    <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                    {{ Form::file('image',['id'=>'profilePicture','class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                                </label>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="px-6 py-4 border-t border border border-gray-300 -gray-300 -gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0','id' => 'btnEditSave','data-loading-text' =>"<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
        @formClose()
    </div>
</div>
</div>
{{-- new changeLanguageModal --}}
<div id="changeAdminLanguageModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border border-gray-300 -gray-300 -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.user_language.change_language') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id'=>'changeAdminLanguageForm'])
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border border border-gray-300 -gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger  hide hidden" id="editProfileValidationErrorsBox"></div>
                {{ csrf_field() }}
                <div class="">
                    {{ Form::label('language',__('messages.user_language.language').(':'), ['class' => 'required block text-sm font-medium text-gray-700 mb-1']) }}
                    {{ Form::select('language', getUserLanguages(), getLoggedInUser()->language, ['id'=>'adminLanguage','class' => 'form-select form-select-solid','text-red-500', 'data-control'=>'select2']) }}
                    </div>
                </div>
                <div class="px-6 py-4 border-t border border border-gray-300 -gray-300 -gray-200 flex justify-end space-x-2 pt-0">
                    {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0','id' => 'btnLanguageChange','data-loading-text' =>"<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span>".__('messages.common.process')]) }}
                    <button type="button" class="border border-gray-300 bg-transparent"
                            id="btnEditCancel"
                            data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            @formClose()
            </div>
        </div>
    </div>
</div>






