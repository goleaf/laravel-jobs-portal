{{--<div class="modal-body">--}}
{{--    <div class="alert p-4 rounded-md mb-4 -danger  hide hidden" id="editValidationErrorsBox"></div>--}}
    <div class="flex flex-wrap">
        <div class="flex-1 -sm-12 mb-5">
            {{ Form::label('home_title', __('messages.cms_service.home_title').(':'), ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('home_title', $cmsServices['home_title'], ['class' => 'form-control','required', 'placeholder' => __('messages.cms_service.home_title')]) }}
        </div>
        <div class="flex-1 -sm-12 mb-5">
            {{ Form::label('home_description', __('messages.cms_service.home_description').(':'),['class' => 'form-label']) }}
            {{ Form::textarea('home_description',$cmsServices['home_description'], ['class' => 'form-control','required', 'placeholder' => __('messages.cms_service.home_description')]) }}
        </div>
    </div>
    <div class="flex-1 -sm-12 mb-5" io-image-input="true">
        <label for="home_banner" class="block text-sm font-medium text-gray-700 mb-1">
            {{__('messages.cms_service.home_banner').':'}}
            <span class="required"></span>
           <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{  __('messages.setting.image_validation') }}">
        <i class="fas fa-question-circle ml-1  general-question-mark"></i>
</span>
        </label>
        <div class="block">
            <div class="image-picker">
                <div class="image previewImage" id="homeBannerPreview"
                     style="background-image: url({{ ($cmsServices['home_banner']) ? asset($cmsServices['home_banner']) :asset('assets/img/infyom-logo.png') }})">
                </div>
                <span class="picker-edit rounded-circle text-gray-500 fs-small"
                      data-bs-toggle="tooltip"
                      data-placement="top" data-bs-original-title="{{__('messages.tooltip.change_home_banner')}}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('home_banner',['id'=>'home_banner','class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap mt-5">
        <!-- Submit Field -->
        <div class="flex justify-content-end">
            {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-3']) }}
            {{--            <a class="btn bg-gray-100 text-gray-800 hover:bg-gray-200 px-4 py-2 rounded font-medium transition-colors -active-light-primary me-2">{{__('messages.common.cancel')}}</a>--}}
        </div>
    </div>
{{--</div>--}}




