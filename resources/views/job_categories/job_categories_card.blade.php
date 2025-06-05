<div class="flex-1 -xl-4 flex-1 md-6 candidate- bg-white shadow rounded -lg overflow-hidden">
    <div class="hover-effect-employee relative mb-5 border-hover-primary employee- border border-gray-300">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex- flex-1">
                <div class="w-full">
                    <div class="pl-0 mb-2 employee-avatar mx-auto">
                        <img src="{{ $jobCategory->image_url }}"
                             class="img-responsive users-avatar-img employee-img mr-2">
                    </div>
                    <div class="text-left employee-data text-limit mx-auto flex justify-center">
                        <span class="text-decoration-none text-color-gray items-center text-truncate">
                            <a href="#" class="show- px-4 py-2 rounded font-medium transition-colors"
                               data-id="{{ $jobCategory->id }}">{{ Str::limit($jobCategory->name,30) }}</a>
                            </span>
                    </div>
                    <div class="text-left employee-date mt-2">
                        <label class="custom-switch pl-0 job-cat-switch flex justify-center">
                            <input type="checkbox" name="show_to_staff" class="custom-switch-input isFeatured"
                                   data-id="{{ $jobCategory->id }}" {{ $jobCategory->is_featured === false ? '' : 'checked' }}>
                            <span class="custom-switch-indicator"></span>
                            <span class="employee-label ml-1 w-auto">{{ __('messages.job_category.is_featured') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.edit') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $jobCategory->id }}" href="#">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $jobCategory->id }}" href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
