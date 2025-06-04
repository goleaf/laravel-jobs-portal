<div id="showBlogCategoryModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.post_category.post_category_detail') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showBlogCategoryForm']) }}
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger  hide hidden" id="maritalStatusValidationErrorsBox"></div>
                <div class="mb-5">
                    {{ Form::label('name',__('messages.post_category.name').(':'), ['class' => 'form-label']) }}
                        <p id="showBlogCategoryName" class="text-gray-600"></p>
                    </div>
                    <div class="mb-5">
                        {{ Form::label('description', __('messages.post_category.description').(':'),['class' => 'form-label']) }}
                        <p id="showBlogCategoryDescription" class="text-gray-600"></p>
                    </div>

                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

