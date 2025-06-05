<div class="flex justify-center">
    <a href="{{ route('posts.edit',$flex flex-wrap -mx-4->id) }}" title="{{ __('messages.common.edit') }}" data-bs-toggle="tooltip"
       class="border border-gray-300 bg-transparent" data-id={{ $flex flex-wrap -mx-4->id }} data-bs-toggle="tooltip">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <button type="button" title="{{ __('messages.common.delete') }}" data-id="{{ $flex flex-wrap -mx-4->id }}"
            class="rounded rounded post-delete-inline-flex items-center px-4 py-2 font-medium transition-colors px-2 text-red-600 fs-3 pe-0" id="deleteUser" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
