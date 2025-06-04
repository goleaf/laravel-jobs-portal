<div class="modal fade" tabindex="-1" role="dialog" id="showModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <th scope="col">{{ __('messages.company.reported_employer_detail') }}</th>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="modal-body">
                <div class="flex flex-wrap details-page">
                    <div class="form-group flex-1 -sm-12">
                        <div class="employee-listing-details">
                            <div class="flex employee-listing-description items-center justify-center flex-column">
                                <div class="pl-0 mb-2 employee-avatar">
                                    <span id="showImage"></span>
                                </div>
                                <div class="mb-auto w-full employee-data">
                                    <div class="flex justify-center items-center w-full">
                                        <div>
                                            <span class="text-decoration-none text-color-gray" id="showName"></span>
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
                                            <label>{{ __('messages.company.notes') }} :</label>
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
