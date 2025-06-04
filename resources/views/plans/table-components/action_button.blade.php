<div class="flex justify-center">
    <a href="javascript:void(0)" title="{{__('messages.common.edit') }}"
       class="btn px-2 text-primary-600 fs-3 ps-0 subscription-edit- px-4 py-2 rounded font-medium transition-colors" data-id="{{ $row->id }}" data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    @if(!$row->is_trial_plan == 1)
        <button type="button" title="{{__('messages.common.delete')}}" data-id="{{ $row->id }}"
                class="subscription-delete-btn px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0 {{ $ flex flex-wrap ->active_subscriptions_count > 0 ?"disabled' : '' }}"
                data-bs-toggle="tooltip">
            <i class="fa-solid fa-trash"></i>
        </button>
    @endif
</div>


