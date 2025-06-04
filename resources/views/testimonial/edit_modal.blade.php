<div class="fixed inset-0 z-50 overflow-y-auto fade" tabindex="-1" role="dialog" id="editTestimonialModal" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4" role="document">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.testimonial.edit_testimonial') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'editTestimonialForm'])  }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hidden" id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ Form::hidden('testimonialId',null,['id'=>'testimonialId'])  }}
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('customer_name', __('messages.testimonial.customer_name').':', ['class' => 'form-label'])  }}
                        <span class="required"></span>
                        {{ Form::text('customer_name', null, ['class' => 'form-control form-control-solid','required' , 'id' => 'editCustomerName', 'placeholder' => __('messages.testimonial.customer_name')])  }}
                    </div>

                    <div class="flex-1 -sm-12 mb-5" io-image-input="true">
                        <label for="customer_image" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('messages.testimonial.customer_image').':' }}
                            <span class="required"></span>
                            <span data-bs-toggle="tooltip"
                                  data-placement="top"
                                  data-bs-original-title="{{ __('messages.setting.image_validation')  }}">
        <i class="fas fa-question-circle ml-1  general-question-mark"></i>
</span>
                        </label>
                        <div class="block">
                            <div class="image-picker">
                                <div class="image previewImage" id="editPreviewImage"
                                     style="background-image: url({{ asset('assets/img/infyom-logo.png')  }})">
                                </div>
                                <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                                      data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('customer_image',['class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg'])  }}
                    </label>
                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('description', __('messages.testimonial.description').':', ['class' => 'form-label'])  }}
                        <span class="required"></span>
                        <div id="editTestimonialDescriptionQuillData"></div>
                        {{ Form::hidden('description', null, ['id' => 'testimonial_edit_desc'])  }}
                    </div>
                </div>

            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'testimonialEditBtn','data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')])  }}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0" id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel')  }}</button>
            </div>
            {{ Form::close()  }}
        </div>
    </div>
</div>
