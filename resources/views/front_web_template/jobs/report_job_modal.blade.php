<div class="modal fade" id="reportJobAbuseModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.job.add_note') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'reportJobAbuse', 'name' => 'frm'])
                <div class="modal-body">
                    {{ Form::hidden('userId', (getLoggedInUserId() !== null) ? getLoggedInUserId() : null) }}
                    {{ Form::hidden('jobId', $job->id) }}
                    <div class="col-md-12 mb-4">
                        <div class="form-group">
                            {{ Form::label('noteForReportAbuse', __('web.web_contact.your_message').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                            <span class="text-primary">*</span>
                            {{ Form::textarea('note', null, [
                                'class' => 'form-control fs-14 text-gray br-10',
                                'rows' => '5',
                                'id' => 'noteForReportAbuse',
                                'required'
                            ]) }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    {{ Form::button(__('messages.common.close'), [
                        'type' => 'button',
                        'class' => 'btn btn-secondary',
                        'data-bs-dismiss' => 'modal'
                    ]) }}
                    {{ Form::button(__('web.job_details.report'), [
                        'type' => 'submit',
                        'class' => 'btn btn-primary btn-primary-register',
                        'id' => 'btnReportJobAbuse',
                        'data-bs-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')
                    ]) }}
                </div>
            @formClose()
        </div>
    </div>
</div>
