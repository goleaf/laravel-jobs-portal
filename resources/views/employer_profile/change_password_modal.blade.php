<div id="changeEmployerPasswordModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.user.change_password')  }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id' => 'changeEmployerPasswordForm'])
            <div class="px-6 py-4">
                @if ($errors->any())
                    <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error  }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hide hidden" id="editPasswordValidationErrorsBox"></div>
                {{ Form::hidden('user_id', null, ['id' => 'pfUserId'])  }}
                {{ Form::hidden('is_active', 1)  }}
                {{ csrf_field() }}
                <div class="flex flex-wrap">
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('pfCurrentPassword', __('messages.company.current_password').':', ['class' => 'form-label'])  }}
                        <span class="required"></span>
                        {{ Form::password('password_current', [
                            'class' => 'form-control',
                            'id' => 'pfCurrentPassword',
                            'required'
                        ])  }}
                    </div>
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('pfNewPassword', __('messages.company.new_password').':', ['class' => 'form-label'])  }}
                        <span class="required"></span>
                        {{ Form::password('password', [
                            'class' => 'form-control',
                            'id' => 'pfNewPassword',
                            'required'
                        ])  }}
                    </div>
                    <div class="form-group flex-1 -sm-12">
                        {{ Form::label('pfNewConfirmPassword', __('messages.company.confirm_password').':', ['class' => 'form-label'])  }}
                        <span class="required"></span>
                        {{ Form::password('password_confirmation', [
                            'class' => 'form-control',
                            'id' => 'pfNewConfirmPassword',
                            'required'
                        ])  }}
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary m-0',
                    'id' => 'btnPrPasswordEditSave',
                    'data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')
                ])  }}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel')  }}</button>
            </div>
            @formClose()
        </div>
    </div>
</div>
