<div class="flex-1 -xl-4 flex-1 md-6 candidate- bg-white shadow rounded -lg overflow-hidden">
    <div class="hover-effect-employee relative mb-5 border-hover-primary employee- border border-gray-300">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex- flex-1">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $headerSlider->header_slider_url }}"
                         class="img-responsive users-avatar-img employee-img mr-2 image-stretching">
                </div>
            </div>
        </div>
        <div class="pt-0">
            <div class="text-center">
                <label class="custom-switch pl-0">
                    <input type="checkbox" name="Is Active"
                           class="custom-switch-input isActive"
                           data-id="{{ $headerSlider->id }}" {{ $headerSlider->is_active == 0 ? '' : 'checked' }}>
                    <span class="custom-switch-indicator"></span>
                    <span class="employee-label ml-1">{{ __('messages.common.status') }}</span>
                </label>
            </div>
        </div>

        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.edit') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $headerSlider->id }}" href="javascript:void(0)">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $headerSlider->id }}" href="javascript:void(0)">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
