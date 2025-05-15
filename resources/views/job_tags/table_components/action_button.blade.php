<div class="flex justify-center">
    <a href="javascript:void(0)" 
       title="{{__('messages.common.edit') }}"
       class="job-tag-edit-btn px-2 text-primary edit-btn"
       data-id="{{ $row->id }}" 
       data-bs-toggle="tooltip">
        <x-icons.edit class="w-5 h-5" />
    </a>
    <button type="button" 
            title="{{__('messages.common.delete')}}" 
            data-id="{{ $row->id }}"
            class="job-tag-delete-btn px-2 text-danger" 
            id="deleteUser" 
            data-bs-toggle="tooltip">
        <x-icons.delete class="w-5 h-5" />
    </button>
</div>
