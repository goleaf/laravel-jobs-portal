<div id="showInquiryModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 fixed inset-0 z-50 overflow-y-auto -lg">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border-gray-300 -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.inquiry.inquiry_details') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'inquiryShowForm']) }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border border-gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger fs-4 text-white flex items-center hidden"
                     id="inquiryShowValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('name', __('messages.inquiry.name').(':'), ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <br>
                        <p id="showInquiresName" class="fs-5 text-gray-800"></p>
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('email', __('messages.inquiry.email').(':'), ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showInquiresEmail" class="fs-5 text-gray-800"></p>
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('phone_no', __('messages.inquiry.phone_no').(':'), ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showInquiresPhoneNo" class="fs-5 text-gray-800"></p>
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('subject', __('messages.inquiry.subject').(':'), ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showInquiresSubject" class="fs-5 text-gray-800"></p>
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('created_at', __('messages.common.created_on').(':'), ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showInquiresCreatedAt" class="fs-5 text-gray-800"></p>
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('updated_at', __('messages.common.last_updated').(':'), ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showUpdatedAt" class="fs-5 text-gray-800"></p>
                    </div>
                    <div class="flex-1 sm-12 mb-5">
                        {{ Form::label('message',__('messages.inquiry.message').(':'), ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showInquiresMessage" class="fs-5 text-gray-800"></p>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

