<div class="flex flex-wrap">
    <div class="flex-1 sm-4 mb-5">
        {{ Form::label('about_title_one', __('messages.cms_about.about_title_one').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('about_title_one', $cmsServices['about_title_one'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','onkeypress' => 'return avoidSpace(event);','placeholder' => __('messages.cms_about.about_title_one')]) }}
    </div>


    <div class="flex-1 sm-4 mb-5">
        {{ Form::label('about_title_two',__('messages.cms_about.about_title_two').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('about_title_two', $cmsServices['about_title_two'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','onkeypress' => 'return avoidSpace(event);','placeholder' => __('messages.cms_about.about_title_two')]) }}

    </div>
    <div class="flex-1 sm-4 mb-5">
        {{ Form::label('home_title_three', __('messages.cms_about.about_title_three').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('about_title_three', $cmsServices['about_title_three'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','onkeypress' => 'return avoidSpace(event);','placeholder' => __('messages.cms_about.about_title_three')]) }}
    </div>
</div>
<div class="flex flex-wrap">
    <div class="flex-1 sm-4 my-0 mb-5">
        {{ Form::label('about_description_title', __('messages.cms_about.about_desc_one').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::textarea('about_description_one', $cmsServices['about_description_one'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','onkeypress' => 'return avoidSpace(event);', 'placeholder' => __('messages.cms_about.about_desc_one')]) }}

    </div>
    <div class="flex-1 sm-4 mb-5">
        {{ Form::label('about_description_title',  __('messages.cms_about.about_desc_two').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::textarea('about_description_two', $cmsServices['about_description_two'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','onkeypress' => 'return avoidSpace(event);','placeholder' => __('messages.cms_about.about_desc_two')]) }}
    </div>
    <div class="flex-1 sm-4 mb-5">
        {{ Form::label('about_description_three',  __('messages.cms_about.about_desc_three').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::textarea('about_description_three', $cmsServices['about_description_three'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','onkeypress' => 'return avoidSpace(event);','placeholder' => __('messages.cms_about.about_desc_three')]) }}
    </div>
</div>
<div class="flex flex-wrap">

    <div class="flex-1 sm-4 mb-5" io-image-input="true">
        <label for="home_banner" class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('messages.cms_about.about_image_one').':' }}
            <span class="required"></span>
            <span data-bs-toggle="tooltip"
                  data-placement="top"
                  data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="fas fa-question-circle ml-1  general-question-mark"></i>
</span>
        </label>
        <div class="block">
            <div class="image-picker">
                <div class="image previewImage" id="aboutImagePreviewOne"
                     style="background-image: url({{ ($cmsServices['about_image_one']) ?"'".asset($cmsServices['about_image_one'])."'" : asset('front_web/images/register.png') }})">
                </div>
                <span class="picker-edit rounded-circle text-gray-500 fs-small"
                      data-bs-toggle="tooltip"
                      data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('about_image_one',['class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
            </div>
        </div>
    </div>
    <div class="flex-1 sm-4 mb-5" io-image-input="true">
        <label for="about_image_two" class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('messages.cms_about.about_image_two').':' }}
            <span class="required"></span>
           <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="fas fa-question-circle ml-1  general-question-mark"></i>
</span>
        </label>
        <div class="block">
            <div class="image-picker">
                <div class="image previewImage" id="aboutImagePreviewTwo"
                     style="background-image: url({{ ($cmsServices['about_image_two']) ?"'".asset($cmsServices['about_image_two'])."'" : asset('front_web/images/resume.png') }})">
                </div>
                <span class="picker-edit rounded-circle text-gray-500 fs-small"
                      data-bs-toggle="tooltip"
                      data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('about_image_two',['class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
            </div>
        </div>
    </div>
    <div class="flex-1 sm-4 mb-5" io-image-input="true">
        <label for="about_image_three" class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('messages.cms_about.about_image_three').':' }}
            <span class="required"></span>
           <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="fas fa-question-circle ml-1  general-question-mark"></i>
</span>
        </label>
        <div class="block">
            <div class="image-picker">
                <div class="image previewImage" id="aboutImagePreviewThree"
                     style="background-image: url({{ ($cmsServices['about_image_three']) ?"'".asset($cmsServices['about_image_three'])."'" : asset('front_web/images/working.png') }})">
                </div>
                <span class="picker-edit rounded-circle text-gray-500 fs-small"
                      data-bs-toggle="tooltip"
                      data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('about_image_three',['class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
            </div>
        </div>
    </div>

</div>
<!-- Submit Field -->
<div class="flex justify-end mt-5">
    {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3']) }}
</div>

