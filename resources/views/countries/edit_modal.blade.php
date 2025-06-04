<div id="editCountryModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.country.edit_country') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'editCountryForm']) }}
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger fs-4 text-white flex items-center hidden"
                     id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ Form::hidden('countryId',null,['id'=>'countryId']) }}
                <div class="form-group flex-1 -sm-12 mb-5">
                    {{ Form::label('name',__('messages.common.name').(':'), ['class' => 'form-label']) }}
                    <span class="required"></span>
                    {{ Form::text('name', null, ['required', 'id'=>'editName','class' => 'form-control', 'placeholder' => __('messages.common.name')]) }}
                </div>
                <div class=" mb-5">
                    {{ Form::label('short_code',__('messages.country.short_code').(':'), ['class' => 'form-label']) }}
                    <span class="required"></span>
                    {{ Form::text('short_code', null, ['required', 'id'=>'editShortCode','class' => 'form-control', 'placeholder' => __('messages.country.short_code')]) }}
                </div>
                <div class=" mb-5">
                    {{ Form::label('phone_code',__('messages.country.phone_code').(':'), ['class' => 'form-label']) }}
                    {{ Form::text('phone_code', null, ['class' => 'form-control','id'=>'editPhoneCode', 'maxlength' => '4', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'placeholder' => __('messages.country.phone_code')]) }}
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'btnEditSave','data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')]) }}
                <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>




