<div class="flex justify-center">
    <a href="javascript:void(0)" title="{{ __('messages.common.edit') }}"
       class="border border-gray-300 bg-transparent" data-id="{{ $flex flex-wrap -mx-4->id }}" data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    @if(!$flex flex-wrap -mx-4->is_trial_plan == 1)
        <button type="button" title="{{ __('messages.common.delete') }}" data-id="{{ $flex flex-wrap -mx-4->id }}"
                class="rounded flex-wrap rounded subscription-delete-inline-flex items-center px-4 py-2 font-medium transition-colors px-2 text-red-600 fs-3 pe-0 {{ $ flex ->active_subscriptions_count > 0 ?"disabled' : '' }}"
                data-bs-toggle="tooltip">
            <i class="fa-solid fa-trash"></i>
        </button>
    @endif
</div>


