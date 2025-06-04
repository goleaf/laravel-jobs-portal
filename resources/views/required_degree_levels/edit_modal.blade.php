<div id="editDegreeLevelModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.required_degree_level.edit_required_degree_level') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'editDegreeLevelForm']) }}
            <div class="modal-body ">
                <div class="alert p-4 rounded-md mb-4 -danger fs-4 text-white flex items-center hidden"
                     id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ Form::hidden('requiredDegreeLevelId',null,['id'=>'requiredDegreeLevelId']) }}
                <div class="flex flex-wrap">
                    <div class="form-group flex-1 -sm-12 mb-5">
                        {{ Form::label('name',__('messages.required_degree_level.name').(':'), ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::text('name', null, ['id'=>'editName','class' => 'form-control','required','placeholder' => __('messages.required_degree_level.name')]) }}
                    </div>

                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'btnEditSave','data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')]) }}
                <button type="button" class="btn bg-gray-100 text-gray-800 hover:bg-gray-200 px-4 py-2 rounded font-medium transition-colors -active-light-primary ms-5 me-2"
                        id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
