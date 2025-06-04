<div class="modal fade" id="reportToCandidateModal" tabindex="-1" aria-labelledby="reportToCandidate"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.job.add_note') }}</h5>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="frm" id="reportToCandidate">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="userId"
                           value="{{ (getLoggedInUserId() !== null) ? getLoggedInUserId() : null }}">
                    <input type="hidden" name="candidateId" value="{{ $candidateDetails->id }}">
                    <div class="form-group">
                        <textarea rows="5" id="noteForReportToCompany" name="note" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary"
                            data-bs-dismiss="modal">{{ __('messages.common.close') }}</button>
                    <button type="submit" class="btn px-4 py-2 rounded font-medium transition-colors -primary"
                            data-bs-loading-text="<span class='spinner-border spinner-border-sm'></span> {{__('messages.common.process')}}"
                            id="btnReportCandidate">{{ __('messages.common.report') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
