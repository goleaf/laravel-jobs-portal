<div id="changeAdminPasswordModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{  __('messages.user.change_password') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id' => 'changeAdminPasswordForm'])
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
                <div class="mb-5">
                    {{ Form::label('pfCurrentPassword', __('messages.company.current_password').':', ['class' => 'form-label required']) }}
                    {{ Form::password('password_current', [
                        'class' => 'form-control form-control-solid',
                        'id' => 'pfCurrentPassword',
                        'required' => true
                    ]) }}
                </div>
                <div class="mb-5">
                    {{ Form::label('pfNewPassword', __('messages.company.new_password').':', ['class' => 'form-label required']) }}
                    {{ Form::password('password', [
                        'class' => 'form-control form-control-solid',
                        'id' => 'pfNewPassword',
                        'required' => true
                    ]) }}
                </div>
                <div class="mb-5">
                    {{ Form::label('pfNewConfirmPassword', __('messages.company.confirm_password').':', ['class' => 'form-label required']) }}
                    {{ Form::password('password_confirmation', [
                        'class' => 'form-control form-control-solid',
                        'id' => 'pfNewConfirmPassword',
                        'required' => true
                    ]) }}
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary me-3',
                    'id' => 'btnPrPasswordEditSave',
                    'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> " . __('messages.common.processing')
                ]) }}
                {{ Form::button(__('messages.common.discard'), [
                    'type' => 'button',
                    'class' => 'btn btn-secondary btn-active-light-primary me-2',
                    'data-bs-dismiss' => 'modal'
                ]) }}
            </div>
            @formClose()
        </div>
    </div>
</div>
