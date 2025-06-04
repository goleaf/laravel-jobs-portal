<div class="flex justify-center">
    <a title="{{ __('messages.common.show') }}
            " class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-2 inquiry-show-btn text-primary-600 fs-3 ps-0 action- px-4 py-2 rounded font-medium transition-colors" data-id="{{ $row->id }}" data-bs-toggle="tooltip">
        <i class="fas fa-eye"></i>
    </a>

    <a title="{{ __('messages.common.delete') }}" data-id="{{ $row->id }}"
       class="inquiry-delete-btn px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 ps-0" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </a>
</div>
