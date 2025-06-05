<div class="fixed inset-0 z-50 overflow-y-auto fade" id="reportToCompanyModal">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border-gray-300 -gray-200">
                <h5 class="fixed inset-0 z-50 overflow-y-auto-title" id="exampleModalLabel">@lang('messages.job.add_note')</h5>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'reportToCompany', 'name' => 'frm'])
                @csrf
                <div class="px-6 py-4">
                    <div class="flex-1 md-12 mb-4">
                        <div class="mb-4">
                            {{ Form::hidden('userId', (getLoggedInUserId() !== null) ? getLoggedInUserId() : null) }}
                            {{ Form::hidden('companyId', $companyDetail->id) }}
                            {{ Form::label('noteForReportToCompany', __('web.web_contact.your_message').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                            <span class="text-indigo-600 -600">*</span>
                            {{ Form::textarea('note', null, [
                                'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10',
                                'rows' => '5',
                                'id' => 'noteForReportToCompany',
                                'text-red-500'
                            ]) }}
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border border-gray-300 -gray-200 flex justify-end space-x-2">
                    {{ Form::button(__('messages.common.report'), [
                        'type' => 'submit',
                        'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors btn-primary-register',
                        'id' => 'btnSave',
                        'name' => 'log-in'
                    ]) }}
                </div>
            @formClose()
        </div>
    </div>
</div>
