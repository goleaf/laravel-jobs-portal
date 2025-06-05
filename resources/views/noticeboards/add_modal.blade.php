<div id="addNoticeboardModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border border-gray-300 -gray-300 -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.noticeboard.new_noticeboard') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'addNoticeboardForm']) }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border border border-gray-300 -gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger fs-4 text-white flex items-center hidden"
                     id="salaryPeriodValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <div class="mb-5">
                    {{ Form::label('title',__('messages.noticeboard.title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    {{ Form::text('title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','placeholder' => __('messages.noticeboard.title')]) }}
                </div>
                <div class="mb-5">
                    {{ Form::label('description',__('messages.noticeboard.description').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    <div id="addNoticeboardDescriptionQuillData"></div>
                    {{ Form::hidden('description', null, ['id' => 'termData']) }}

                </div>
                <div class="mb-5">
                    {{ Form::label('status',__('messages.common.status').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <br>
                    <div class="flex items-center form-switch mb-0">
                        <input class="flex items-center input is-active" name="is_active" type="checkbox"
                               value="1"
                               tabindex="8" id="active" checked>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border border border-gray-300 -gray-300 -gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0','id' => 'noticeboardSaveBtn','data-loading-text' =>"<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent"
                        id="btnCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
