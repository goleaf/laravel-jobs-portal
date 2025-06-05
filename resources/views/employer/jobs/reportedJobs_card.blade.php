<div class="flex-1 -xl-4 flex-1 md-6 candidate- bg-white shadow rounded -lg overflow-hidden">
    <div class="hover-effect-employee relative mb-5 border-hover-primary employee- border border-gray-300">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex- flex-1">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $reportedJob->$job->$company->company_url }}"
                         class="img-responsive users-avatar-img employee-img mr-2">
                </div>
                <div class="mb-auto w-full employee-data">
                    <div class="flex justify-center items-center w-full">
                        <div>
                            <label class="text-decoration-none text-color-gray">
                                <a href=" {{ route('front.') }}/{{ $reportedJob->$job->job_id }}"
                                   class="text-decoration-none text-color-gray"
                                   target="_blank"> {{ $reportedJob->$job->job_title }}</a>
                            </label>
                        </div>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.company.reported_by') }} :</label>
                        <label class="text-decoration-none text-color-gray">
                            <a href="{{ url('candidate-details') }}/{{ $reportedJob->$user->$candidate->unique_id }}"
                               class="text-decoration-none text-color-gray"
                               target="_blank">{{ $reportedJob->$user->full_name }}</a>
                        </label>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.company.reported_on') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ \Carbon\Carbon::parse($reportedJob->created_at)->formatLocalized('%d %b, %Y') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <button title="{{ __('messages.common.view') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $reportedJob->id }}">
                <i class="fas fa-eye"></i>
            </button>
            <a title="{{ __('messages.common.delete') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $reportedJob->id }}" href="javascript:void(0)">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
