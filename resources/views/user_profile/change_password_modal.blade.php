<div id="changeAdminPasswordModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.user.change_password') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id' => 'changeAdminPasswordForm'])
            <div class="px-6 py-4">
                @if ($errors->any())
                    <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger hide hidden" id="editPasswordValidationErrorsBox"></div>
                {{ Form::hidden('user_id', null, ['id' => 'pfUserId']) }}
                {{ Form::hidden('is_active', 1) }}
                {{ csrf_field() }}
                <div class="mb-5">
                    {{ Form::label('pfCurrentPassword', __('messages.company.current_password').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 required']) }}
                    {{ Form::password('password_current', [
                        'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid',
                        'id' => 'pfCurrentPassword',
                        'required' => true
                    ]) }}
                </div>
                <div class="mb-5">
                    {{ Form::label('pfNewPassword', __('messages.company.new_password').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 required']) }}
                    {{ Form::password('password', [
                        'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid',
                        'id' => 'pfNewPassword',
                        'required' => true
                    ]) }}
                </div>
                <div class="mb-5">
                    {{ Form::label('pfNewConfirmPassword', __('messages.company.confirm_password').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 required']) }}
                    {{ Form::password('password_confirmation', [
                        'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid',
                        'id' => 'pfNewConfirmPassword',
                        'required' => true
                    ]) }}
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3',
                    'id' => 'btnPrPasswordEditSave',
                    'data-loading-text' =>"<span class="spinner-border spinner-border-sm"></span>" . __('messages.common.processing')
                ]) }}
                {{ Form::button(__('messages.common.discard'), [
                    'type' => 'button',
                    'class' => 'rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors btn-active-light-primary me-2',
                    'data-bs-dismiss' => 'modal'
                ]) }}
            </div>
            @formClose()
        </div>
    </div>
</div>
