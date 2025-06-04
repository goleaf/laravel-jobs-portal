<div id="editCurrencyModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2>{{ __('messages.salary_currency.edit_salary_currency')  }}</h2>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            {{ Form::open(['id'=>'editCurrencyForm'])  }}
            <div class="px-6 py-4 scroll-y">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hidden hide" id="editValidationErrorsBox"></div>
                {{ Form::hidden('currencyId',null,['id'=>'currencyId'])  }}
                <div class="flex flex-wrap">
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('currency_name',__('messages.salary_currency.currency_name').':', ['class' => 'form-label required mb-3'])  }}
                        {{ Form::text('currency_name', null, ['id'=>'editCurrencyName','class' => 'form-control ','required','placeholder' => __('messages.salary_currency.currency_name')])  }}
                    </div>
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('currency_icon',__('messages.salary_currency.currency_icon').':', ['class' => 'form-label required mb-3'])  }}
                        {{ Form::text('currency_icon', null, ['id'=>'editCurrencyIcon','class' => 'form-control ','required','placeholder' => __('messages.salary_currency.currency_icon')])  }}
                    </div>
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('currency_code', __('messages.salary_currency.currency_code').':',
                            ['class' => 'required fw-bold fs-6 mb-2'])  }}
                        {{ Form::text('currency_code', null, ['class' => 'form-control  mb-3 mb-lg-0 currency-code', 'placeholder' =>                                        __('messages.salary_currency.currency_code'), 'required','id'=>'editCurrencyCode'])  }}
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary me-2','id' => 'btnEditSave','data-loading-text' => "<span class="spinner-border spinner-border-sm"></span>".__('messages.common.process')])  }}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-gray-500 text-white hover:bg-gray-600 px-4 py-2 rounded font-medium transition-colors -active-light-primary me-2"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel')  }}</button>
            </div>
            {{ Form::close()  }}
        </div>
    </div>
</div>
