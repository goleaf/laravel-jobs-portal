<div class="modal fade" id="reportToCompanyModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.job.add_note')</h5>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="frm" id="reportToCompany">
                @csrf
            <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="userId" value="{{ (getLoggedInUserId() !== null) ? getLoggedInUserId() : null }}">
                        <input type="hidden" name="companyId" value="{{ $companyDetail->id }}">
                        <textarea rows="5" id="noteForReportToCompany" name="note" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required></textarea>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn px-4 py-2 rounded font-medium transition-colors -primary" name="log-in" id="btnSave">@lang('messages.common.report')</button>
            </div>
            </form>
        </div>
    </div>
</div>
