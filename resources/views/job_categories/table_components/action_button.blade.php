<div class="flex space-x-2 justify-center">
    <button
        title="{{ __('messages.common.edit') }}"
        class="edit-inline-flex items-center px-4 py-2 rounded font-medium transition-colors px-2 text-indigo-600-600 fs-3"
        data-bs-toggle="modal" data-bs-target="#editJobCategoryModal" data-id="{{ $row->id }}">
        <x-icons.edit class="h-4 w-4" />
    </button>
    
    <button type="button" data-id="{{ $row->id }}"
        class="rounded-md transition" data-turbo="false">
        <x-icons.trash class="h-4 w-4" />
    </button>
</div>
