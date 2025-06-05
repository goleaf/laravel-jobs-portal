<div class="fixed inset-0 z-50 overflow-y-auto fade" id="reportToCandidateModal" tabindex="-1" aria-labelledby="reportToCandidate"
     aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h5 class="fixed inset-0 z-50 overflow-y-auto-title" id="exampleModalLabel">{{ __('messages.job.add_note') }}</h5>
                <button type="button" class="rounded px-4 py-2 font-medium transition-colors close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="frm" id="reportToCandidate">
                @csrf
                <div class="px-6 py-4">
                    <input type="hidden" name="userId"
                           value="{{ (getLoggedInUserId() !== null) ? getLoggedInUserId() : null }}">
                    <input type="hidden" name="candidateId" value="{{ $candidateDetails->id }}">
                    <div class="mb-4">
                        <textarea rows="5" id="noteForReportToCompany" name="note" class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                  required></textarea>
                    </div>
                </div>
                <div class="border border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                    <button type="button" class="border border-gray-300 bg-transparent"
                            data-bs-dismiss="modal">{{ __('messages.common.close') }}</button>
                    <button type="submit" class="border border-gray-300 bg-transparent"
                            data-bs-loading-text="<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span> {{ __('messages.common.process') }}"
                            id="btnReportCandidate">{{ __('messages.common.report') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
