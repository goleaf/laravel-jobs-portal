<div class="flex justify-content-start">
    <a href="{{route('admin.edit', $row->id)}}" title="{{__('messages.common.edit') }}"
       class="btn px-2 text-primary-600 fs-3 ps-0 candidates-edit- px-4 py-2 rounded font-medium transition-colors" data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <button type="button" title="{{__('messages.common.delete')}}" data-id="{{ $row->id }}"
            class="admins-delete-btn px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0" id="deleteAdmin" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
