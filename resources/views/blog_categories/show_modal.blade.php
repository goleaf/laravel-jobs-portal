<div id="showBlogCategoryModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.post_category.post_category_detail') }}</h3>
                <button type="button" aria-label="Close" class="rounded px-4 py-2 font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showBlogCategoryForm']) }}
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 px-4 py-3 -md -gray-300 -md danger hide hidden" id="maritalStatusValidationErrorsBox"></div>
                <div class="mb-5">
                    {{ Form::label('name',__('messages.post_category.name').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showBlogCategoryName" class="text-gray-600"></p>
                    </div>
                    <div class="mb-5">
                        {{ Form::label('description', __('messages.post_category.description').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showBlogCategoryDescription" class="text-gray-600"></p>
                    </div>

                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

