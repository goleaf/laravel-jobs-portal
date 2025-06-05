
@if($row->admin)
<span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 warning">{{ $row->admin->full_name }}</span>
@else
    <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-600">{{ __('messages.common.n/a') }}</span>
@endif
