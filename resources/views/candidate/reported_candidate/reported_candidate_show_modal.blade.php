<div id="showCandidateModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.candidate.reported_candidate_detail') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showReportedCandidateForm']) }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger  hide hidden" id="maritalStatusValidationErrorsBox">
                    <i class='fa-solid fa-face-frown me-4'></i>
                </div>
                <div class="mb-5">
                    <div class="flex-1 sm-12 mb-5">
                        {{ Form::label('employer_name', __('messages.company.candidate_name').':', ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showReportedCandidate" class="fs-4 text-gray-800"></p>
                    </div>
                    <div class="flex-1 sm- mb-5">
                        {{ Form::label('employer_name', __('messages.post.image').':', ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <br>
                        <p id="showImage" class="image image-medium me-3"></p>
                    </div>
                    <div class="flex-1 sm-12 mb-5">
                        {{ Form::label('reported_by', __('messages.company.reported_by').':',['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showReportedBy" class="fs-4 text-gray-800"></p>
                    </div>
                    <div class="flex-1 sm-12 mb-5">
                        {{ Form::label('reported_on', __('messages.company.reported_on').':',['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <br>
                        <p id="showReportedWhen" class="fs-4 text-gray-800"></p>
                    </div>
                    <div class="flex-1 sm-12 mb-5">
                        {{ Form::label('notes', __('messages.company.notes').':',['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showReportedNote" class="fs-4 text-gray-800" style="width:100%; word-wrap: break-word;"></p>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
