<div id="showModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3>{{ __('messages.candidate.reported_candidate_detail') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger  hide hidden" id="maritalStatusValidationErrorsBox"></div>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6">
                        {{ Form::label('image', __('messages.post.image').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showImage"></p>
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('candidate_name', __('messages.job_application.candidate_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showReportedCandidate"></p>
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('reported_by', __('messages.company.reported_by').':',['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showReportedBy"></p>
                    </div>
                    <div class="flex-1 sm-6 mb-5">
                        {{ Form::label('reported_on', __('messages.company.reported_on').':',['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <br>
                        {{ date('jS M, Y', `<p id="showReportedWhen"></p>` ) }}
                    </div>
                    <div class="flex-1 sm-12 mb-5">
                        {{ Form::label('notes', __('messages.company.notes').':',['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showReportedNote"></p>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
