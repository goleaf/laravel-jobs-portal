<div id="changePasswordModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{  __('messages.user.change_password') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id' => 'changeCandidatePasswordForm'])
            <div class="modal-body">
                @if($errors->any())
                    <div class="alert p-4 rounded-md mb-4 -danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="alert p-4 rounded-md mb-4 -danger hidden" id="editPasswordValidationErrorsBox"></div>
                {{ Form::hidden('user_id',null,['id'=>'pfUserId']) }}
                {{ Form::hidden('is_active',1) }}
                {{csrf_field()}}
                <div class="mb-5">
                    {{ Form::label('current password', __('messages.company.current_password').(':'), ['class' => 'form-label required']) }}
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" id="pfCurrentPassword" type="password" name="password_current" required>
                </div>
                <div class="mb-5">
                    {{ Form::label('password', __('messages.company.new_password').(':'),['class' => 'required form-label']) }}
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" id="pfNewPassword" type="password" name="password" required>
                </div>
                <div class="">
                    {{ Form::label('password_confirmation', __('messages.company.confirm_password').(':'),['class' => 'required form-label']) }}
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" id="pfNewConfirmPassword" type="password"
                           name="password_confirmation" required>
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'btnPrPasswordEditSave','data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')]) }}
                <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            @formClose()
        </div>
    </div>
</div>
