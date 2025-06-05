<div class="flex justify-start">
    <a href="{{ route('admin.edit', $row->id) }}" title="{{ __('messages.common.edit') }}"
       class="rounded-md transition" data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <button type="button" title="{{ __('messages.common.delete') }}" data-id="{{ $row->id }}"
            class="admins-delete-inline-flex items-center px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0" id="deleteAdmin" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
