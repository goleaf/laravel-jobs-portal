<div class="fixed inset-0 z-50 overflow-y-auto fade" id="scheduleSlotBookModal" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 modal-lg">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.job_stage.choose_slots') }}</h3>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            {{ Form::open(['id' => 'scheduleSlotBookForm']) }}
                <div class="px-6 py-4">
                    <div class="alert-slot-msg p-4 rounded-md mb-4 danger hidden rounded p-4"
                         id="scheduleSlotBookValidationErrorsBox"></div>
                    <div class="alert-slot-msg p-4 rounded-md mb-4 success hidden rounded p-4"
                         id="selectedSlotBookValidationErrorsBox"></div>
                    <div class="slot-main-div mt-2">

                    </div>
                    <div class="flex flex-wrap p-3 choose-slot-textarea hidden">
                    <textarea name="choose_slot_notes" class="textarea-sizing w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 solid" required
                              placeholder="{{ __('messages.flash.enter_notes') }}" rows="3"></textarea>
                    </div>
                    <div id="historyMainDiv" class="hidden mt-5">
                        <h3>{{ __('messages.job_stage.history') }}</h3>
                        <div id="historyDiv" class="scroll-history-div">

                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                        {{ Form::button(__('messages.job_stage.send_slots'), ['type'=>'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','id'=>'scheduleInterviewBtnSave','data-loading-text'=>"<span class="spinner-border spinner-border-sm"></span> Processing..."]) }}
                        <button type="submit" value="" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors danger rejectSlot me-3" id="rejectSlotBtnSave"
                                name="rejectSlot">{{ __('messages.job_stage.reject_all_slot') }}
                        </button>
                        <button id="scheduleInterviewBtnCancel" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary my-0 me-0"
                                data-bs-dismiss="modal">{{ __('messages.common.cancel') }}
                        </button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
