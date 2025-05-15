<div class="flex justify-center space-x-2">
    <a href="javascript:void(0)" 
       title="{{ __('messages.common.edit') }}"
       class="skill-edit-btn text-primary" 
       data-id="{{ $row->id }}" 
       data-bs-toggle="tooltip">
        <x-icons.edit />
    </a>
    <a href="javascript:void(0)" 
       title="{{ __('messages.common.view') }}"
       class="skill-show-btn text-info" 
       data-id="{{ $row->id }}" 
       data-bs-toggle="tooltip">
        <x-icons.view />
    </a>
    <button type="button" 
            title="{{ __('messages.common.delete') }}" 
            data-id="{{ $row->id }}"
            class="skill-delete-btn text-danger" 
            data-bs-toggle="tooltip">
        <x-icons.delete />
    </button>
</div> 