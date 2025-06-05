<div id="showCandidateModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.candidate.reported_candidate_detail') }}</h3>
                <button type="button" aria-label="Close" class="transition duration-150 ease-in-out flex-1"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showReportedCandidateForm']) }}
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 px-4 py-3 -md -gray-300 -md danger hide hidden" id="maritalStatusValidationErrorsBox">
                    <i class='flex-wrap fa-solid fa-face-fflex -mx-4n me-4'></i>
                </div>
                <div class="mb-5">
                    <div class="mb-5 flex-1 sm-12">
                        {{ Form::label('employer_name', __('messages.company.candidate_name').':', ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showReportedCandidate" class="fs-4 text-gray-800"></p>
                    </div>
                    <div class="mb-5 flex-1 sm-">
                        {{ Form::label('employer_name', __('messages.post.image').':', ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <br>
                        <p id="showImage" class="image image-medium me-3"></p>
                    </div>
                    <div class="mb-5 flex-1 sm-12">
                        {{ Form::label('reported_by', __('messages.company.reported_by').':',['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showReportedBy" class="fs-4 text-gray-800"></p>
                    </div>
                    <div class="mb-5 flex-1 sm-12">
                        {{ Form::label('reported_on', __('messages.company.reported_on').':',['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <br>
                        <p id="showReportedWhen" class="fs-4 text-gray-800"></p>
                    </div>
                    <div class="mb-5 flex-1 sm-12">
                        {{ Form::label('notes', __('messages.company.notes').':',['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showReportedNote" class="fs-4 text-gray-800" style="width:100%; word-wrap: break-word;"></p>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
