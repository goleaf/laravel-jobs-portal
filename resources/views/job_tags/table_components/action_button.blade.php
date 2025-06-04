<div class="flex justify-center">
    <a href="javascript:void(0)" 
       title="{{__('messages.common.edit') }}"
       class="job-tag-edit-btn px-2 text-primary-600 edit- px-4 py-2 rounded font-medium transition-colors"
       data-id="{{ $row->id }}" 
       data-bs-toggle="tooltip">
        <x-icons.edit class="w-5 h-5" />
    </a>
    <button type="button" 
            title="{{__('messages.common.delete')}}" 
            data-id="{{ $row->id }}"
            class="job-tag-delete- px-4 py-2 rounded font-medium transition-colors px-2 text-red-600" 
            id="deleteUser" 
            data-bs-toggle="tooltip">
        <x-icons.delete class="w-5 h-5" />
    </button>
</div>
