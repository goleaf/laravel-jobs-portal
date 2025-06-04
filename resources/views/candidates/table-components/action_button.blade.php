<div class="flex justify-center">
    <a href="{{route('admin.candidates.edit', $row->id)}}" title="{{__('messages.common.edit') }}"
       class="btn px-2 text-primary-600 fs-3 ps-0 candidates-edit- px-4 py-2 rounded font-medium transition-colors" data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <button type="button" title="{{__('messages.common.delete')}}" data-id="{{ $row->id }}"
            class="candidates-delete-btn px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0" id="deleteUser" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
