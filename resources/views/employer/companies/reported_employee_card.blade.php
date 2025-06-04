<div class="col-xl-4 flex-1 -md-6 candidate- bg-white shadow rounded-lg overflow-hidden">
    <div class="hover-effect-employee position-relative mb-5 border-hover-primary employee-border">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex-column">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $reportedEmployee->company->company_url }}"
                         class="img-responsive users-avatar-img employee-img mr-2">
                </div>
                <div class="mb-auto w-full employee-data">
                    <div class="flex justify-center items-center w-full">
                        <div>
                            <label class="text-decoration-none text-color-gray">{{ $reportedEmployee->company->user->first_name }}</label>
                        </div>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.company.reported_by') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ $reportedEmployee->user->first_name }}</label>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.company.reported_on') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ \Carbon\Carbon::parse($reportedEmployee->created_at)->formatLocalized('%d %b, %Y') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.view') }}" class="btn bg-blue-500 text-white hover:bg-blue-600 action- px-4 py-2 rounded font-medium transition-colors view-note"
               data-id="{{$reportedEmployee->id}}" href="#">
                <i class="fas fa-eye"></i>
            </a>
            <a title="<?php echo __('messages.common.delete') ?>" class="btn bg-red-600 text-white hover:bg-red-700 action-btn delete- px-4 py-2 rounded font-medium transition-colors"
               data-id="{{$reportedEmployee->id}}" href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
