<div class="overflow-hidden shadow rounded bg-white flex-1 px-4 -xl-4 flex-1 md-6 candidate- -lg">
    <div class="border mb-5 border border hover-effect- relative -hover-primary employee- custom-h-auto">
        <div class="employee-listing-details">
            <div class="flex-1 px-4 flex employee-listing-description items-center justify-center flex-">
                <div class="w-full">
                    <div class="text-left employee-data text-limit">
                        <span class="text-decoration-none text-flex-1 px-4or-gray">{{ Str::limit($degreeLevel->name,30) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="transition duration-150 ease-in-out flex-1">

            <a title="{{ __('messages.common.edit') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $degreeLevel->id }}" href="#">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $degreeLevel->id }}" href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
