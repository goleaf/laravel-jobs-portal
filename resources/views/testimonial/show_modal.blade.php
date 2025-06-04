<div class="fixed inset-0 z-50 overflow-y-auto fade" tabindex="-1" role="dialog" id="showTestimonialModal" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4" role="document">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.testimonial.testimonial_detail') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="px-6 py-4">
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('name', __('messages.testimonial.customer_name').':', ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <br>
                        <span id="showCustomerName" class="fs-5 text-gray-800"></span>
                    </div>
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('customer_image', __('messages.testimonial.customer_image').':', ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <br>
                        {{--                        <a href="#" id="documentUrl" target="_blank"></a>--}}
                        <div class="image image-medium">
                            <img src="" id="documentUrl" class="testimonial-modal-img"
                                 style="">
                        </div>
                        <label id="noDocument">{{ __('messages.common.n/a') }}</label>
                    </div>
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('description',__('messages.testimonial.description').':', ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <br>
                        <div class="reported-note">
                            <span id="showTestimonialDescription" class="fs-5 text-gray-800"></span>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
