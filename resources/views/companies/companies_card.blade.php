<div class="col-xl-4 flex-1 md-6 candidate- bg-white shadow rounded-lg overflow-hidden">
    <div class="hover-effect-employee relative mb-5 border-hover-primary employee-border fix-employee-height">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex-col employee-pt-2">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $employee['company_url'] }}"
                         class="img-responsive users-avatar-img employee-img mr-2">
                </div>
                <div class="mb-auto w-full employee-data">
                    <div class="flex justify-center items-center w-full">
                        <div>
                            <a href="{{ route('company.index') }}/{{ $employee['id'] }}"
                               class="employee-listing-title text-decoration-none">{{ $employee['user']['first_name'] }}</a>
                        </div>
                    </div>
                    <div class="text-center">
                        <label class="text-decoration-none text-color-gray">{{ $employee['user']['email'] }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-0 pb-3 flex justify-content-around">
            <div>
                <label class="custom-switch pl-0">
                    <input type="checkbox" name="Is Active"
                           class="custom-switch-input isEmployerActive"
                           data-id="{{ $employee['id'] }}" {{ $employee['user']['is_active']==\App\Models\Company::ISACTIVE ? 'checked' : '' }}>
                    <span class="custom-switch-indicator"></span>
                    <span class="employee-label ml-1">{{ __('messages.common.status') }}</span>
                </label>
            </div>
            @if($employee['user']['email_verified_at'] == null)
                <div>
                    <label class="custom-switch pl-0">
                        <input type="checkbox" name="Is Active"
                               class="custom-switch-input is-email-verified"
                               data-id="{{ $employee['id'] }}">
                        <span class="custom-switch-indicator"></span>
                        <span class="employee-label ml-1">{{ __('messages.company.email_verified') }}</span>
                    </label>
                </div>
            @else
                <div>
                    <a title="{{ __('messages.common.resend_verification_mail') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 action- px-4 py-2 rounded font-medium transition-colors send-email-company-verification"
                       data-id="{{ $employee['id'] }}"
                       href="#">
                        <i class="fa fa-sync"></i>
                    </a>
                    <label class="employee-label ml-1">{{ __('messages.common.resend_verification_mail') }}</label>
                </div>
            @endif
        </div>

        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.edit') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-yellow-500 text-white hover:bg-yellow-600 action-btn edit-action-btn edit- px-4 py-2 rounded font-medium transition-colors"
               href="{{ route('company.index') }}/{{ $employee['id'] }}/edit">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-red-600 text-white hover:bg-red-700 action-btn delete-action-btn delete- px-4 py-2 rounded font-medium transition-colors"
               data-id="{{ $employee['id'] }}" href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
        <div class="employee-isFeature">
            @if($employee['user']['is_active']==\App\Models\Company::ISACTIVE)
                @if(!$employee['activeFeatured'])
                    <a type="button" data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="false">
                    <span class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-blue-500 text-white hover:bg-blue-600 action- px-4 py-2 rounded font-medium transition-colors w-full inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 text-white">
                        {{ __('messages.front_settings.make_feature') }}
                    </span>
                    </a>
                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 w-auto">
                        <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 adminMakeFeatured"
                           data-id="{{ $employee['id'] }}"
                           href="#">{{ __('messages.front_settings.make_featured') }}</a>
                    </div>
                @else
                    <div
                        title="Expries On {{ \Carbon\Carbon::parse($employee['activeFeatured']['end_time'])->translatedFormat('Y/m/d') }}"
                        data-toggle="tooltip" data-placement="top">
                        <a type="button" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false">
                        <span class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-green-600 text-white hover:bg-green-700 action- px-4 py-2 rounded font-medium transition-colors w-full inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 text-white">
                            {{ __('messages.front_settings.featured') }}
                            <i class="far fa-check-circle pl-1 pt-1"></i>
                        </span>
                        </a>
                        <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 w-auto">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 adminUnFeatured"
                               data-id="{{ $employee['id'] }}"
                               href="#">{{ __('messages.front_settings.remove_featured') }}</a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
