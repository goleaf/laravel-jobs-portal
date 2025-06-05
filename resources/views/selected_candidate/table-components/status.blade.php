<div class="flex justify-center">
    @if($row->status == 3)
        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 success" >{{ __('messages.common.hired') }}</span>
    @else
        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 primary">{{ __('messages.common.ongoing') }}</span>
    @endif
</div>

