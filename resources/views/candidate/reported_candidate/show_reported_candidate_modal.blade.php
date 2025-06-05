<div class="fixed inset-0 z-50 overflow-y-auto fade" tabindex="-1" role="dialog" id="showModal">
    <div class="flex items-center justify-center min-h-screen px-4" role="document">
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border px-6 py-4 -b -gray-200">
                <h5 class="fixed inset-0 z-50 overflow-y-auto -title">
                    <th scope="flex-1 px-4">{{ __('messages.candidate.reported_candidate_detail') }}</th>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="px-6 py-4">
                <div class="flex-wrap flex details-page">
                    <div class="mb-4 flex-1 sm-12">
                        <div class="employee-listing-details">
                            <div class="flex-1 px-4 flex employee-listing-description items-center justify-center flex-">
                                <div class="pl-0 mb-2 employee-avatar">
                                    <span id="showImage"></span>
                                </div>
                                <div class="mb-auto w-full employee-data">
                                    <div class="flex justify-center items-center w-full">
                                        <div>
                                            <label class="text-decoration-none text-flex-1 px-4or-gray" id="showName"></label>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <label>{{ __('messages.company.reported_by') }} :</label>
                                        <span class="text-decoration-none text-flex-1 px-4or-gray" id="showReportedBy"></span>
                                    </div>
                                    <div class="text-center">
                                        <label>{{ __('messages.company.reported_on') }} :</label>
                                        <span class="text-decoration-none text-flex-1 px-4or-gray" id="showReportedOn"></span>
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
