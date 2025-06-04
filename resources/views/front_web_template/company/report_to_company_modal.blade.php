<div class="modal fade" id="reportToCompanyModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.job.add_note')</h5>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'reportToCompany', 'name' => 'frm'])
                @csrf
                <div class="modal-body">
                    <div class="flex-1 -md-12 mb-4">
                        <div class="form-group">
                            {{ Form::hidden('userId', (getLoggedInUserId() !== null) ? getLoggedInUserId() : null) }}
                            {{ Form::hidden('companyId', $companyDetail->id) }}
                            {{ Form::label('noteForReportToCompany', __('web.web_contact.your_message').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                            <span class="text-primary-600">*</span>
                            {{ Form::textarea('note', null, [
                                'class' => 'form-control fs-14 text-gray br-10',
                                'rows' => '5',
                                'id' => 'noteForReportToCompany',
                                'required'
                            ]) }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{ Form::button(__('messages.common.report'), [
                        'type' => 'submit',
                        'class' => 'btn btn-primary btn-primary-register',
                        'id' => 'btnSave',
                        'name' => 'log-in'
                    ]) }}
                </div>
            @formClose()
        </div>
    </div>
</div>
