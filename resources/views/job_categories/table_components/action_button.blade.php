<div class="flex space-x-2 justify-center">
    <button
        title="{{ __('messages.common.edit') }}"
        class="edit-btn btn px-2 text-primary fs-3"
        data-bs-toggle="modal" data-bs-target="#editJobCategoryModal" data-id="{{ $row->id }}">
        <x-icons.edit class="h-4 w-4" />
    </button>
    
    <button type="button" data-id="{{ $row->id }}"
        class="btn px-2 text-danger fs-3 job-category-delete-btn" data-turbo="false">
        <x-icons.trash class="h-4 w-4" />
    </button>
</div>
