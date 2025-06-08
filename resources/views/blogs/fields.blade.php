<div class="flex-wrap flex">
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('title',__('messages.post.title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}<span
                class="required"></span>
        {{ Form::text('title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' => __('messages.post.title')]) }}
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('blog_category_id', __('messages.post_category.post_category').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="text-red-600">*</span>
        {{ Form::select('blogCategories[]', $blogCategories, isset($post)?$selectedBlogCategories:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'blog_category_id','multiple'=>true,'text-red-500']) }}
    </div>
    <div class="mb-5 px-4-xl-6 md:w-6/12 flex-1 sm-12" io-image-input="true">
        <label for="category_image" class="mb-1 block text-sm font-medium text-gray-700">
            {{ __('messages.post.image').':' }}
            <span class="required"></span>
           <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="ml-1 fas fa-question-circle general-question-mark"></i>
</span>
        </label>
        <div class="block">
            <div class="image-picker">
                <div class="image previewImage" id="previewImage"
                     style="background-image: url({{ !empty($post->blog_image_url) ? asset($post->blog_image_url) : asset('front_web/images/blog-1.png') }})">
                </div>
                <span class="rounded picker-edit -full text-gray-500 fs-small"
                      data-bs-toggle="tooltip"
                      data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('image',['class' => 'image-upload hidden', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
            </div>
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label('description',__('messages.post.description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}<span
                class="required"></span>
        {{ --  Form::textarea('description', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id' => 'description', 'rows' => '5'])  -- }}
        <div id="details"></div>
        {{ Form::hidden('description', null, ['id' => 'postDescription']) }}
    </div>
</div>
<div class="mt-5 flex justify-end">
    {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','name' => 'save', 'id' => 'saveJob']) }}
    <a href="{{ route('posts.index') }}"
       class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
</div>
