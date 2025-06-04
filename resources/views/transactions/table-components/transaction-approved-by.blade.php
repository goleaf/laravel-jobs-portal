
@if($row->admin)
<span class="badge bg-gray-100 -warning">{{$row->admin->full_name}}</span>
@else
    <span class="badge bg-secondary">{{__('messages.common.n/a')}}</span>
@endif
