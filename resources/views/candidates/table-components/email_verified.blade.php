<div class="flex justify-center">
    @if( ! $row->user->email_verified_at )
        <label class="flex items-center form-switch form-switch-sm justify-center">
            <input type="checkbox" name="Is isActive"
                   class="flex items-center -input is-email-verified is-candidate-email-verified" data-id="{{ $row->id }}">
        </label>
    @else
        <div>
            <a title="{{ __('messages.common.resend_verification_mail') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out btn-icon text-primary-600 edit- px-4 py-2 rounded font-medium transition-colors send-email-verification" data-id="{{ $row->id }}">
                <i title="{{ __('messages.common.resend_verification_mail') }}" class="fa fa-sync"></i>
            </a>
        </div>
    @endif
</div>
