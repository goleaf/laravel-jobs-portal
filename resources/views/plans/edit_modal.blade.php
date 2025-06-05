<div id="editSubscriptionModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.plan.edit_subscription_plan') }}</h3>
                <button type="button" aria-label="Close" class="rounded px-4 py-2 font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'editSubscriptionForm']) }}
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hidden" id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ Form::hidden('planId',null,['id'=>'planId']) !!}
                <div class="mb-5">
                    {{ Form::label('name', __('messages.inquiry.name').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    {{ Form::text('name', null, ['id'=>'editName','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' => __('messages.inquiry.name')]) }}
                </div>
                <div class="mb-5">
                    {{ Form::label('allowed_jobs', __('messages.plan.allowed_jobs').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    {{ Form::text('allowed_jobs', null, ['id'=>'editAllowedJobs','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm allowed-jobs','text-red-500', 'min' => '1', 'max' => '99999', 'placeholder' => __('messages.plan.allowed_jobs'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
                </div>
                <div class="mb-5">
                    {{ Form::label('plan_currency', __('messages.plan.currency').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    {{ Form::select('salary_currency_id',[], null, ['id'=>'editCurrency','text-red-500','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm select2Selector','placeholder' => __('messages.company.select_currency')]) !!}
                </div>
                <div class="mb-5">
                    {{ Form::label('amount', __('messages.plan.amount').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    {{ Form::text('amount', null, ['id'=>'editAmount','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm amount','text-red-500', 'min' => '1', 'max' => '99999','readonly','placeholder' => __('messages.plan.amount')]) }}
                </div>
            </div>
            <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0','id' => 'editSubscriptionSaveBtn','data-loading-text' =>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent"
                        id="planBtnCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
