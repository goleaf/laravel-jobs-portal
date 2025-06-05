<div class="flex justify-center">
    <a href="{{ route('company.edit', $row->id) }}" title="{{ __('messages.common.edit') }}"
       class="px-4 py-2 rounded font-medium transition-colors px-2 text-indigo-600-600 fs-3 ps-0" data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <button type="button" title="{{ __('messages.common.delete') }}" data-id="{{ $row->id }}"
            class="employer-delete-inline-flex items-center px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0" id="deleteUser" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>

