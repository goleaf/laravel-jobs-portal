<div class="flex justify-center">
    <button type="button" title="{{ __('messages.common.delete') }}" data-id="{{ $$row->id  }}"
            class="subscriber-delete-btn px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0 {{ $ flex flex-wrap ->active_subscriptions_count > 0 ?"disabled' : ''  }}"
            data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
