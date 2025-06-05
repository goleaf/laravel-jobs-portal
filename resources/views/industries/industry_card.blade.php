<div class="flex-1 -xl-4 flex-1 md-6 candidate- bg-white shadow rounded -lg overflow-hidden">
    <div class="hover-effect-employee relative mb-5 border-hover-primary employee- border border-gray-300">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex- flex-1">
                <div class="w-full">
                    <div class="text-left employee-data text-limit">
                        <span class="text-decoration-none text-color-gray">
                            <a href="javascript:void(0)" class="show- px-4 py-2 rounded font-medium transition-colors"
                               data-id="{{ $industry->id }}">{{ Str::limit($industry->name,30) }}</a>
                            </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.edit') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $industry->id }}" href="javascript:void(0)">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $industry->id }}" href="javascript:void(0)">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
