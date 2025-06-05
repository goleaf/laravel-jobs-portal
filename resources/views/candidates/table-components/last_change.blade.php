
@if($row->last_change)
    <span class="badge bg-yellow-100 text-yellow-800">{{ $row->admin->full_name }}</span>
@else
    <span class="badge bg-gray-600 text-white">{{ __('messages.common.n/a') }}</span>
@endif
