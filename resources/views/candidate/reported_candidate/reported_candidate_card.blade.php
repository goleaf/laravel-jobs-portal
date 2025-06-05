<div class="overflow-hidden shadow rounded bg-white flex-1 px-4 -xl-4 flex-1 md-6 candidate- -lg">
    <div class="border mb-5 border hover-effect-employee relative -hover-primary employee-">
        <div class="employee-listing-details">
            <div class="flex-1 px-4 flex employee-listing-description items-center justify-center flex-">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $reportedCandidate->$candidate->candidate_url }}"
                         class="mr-2 img-responsive users-avatar-img employee-img">
                </div>
                <div class="mb-auto w-full employee-data">
                    <div class="flex justify-center items-center w-full">
                        <div>
                            <span class="text-decoration-none text-flex-1 px-4or-gray">{{ $reportedCandidate->$candidate->$user->first_name }}</span>
                        </div>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.company.reported_by') }} :</label>
                        <label class="text-decoration-none text-flex-1 px-4or-gray">{{ $reportedCandidate->$user->first_name }}</label>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.company.reported_on') }} :</label>
                        <label class="text-decoration-none text-flex-1 px-4or-gray">{{ \Carbon\Carbon::parse($reportedCandidate->created_at)->formatLocalized('%d %b, %Y') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="transition duration-150 ease-in-out flex-1">
            <a title="{{ __('messages.common.view') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $reportedCandidate->id }}" href="#">
                <i class="fas fa-eye"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $reportedCandidate->id }}" href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
