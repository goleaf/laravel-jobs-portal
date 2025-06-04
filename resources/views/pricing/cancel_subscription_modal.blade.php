<div id="cancelSubscriptionModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.plan.cancel_subscription') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id' => 'cancelSubscriptionForm'])
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger hidden" id="validationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-12 mb-0">
                        {{ Form::label('cancellation_reason', __('messages.plan.cancel_reason').':', ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::textarea('cancellation_reason', null, [
                            'id' => 'reason',
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('messages.plan.cancel_reason')
                        ]) }}
                    </div>
                </div>
            </div>
            <div class="modal-footer mt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary m-0',
                    'id' => 'btnCancelSave'
                ]) }}
                <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0" data-bs-dismiss="modal">
                    <i class="bx bx-x block d-sm-none"></i>
                    <span>{{ __('messages.common.cancel') }}</span>
                </button>
            </div>
            @formClose()
        </div>
    </div>
</div>
