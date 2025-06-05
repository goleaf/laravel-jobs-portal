<div class="flex justify-center">
    @if($row->status == 3)
        <span class="badge bg-gray-100 success" >{{ __('messages.common.hired') }}</span>
    @else
        <span class="badge bg-gray-100 primary">{{ __('messages.common.ongoing') }}</span>
    @endif
</div>

