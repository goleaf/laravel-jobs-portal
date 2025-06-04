<div id="editSubscriptionModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.plan.edit_subscription_plan')  }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'editSubscriptionForm'])  }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hidden" id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {!! Form::hidden('planId',null,['id'=>'planId']) !!}
                <div class=" mb-5">
                    {{ Form::label('name', __('messages.inquiry.name').(':'), ['class' => 'form-label'])  }}
                    <span class="required"></span>
                    {{ Form::text('name', null, ['id'=>'editName','class' => 'form-control','required', 'placeholder' => __('messages.inquiry.name')])  }}
                </div>
                <div class=" mb-5">
                    {{ Form::label('allowed_jobs', __('messages.plan.allowed_jobs').(':'), ['class' => 'form-label'])  }}
                    <span class="required"></span>
                    {{ Form::text('allowed_jobs', null, ['id'=>'editAllowedJobs','class' => 'form-control allowed-jobs','required', 'min' => '1', 'max' => '99999', 'placeholder' => __('messages.plan.allowed_jobs'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")'])  }}
                </div>
                <div class=" mb-5">
                    {{ Form::label('plan_currency', __('messages.plan.currency').(':'), ['class' => 'form-label'])  }}
                    <span class="required"></span>
                    {!! Form::select('salary_currency_id',[], null, ['id'=>'editCurrency','required','class' => 'form-select select2Selector','placeholder' => __('messages.company.select_currency')]) !!}
                </div>
                <div class=" mb-5">
                    {{ Form::label('amount', __('messages.plan.amount').(':'), ['class' => 'form-label'])  }}
                    <span class="required"></span>
                    {{ Form::text('amount', null, ['id'=>'editAmount','class' => 'form-control amount','required', 'min' => '1', 'max' => '99999','readonly','placeholder' => __('messages.plan.amount')])  }}
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'editSubscriptionSaveBtn','data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')])  }}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        id="planBtnCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel')  }}</button>
            </div>
            {{ Form::close()  }}
        </div>
    </div>
</div>
