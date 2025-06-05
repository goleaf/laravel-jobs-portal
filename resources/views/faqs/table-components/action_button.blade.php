<div class="flex justify-center">
    <a title="{{ __('messages.common.edit') }}" class="rounded-md transition" data-id="{{ $row->id }}" data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <a title="{{ __('messages.common.delete') }}" data-id="{{ $row->id }}"
       class="faqs-delete-inline-flex items-center px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 ps-0" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </a>
</div>
