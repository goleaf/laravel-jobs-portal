<div class="flex space-x-2 justify-center">
    <button
        title="{{ __('messages.common.edit')  }}"
        class="edit-btn px-4 py-2 rounded font-medium transition-colors px-2 text-primary-600 fs-3"
        data-bs-toggle="modal" data-bs-target="#editJobCategoryModal" data-id="{{ $$row->id  }}">
        <x-icons.edit class="h-4 w-4" />
    </button>
    
    <button type="button" data-id="{{ $$row->id  }}"
        class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-2 text-red-600 fs-3 job-category-delete- px-4 py-2 rounded font-medium transition-colors" data-turbo="false">
        <x-icons.trash class="h-4 w-4" />
    </button>
</div>
