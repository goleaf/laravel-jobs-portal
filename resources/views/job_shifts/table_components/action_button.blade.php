<div class="flex justify-center">
    <a href="javascript:void(0)" title="{{ __('messages.common.edit') }}"
       class="rounded-md transition" data-bs-toggle="tooltip"
       data-id={{ $row->id }} data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <button type="button" title="{{ __('messages.common.delete') }}" data-id="{{ $row->id }}"
            class="job-shift-delete-inline-flex items-center px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0" id="deleteUser" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
