<div class="fixed inset-0 z-50 overflow-y-auto fade" id="reportToCandidateModal" tabindex="-1" aria-labelledby="reportToCandidate"
     aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.job.add_note')  }}</h5>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @formOpen(['id' => 'reportToCandidate', 'name' => 'frm'])
                @csrf
                <div class="px-6 py-4">
                    {{ Form::hidden('userId', (getLoggedInUserId() !== null) ? getLoggedInUserId() : null)  }}
                    {{ Form::hidden('candidateId', $candidateDetails->id)  }}
                    <div class="flex-1 -md-12 mb-4">
                        <div class="form-group">
                            {{ Form::label('noteForReportToCandidate', __('web.web_contact.your_message').':', ['class' => 'fs-16 text-secondary mb-2'])  }}
                            <span class="text-primary-600">*</span>
                            {{ Form::textarea('note', null, [
                                'class' => 'form-control fs-14 text-gray br-10',
                                'rows' => '5',
                                'id' => 'noteForReportToCandidate',
                                'required'
                            ])  }}
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                    {{ Form::button(__('messages.common.close'), [
                        'type' => 'button',
                        'class' => 'btn btn-secondary',
                        'data-bs-dismiss' => 'modal'
                    ])  }}
                    {{ Form::button(__('messages.common.report'), [
                        'type' => 'submit',
                        'class' => 'btn btn-primary btn-primary-register',
                        'id' => 'btnReportCandidate',
                        'data-bs-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')
                    ])  }}
                </div>
            @formClose()
        </div>
    </div>
</div>
