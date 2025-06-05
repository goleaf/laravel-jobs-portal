<div class="flex-1 -xl-4 flex-1 md-6 candidate- bg-white shadow rounded -lg overflow-hidden">
    <div class="hover-effect-employee relative mb-5 border-hover-primary employee- border border-gray-300">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex- flex-1">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $reportedEmployee->$company->company_url }}"
                         class="img-responsive users-avatar-img employee-img mr-2">
                </div>
                <div class="mb-auto w-full employee-data">
                    <div class="flex justify-center items-center w-full">
                        <div>
                            <label class="text-decoration-none text-color-gray">{{ $reportedEmployee->$company->$user->first_name }}</label>
                        </div>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.company.reported_by') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ $reportedEmployee->$user->first_name }}</label>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.company.reported_on') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ \Carbon\Carbon::parse($reportedEmployee->created_at)->formatLocalized('%d %b, %Y') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.view') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $reportedEmployee->id }}" href="#">
                <i class="fas fa-eye"></i>
            </a>
            <a title="<?php echo __('messages.common.delete') ?>" class="border border-gray-300 bg-transparent"
               data-id="{{ $reportedEmployee->id }}" href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
