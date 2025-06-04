<div id="addLanguageTranslateModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3>{{ __('messages.language.new_language')  }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'addLanguageTranslateForm'])  }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger fs-4 text-white flex items-center hidden"
                     id="validationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <span class="ml-3"><b>{{ __('messages.common.note').(':') }}</b> @lang('messages.common.note_message')</span>
                <div class="mb-5">
                    {{ Form::label('name', __('messages.common.name').(':'), ['class' => 'form-label'])  }}
                    <span class="required"></span>
                    {{ Form::text('name', null, ['class' => 'form-control','required','maxlength'=>'2','onkeyup' => 'if (/^$|\s+/.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'onkeypress' => 'return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123)','placeholder' => __('messages.common.name')])  }}
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'langSaveBtn','data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')])  }}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        id="maritalStatusBtnCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel')  }}</button>
            </div>

        </div>
        {{ Form::close()  }}
        </div>
    </div>
