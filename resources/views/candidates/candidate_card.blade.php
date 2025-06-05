<div class="overflow-hidden shadow rounded bg-white flex-1 px-4 -xl-4 flex-1 md-6 candidate- -lg">
    <div class="border mb-5 border hover-effect-employee relative -hover-primary employee- fix-candidate-height">
        <div class="employee-listing-details">
            <div class="flex-1 px-4 flex employee-listing-description items-center justify-center flex-">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $candidate['candidate_url'] }}"
                         class="mr-2 img-responsive users-avatar-img employee-img">
                </div>
                <div class="mb-auto w-full">
                    <div class="flex justify-center items-center w-full">
                        <div>
                            <a href="{{ route('admin.', $candidate['id']) }}"
                               class="employee-listing-title text-decoration-none">{{ $candidate['user']['first_name'] }}</a>
                        </div>
                    </div>
                    <div class="text-center">
                        <label class="text-decoration-none text-flex-1 px-4or-gray">{{ $candidate['user']['email'] }}</label>
                    </div>
                    <div class="text-center">
                        <span class="rounded rounded inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium uppercase inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium text-black available-">
                            {{ $candidate['immediate_available'] == 0 ? __('messages.not_immediate_available') : __('messages.immediate_available') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="pb-3 pt-0 justify-around flex">
            <div>
                <label class="pl-0 custom-switch">
                    <input type="checkbox" name="Is Active"
                           class="custom-switch-input isCandidateActive"
                           data-id="{{ $candidate['id'] }}" {{ $candidate['user']['is_active']==0?'':'checked' }}>
                    <span class="custom-switch-indicator"></span>
                    <span class="ml-1 employee-label">{{ __('messages.common.status') }}</span>
                </label>
            </div>
            @if($candidate['user']['email_verified_at'] == null)
                <div>
                    <label class="pl-0 custom-switch">
                        <input type="checkbox" name="Is Active" data-id="{{ $candidate['id'] }}"
                               class="custom-switch-input is-candidate-email-verified">
                        <span class="custom-switch-indicator"></span>
                        <span class="ml-1 employee-label">{{ __('messages.candidate.email_verified') }}</span>
                    </label>
                </div>
            @else
                <div>
                    <a title="{{ __('messages.common.resend_verification_mail') }}"
                       class="border border-gray-300 bg-transparent" data-id="{{ $candidate['id'] }}"
                       href="#">
                        <i class="fa fa-sync"></i>
                    </a>
                    <span class="ml-1 employee-label">{{ __('messages.common.resend_verification_mail') }}</span>
                </div>
            @endif
        </div>

        <div class="transition duration-150 ease-in-out flex-1">
            <a title="{{ __('messages.common.edit') }}"
               class="border border-gray-300 bg-transparent"
               href="{{ route('admin.', $candidate['id']) }}">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}"
               class="border border-gray-300 bg-transparent" data-id="{{ $candidate['id'] }}"
               href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
