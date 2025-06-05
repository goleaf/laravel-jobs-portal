<div id="showModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border-gray-300 -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.job.reported_jobs_detail') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="px-6 py-4">
                    <div class="mb-5">
                        {{ Form::label('title', __('messages.company.title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p class="text-gray-600 showName"></p>
                    </div>
                        <div class="mb-5">
                            {{ Form::label('company_image', __('messages.company.image').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                            <br>
                            <div class="image image-medium me-3">
                                <img src="" id="documentUrl" class="testimonial- fixed inset-0 z-50 overflow-y-auto -img">
                                <label id="noDocument">{{ __('messages.n/a') }}</label>
                            </div>
                        </div>
                <div class="mb-5">
                    {{ Form::label('reported_by',__('messages.company.reported_by').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                            <p id="showReportedBy" class="text-gray-600"></p>
                        </div>
                        <div class="mb-5">
                            {{ Form::label('reported_on',__('messages.company.reported_on').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                            <p id="showReportedOn" class="text-gray-600"></p>
                        </div>
                        <div class="mb-5">
                            {{ Form::label('notes',__('messages.company.notes').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                            <p id="showNote" class="text-gray-600" style="width:100%; word-wrap: break-word;"></p>
                        </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>




