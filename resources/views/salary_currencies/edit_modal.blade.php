<div id="editCurrencyModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border border-gray-300 -gray-300 -gray-200">
                <h2>{{ __('messages.salary_currency.edit_salary_currency') }}</h2>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            {{ Form::open(['id'=>'editCurrencyForm']) }}
            <div class="px-6 py-4 scroll-y">
                <div class="px-4 py-3 rounded-md border border border border-gray-300 -gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger hidden hide" id="editValidationErrorsBox"></div>
                {{ Form::hidden('currencyId',null,['id'=>'currencyId']) }}
                <div class="flex flex-wrap">
                    <div class="mb-4 flex-1 sm-12 mb-5">
                        {{ Form::label('currency_name',__('messages.salary_currency.currency_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 required mb-3']) }}
                        {{ Form::text('currency_name', null, ['id'=>'editCurrencyName','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','text-red-500','placeholder' => __('messages.salary_currency.currency_name')]) }}
                    </div>
                    <div class="mb-4 flex-1 sm-12 mb-5">
                        {{ Form::label('currency_icon',__('messages.salary_currency.currency_icon').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 required mb-3']) }}
                        {{ Form::text('currency_icon', null, ['id'=>'editCurrencyIcon','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','text-red-500','placeholder' => __('messages.salary_currency.currency_icon')]) }}
                    </div>
                    <div class="mb-4 flex-1 sm-12 mb-5">
                        {{ Form::label('currency_code', __('messages.salary_currency.currency_code').':',
                            ['class' => 'required fw-bold fs-6 mb-2']) }}
                        {{ Form::text('currency_code', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm  mb-3 mb-lg-0 currency-code', 'placeholder' =>                                        __('messages.salary_currency.currency_code'), 'text-red-500','id'=>'editCurrencyCode']) }}
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border border border-gray-300 -gray-300 -gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-2','id' => 'btnEditSave','data-loading-text' =>"<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
