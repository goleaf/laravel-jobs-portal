<div class="col-xl-4 flex-1 -md-6 candidate- bg-white shadow rounded-lg overflow-hidden">
    <div class="hover-effect-employee position-relative mb-5 border-hover-primary employee-border">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex-column">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $imageSlider->image_slider_url }}"
                         class="img-responsive users-avatar-img employee-img mr-2 image-stretching">
                </div>
            </div>
        </div>
        <div class="pt-0">
            <div class="text-center">
                <label class="custom-switch pl-0">
                    <input type="checkbox" name="Is Active"
                           class="custom-switch-input isActive"
                           data-id="{{ $imageSlider->id }}" {{ $imageSlider->is_active == 0 ? '' : 'checked' }}>
                    <span class="custom-switch-indicator"></span>
                    <span class="employee-label ml-1">{{ __('messages.common.status') }}</span>
                </label>
            </div>
        </div>

        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.view') }}" class="btn bg-blue-500 text-white hover:bg-blue-600 action-btn show- px-4 py-2 rounded font-medium transition-colors"
               data-id="{{$imageSlider->id}}" href="javascript:void(0)">
                <i class="fa fa-eye"></i>
            </a>
            <a title="{{ __('messages.common.edit') }}" class="btn bg-yellow-500 text-white hover:bg-yellow-600 action-btn edit- px-4 py-2 rounded font-medium transition-colors"
               data-id="{{$imageSlider->id}}" href="javascript:void(0)">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="btn bg-red-600 text-white hover:bg-red-700 action-btn delete- px-4 py-2 rounded font-medium transition-colors"
               data-id="{{$imageSlider->id}}" href="javascript:void(0)">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
