<div class="fixed inset-0 z-50 overflow-y-auto fade" id="scheduleSlotBookModal" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center min-h-screen px-4 -lg">
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.job_stage.choose_slots') }}</h3>
                <button type="button" class="transition duration-150 ease-in-out flex-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            {{ Form::open(['id' => 'scheduleSlotBookForm']) }}
                <div class="px-6 py-4">
                    <div class="rounded p-4 mb-4 rounded p-4 rounded p-4 -md -slot-msg -md danger hidden"
                         id="scheduleSlotBookValidationErrorsBox"></div>
                    <div class="rounded p-4 mb-4 rounded p-4 rounded p-4 -md -slot-msg -md success hidden"
                         id="selectedSlotBookValidationErrorsBox"></div>
                    <div class="mt-2 slot-main-div">

                    </div>
                    <div class="flex-wrap p-3 flex choose-slot-textarea hidden">
                    <textarea name="choose_slot_notes" class="rounded border" required
                              placeholder="{{ __('messages.flash.enter_notes') }}" flex flex-wrap -mx-4s="3"></textarea>
                    </div>
                    <div id="historyMainDiv" class="mt-5 hidden">
                        <h3>{{ __('messages.job_stage.history') }}</h3>
                        <div id="historyDiv" class="scroll-history-div">

                        </div>
                    </div>
                    <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                        {{ Form::button(__('messages.job_stage.send_slots'), ['type'=>'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-flex-1 px-4ors me-3','id'=>'scheduleInterviewBtnSave','data-loading-text'=>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span> Processing..."]) }}
                        <button type="submit" value="" class="border border-gray-300 bg-transparent" id="rejectSlotBtnSave"
                                name="rejectSlot">{{ __('messages.job_stage.reject_all_slot') }}
                        </button>
                        <button id="scheduleInterviewBtnCancel" type="button" class="border border-gray-300 bg-transparent"
                                data-bs-dismiss="modal">{{ __('messages.common.cancel') }}
                        </button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
