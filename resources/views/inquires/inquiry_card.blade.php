<div class="flex-1 -xl-4 flex-1 md-6 candidate- bg-white shadow rounded -lg overflow-hidden">
    <div class="hover-effect-border relative mb-5 border-hover-primary employee- border border-gray-300">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex- flex-1">
                <div class="mb-auto w-full employee-data mt-4">
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.inquiry.name') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ $inquiry->name }}</label>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.inquiry.subject') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ $inquiry->subject }}</label>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.inquiry.inquiry_date') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ \Carbon\Carbon::parse($inquiry->created_at)->translatedFormat('d M Y') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.view') }}" class="border border-gray-300 bg-transparent" href="{{ route('admin.', $inquiry->id) }}">
                <i class="fa fa-eye"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="border border-gray-300 bg-transparent"
               data-id="{{ $inquiry->id }}" href="javascript:void(0)">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
