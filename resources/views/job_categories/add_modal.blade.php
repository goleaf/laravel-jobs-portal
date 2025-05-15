<div id="addJobCategoryModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.job_category.new_job_category') }}</h3>
                <button type="button" aria-label="Close" class="btn-close" data-bs-dismiss="modal">
                    <x-icons.close class="h-4 w-4" />
                </button>
            </div>
            {!! Form::open(['id' => 'addJobCategoryForm']) !!}
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="jobCategoryValidationErrorsBox"></div>
                <div class="mb-5">
                    {!! Form::label('name', __('messages.job_category.name').':', ['class' => 'form-label']) !!}
                    <span class="required"></span>
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'required' => 'required',
                        'placeholder' => __('messages.job_category.name')
                    ]) !!}
                </div>
                <div class="mb-5 h-100">
                    {!! Form::label('description', __('messages.job_category.description').':', ['class' => 'form-label']) !!}
                    <span class="required"></span>
                    <div id="addJobCategoryDescriptionQuillData" class="job-category-description"></div>
                    {!! Form::hidden('description', null, ['id' => 'jobCategoryDescriptionValue']) !!}
                </div>
                <div class="col-xl-6 col-md-6 col-sm-12 mb-5" io-image-input="true">
                    {!! Form::label('category_image', __('messages.common.category_image').':', ['class' => 'form-label']) !!}
                    <span data-bs-toggle="tooltip"
                          data-placement="top"
                          data-bs-original-title="{{ __('messages.setting.image_validation') }}">
                        <i class="fas fa-question-circle ml-1 general-question-mark"></i>
                    </span>
                    <div class="d-block">
                        <div class="image-picker">
                            <div class="image previewImage" id="logoPreview"
                                 style="background-image: url({{ asset('front_web/images/job-categories.png') }})">
                            </div>
                            <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                                  data-placement="top" data-bs-original-title="{{__('messages.tooltip.change_image')}}">
                                <label>
                                    <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                    {!! Form::file('customer_image', [
                                        'class' => 'image-upload d-none',
                                        'accept' => '.png, .jpg, .jpeg'
                                    ]) !!}
                                </label>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pt-0">
                {!! Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary m-0',
                    'id' => 'jobCategoryBtnSave',
                    'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')
                ]) !!}
                {!! Form::button(__('messages.common.cancel'), [
                    'type' => 'button',
                    'class' => 'btn btn-secondary my-0 ms-5 me-0',
                    'id' => 'jobCategoryBtnCancel',
                    'data-bs-dismiss' => 'modal'
                ]) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
