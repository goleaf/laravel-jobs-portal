<div id="editIndustriesModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.industry.edit_industry') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'editIndustryForm']) }}
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger fs-4 text-white flex items-center hidden"
                     id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ Form::hidden('industryId',null,['id'=>'industryId']) }}
                <div class="mb-5">
                    {{ Form::label('name', __('messages.industry.name').(':'), ['class' => 'form-label']) }}
                    <span class="required"></span>
                    {{ Form::text('name', null, ['id' => 'editName','class' => 'form-control','required','placeholder' => __('messages.industry.name')]) }}
                </div>
                <div class="mb-5">
                    {{ Form::label('description', __('messages.industry.description').(':'), ['class' => 'form-label']) }}
                    <span class="required"></span>
                    {{--                        {{ Form::textarea('description', null, ['id' => 'editDescription','class' => 'form-control form-control-solid','required']) }}--}}
                    <div id="editIndustryDescriptionQuillData"></div>
                    {{ Form::hidden('description', null, ['id' => 'edit_industry_desc']) }}
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'industriesSaveBtn','data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')]) }}
                <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0" id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
