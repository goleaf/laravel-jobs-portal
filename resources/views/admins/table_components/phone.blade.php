<div class="flex items-center">
    <div class="flex flex-col">
        @if($row->phone)
            <span class="fs-6">{{ $row->phone }}</span>
        @else
            <span class="fs-6">{{__('messages.common.n/a')}}</span>
        @endif
    </div>
</div>
