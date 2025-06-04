<div id="addCurrencyModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.salary_currency.new_salary_currency') }}</h2>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            {{ Form::open(['id'=>'addCurrencyForm']) }}
            <div class="modal-body scroll-y">
                <div class="alert p-4 rounded-md mb-4 -danger hidden hide" id="validationErrorsBox"></div>
                <div class="flex flex-wrap">
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('currency_name',__('messages.salary_currency.currency_name').':', ['class' => 'form-label required mb-3']) }}
                        {{ Form::text('currency_name', null, ['id'=>'name','class' => 'form-control ','required', 'placeholder' => __('messages.salary_currency.currency_name')]) }}
                    </div>
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('currency_icon',__('messages.salary_currency.currency_icon'), ['class' => 'form-label required mb-3']) }}
                        {{ Form::text('currency_icon', null, ['id'=>'icon','class' => 'form-control ','required', 'placeholder' => __('messages.salary_currency.currency_icon')]) }}
                    </div>
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('currency_code', __('messages.salary_currency.currency_code').':', ['class' => 'required mb-2']) }}
                        {{ Form::text('currency_code', null, ['class' => 'form-control mb-3 mb-lg-0 currency-code', 'placeholder' =>                                        __('messages.salary_currency.currency_code'),'required']) }}
                    </div>

                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary me-2','id' => 'btnSave','data-loading-text' => "<span class='spinner-border spinner-border-sm'></span>".__('messages.common.process')]) }}
                <button type="button" class="btn bg-gray-500 text-white hover:bg-gray-600 px-4 py-2 rounded font-medium transition-colors -active-light-primary me-2"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
