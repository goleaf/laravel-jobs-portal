<div id="changeEmployerPasswordModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.user.change_password') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id' => 'changeEmployerPasswordForm'])
            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert p-4 rounded-md mb-4 -danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="alert p-4 rounded-md mb-4 -danger hide hidden" id="editPasswordValidationErrorsBox"></div>
                {{ Form::hidden('user_id', null, ['id' => 'pfUserId']) }}
                {{ Form::hidden('is_active', 1) }}
                {{csrf_field()}}
                <div class="flex flex-wrap">
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('pfCurrentPassword', __('messages.company.current_password').':', ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::password('password_current', [
                            'class' => 'form-control',
                            'id' => 'pfCurrentPassword',
                            'required'
                        ]) }}
                    </div>
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('pfNewPassword', __('messages.company.new_password').':', ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::password('password', [
                            'class' => 'form-control',
                            'id' => 'pfNewPassword',
                            'required'
                        ]) }}
                    </div>
                    <div class="form-group flex-1 -sm-12">
                        {{ Form::label('pfNewConfirmPassword', __('messages.company.confirm_password').':', ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::password('password_confirmation', [
                            'class' => 'form-control',
                            'id' => 'pfNewConfirmPassword',
                            'required'
                        ]) }}
                    </div>
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary m-0',
                    'id' => 'btnPrPasswordEditSave',
                    'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')
                ]) }}
                <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            @formClose()
        </div>
    </div>
</div>
