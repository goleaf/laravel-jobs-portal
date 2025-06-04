<div class="flex justify-center">
    <a title="{{ __('messages.common.show') }}
            " class="btn px-2 inquiry-show-btn text-primary-600 fs-3 ps-0 action- px-4 py-2 rounded font-medium transition-colors" data-id="{{ $row->id }}" data-bs-toggle="tooltip">
        <i class="fas fa-eye"></i>
    </a>

    <a title="{{ __('messages.common.delete') }}" data-id="{{ $row->id }}"
       class="inquiry-delete-btn px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 ps-0" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </a>
</div>
