<div class="overflow-hidden shadow rounded bg-white flex-1 px-4 -xl-4 flex-1 md-6 candidate- -lg">
    <div class="border mb-5 border hover-effect-employee relative -hover-primary employee-">
        <div class="employee-listing-details">
            <div class="flex-1 px-4 flex employee-listing-description items-center justify-center flex-">
                <div class="w-full">
                    <div class="text-left employee-data text-limit">
                        <span class="text-decoration-none text-color-gray">
                            <a href="javascript:void(0)" class="rounded show- px-4 py-2 font-medium transition-colors"
                               data-id="{{ $postCategory->id }}">{{ Str::limit($postCategory->name,30) }}</a>
                            </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="rounded employee-action- px-4 py-2 font-medium transition-colors">
            <a title="{{ __('messages.common.edit') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $postCategory->id }}" href="javascript:void(0)">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $postCategory->id }}" href="javascript:void(0)">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
