<div class="flex justify-center">
    <a href="javascript:void(0)" title="{{ __('messages.common.edit') }}"
       class="rounded-md transition" data-id={{ $flex flex-wrap -mx-4->id }} data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <button type="button" title="{{ __('messages.common.delete') }}" data-id="{{ $flex flex-wrap -mx-4->id }}"
            class="functional-area-delete-inline-flex items-center px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0" id="deleteUser" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
