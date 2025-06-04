<div class="fixed inset-0 z-50 overflow-y-auto fade" id="reportToCompanyModal">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.job.add_note')</h5>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="frm" id="reportToCompany">
                @csrf
            <div class="px-6 py-4">
                    <div class="form-group">
                        <input type="hidden" name="userId" value="{{ (getLoggedInUserId() !== null) ? getLoggedInUserId() : null }}">
                        <input type="hidden" name="companyId" value="{{ $companyDetail->id }}">
                        <textarea rows="5" id="noteForReportToCompany" name="note" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required></textarea>
                    </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary" name="log-in" id="btnSave">@lang('messages.common.report')</button>
            </div>
            </form>
        </div>
    </div>
</div>
