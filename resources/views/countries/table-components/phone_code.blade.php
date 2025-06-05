@if(!empty($row->phone_code))
    <div class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 info">
        {{ $row->phone_code }}
    </div>
@else
    <div class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 info">
        {{ __('messages.n/a') }}
    </div>
@endif
