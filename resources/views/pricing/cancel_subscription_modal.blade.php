<div id="cancelSubscriptionModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border-gray-300 -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.plan.cancel_subscription') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            @formOpen(['id' => 'cancelSubscriptionForm'])
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border border-gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger hidden" id="validationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-12 mb-0">
                        {{ Form::label('cancellation_reason', __('messages.plan.cancel_reason').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        {{ Form::textarea('cancellation_reason', null, [
                            'id' => 'reason',
                            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                            'text-red-500',
                            'placeholder' => __('messages.plan.cancel_reason')
                        ]) }}
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border border-gray-300 -gray-200 flex justify-end space-x-2 mt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0',
                    'id' => 'btnCancelSave'
                ]) }}
                <button type="button" class="border border-gray-300 bg-transparent" data-bs-dismiss="modal">
                    <i class="bx bx-x block d-sm-none"></i>
                    <span>{{ __('messages.common.cancel') }}</span>
                </button>
            </div>
            @formClose()
        </div>
    </div>
</div>
