<div id="editSlotModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 modal-lg">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.job_stage.edit_slot') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'editSlotForm']) }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger hidden" id="editSlotValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <input type="hidden" id="editSlotId">
                <div class="add-slot-main-div">
                    <div class="slot-box rounded shadow mb-5">
                        <div class="flex flex-wrap p-5">
                            <div class="flex-1 sm-6">
                                <div class="">
                                    <label name="date" class="block text-sm font-medium text-gray-700 mb-1"><?php echo __(
                                                'messages.job_stage.date'
                                            ).':' ?></label>
                                    <span class="required"></span>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white' }}"
                                           name="date" id="editDate" required>
                                </div>
                                <div class="mb-0 mt-3">
                                    <label name="time" class="block text-sm font-medium text-gray-700 mb-1"><?php echo __(
                                                'messages.job_stage.time'
                                            ).':' ?></label>
                                    <span class="required"></span>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ (getLoggedInUser()->theme_mode) ?"bg-light' : 'bg-white' }}"
                                           name="time" id="editTime" required>
                                </div>
                            </div>
                            <div class="flex-1 sm-6 mb-0">
                                <label name="notes" class="block text-sm font-medium text-gray-700 mb-1"><?php echo __('messages.company.notes').':' ?>
                                </label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 textarea-sizing" name="notes"
                                          id="editNotes" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0','id' => 'editSlotBtnSave','data-loading-text' =>"<span class="spinner-border spinner-border-sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary my-0 ms-5 me-0" id="editSlotBtnCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
