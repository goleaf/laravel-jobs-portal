@if(!empty($row->phone_code))
    <div class="badge bg-gray-100 -info">
        {{ $row->phone_code }}
    </div>
@else
    <div class="badge bg-gray-100 -info">
        {{ __('messages.n/a') }}
    </div>
@endif
