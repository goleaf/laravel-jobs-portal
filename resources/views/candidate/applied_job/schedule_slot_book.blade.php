<div class="modal fade" id="scheduleSlotBookModal" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{__('messages.job_stage.choose_slots')}}</h3>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            {{ Form::open(['id' => 'scheduleSlotBookForm']) }}
                <div class="modal-body">
                    <div class="alert-slot-msg p-4 rounded-md mb-4 -danger hidden rounded p-4"
                         id="scheduleSlotBookValidationErrorsBox"></div>
                    <div class="alert-slot-msg p-4 rounded-md mb-4 -success hidden rounded p-4"
                         id="selectedSlotBookValidationErrorsBox"></div>
                    <div class="slot-main-div mt-2">

                    </div>
                    <div class="flex flex-wrap p-3 choose-slot-textarea hidden">
                    <textarea name="choose_slot_notes" class="textarea-sizing form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 -solid" required
                              placeholder="{{__('messages.flash.enter_notes')}}" rows="3"></textarea>
                    </div>
                    <div id="historyMainDiv" class="hidden mt-5">
                        <h3>{{ __('messages.job_stage.history') }}</h3>
                        <div id="historyDiv" class="scroll-history-div">

                        </div>
                    </div>
                    <div class="modal-footer pt-0">
                        {{ Form::button(__('messages.job_stage.send_slots'), ['type'=>'submit','class' => 'btn btn-primary me-3','id'=>'scheduleInterviewBtnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                        <button type="submit" value="" class="btn px-4 py-2 rounded font-medium transition-colors -danger rejectSlot me-3" id="rejectSlotBtnSave"
                                name="rejectSlot">{{__('messages.job_stage.reject_all_slot')}}
                        </button>
                        <button id="scheduleInterviewBtnCancel" type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary my-0 me-0"
                                data-bs-dismiss="modal">{{ __('messages.common.cancel') }}
                        </button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
