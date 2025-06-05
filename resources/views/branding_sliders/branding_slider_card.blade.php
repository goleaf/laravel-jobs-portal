<div class="overflow-hidden shadow rounded bg-white flex-1 px-4 -xl-4 flex-1 md-6 candidate- -lg">
    <div class="border mb-5 border hover-effect-employee relative -hover-primary employee-">
        <div class="employee-listing-details">
            <div class="flex-1 px-4 flex employee-listing-description items-center justify-center flex-">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $brandingSlider->branding_slider_url }}"
                         class="mr-2 img-responsive users-avatar-img employee-img image-stretching">
                </div>
                <div class="mb-auto w-full employee-data">
                    <div class="flex justify-center items-center w-full">
                        <div>
                            <span class="text-decoration-none text-color-gray">
                                {{ $brandingSlider->title }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-0">
            <div class="text-center">
                <label class="pl-0 custom-switch">
                    <input type="checkbox" name="Is Active"
                           class="custom-switch-input isActive"
                           data-id="{{ $brandingSlider->id }}" {{ $brandingSlider->is_active == 0 ? '' : 'checked' }}>
                    <span class="custom-switch-indicator"></span>
                    <span class="ml-1 employee-label">{{ __('messages.common.status') }}</span>
                </label>
            </div>
        </div>

        <div class="rounded employee-action- px-4 py-2 font-medium transition-colors">
            <a title="{{ __('messages.common.edit') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $brandingSlider->id }}" href="javascript:void(0)">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $brandingSlider->id }}" href="javascript:void(0)">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
