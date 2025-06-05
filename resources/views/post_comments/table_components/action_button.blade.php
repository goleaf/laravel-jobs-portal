<div class="flex justify-center">
    <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-2 text-primary-600 fs-3 py-2 post-comment-show- px-4 py-2 rounded font-medium transition-colors" data-id="{{ $row->id }}" data-bs-toggle="tooltip"
       title="{{ __('messages.common.show') }}" data-turbo="false">
        <i class="fa-solid fa-eye fs-4"></i>
    </a>
    <a title="<?php echo __('messages.common.delete') ?>" data-id="{{ $row->id }}"
       class="post-comment-delete-btn px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 py-2" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash fs-4"></i>
    </a>
</div>
