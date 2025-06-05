@if($row->last_change)
<span class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">{{ $row->admin->full_name }}</span>
@else
<span class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ __('messages.common.not_available') }}</span>
@endif
