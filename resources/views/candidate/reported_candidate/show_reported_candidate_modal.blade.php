<div class="fixed inset-0 z-50 overflow-y-auto fade" tabindex="-1" role="dialog" id="showModal">
    <div class="flex items-center justify-center min-h-screen px-4" role="document">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="modal-title">
                    <th scope="col">{{ __('messages.candidate.reported_candidate_detail') }}</th>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="px-6 py-4">
                <div class="flex flex-wrap details-page">
                    <div class="form-group flex-1 sm-12">
                        <div class="employee-listing-details">
                            <div class="flex employee-listing-description items-center justify-center flex-col">
                                <div class="pl-0 mb-2 employee-avatar">
                                    <span id="showImage"></span>
                                </div>
                                <div class="mb-auto w-full employee-data">
                                    <div class="flex justify-center items-center w-full">
                                        <div>
                                            <label class="text-decoration-none text-color-gray" id="showName"></label>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <label>{{ __('messages.company.reported_by') }} :</label>
                                        <span class="text-decoration-none text-color-gray" id="showReportedBy"></span>
                                    </div>
                                    <div class="text-center">
                                        <label>{{ __('messages.company.reported_on') }} :</label>
                                        <span class="text-decoration-none text-color-gray" id="showReportedOn"></span>
                                    </div>
                                    <div class="text-center">
                                        <div class="reported-note">
                                            <label>{{ __('messages.applied_job.notes') }} :</label>
                                            <span id="showNote"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
