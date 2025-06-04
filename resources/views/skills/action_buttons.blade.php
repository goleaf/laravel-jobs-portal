<div class="flex justify-center space-x-2">
    <a href="javascript:void(0)" 
       title="{{ __('messages.common.edit') }}"
       class="skill-edit- px-4 py-2 rounded font-medium transition-colors text-primary-600" 
       data-id="{{ $row->id }}" 
       data-bs-toggle="tooltip">
        <x-icons.edit />
    </a>
    <a href="javascript:void(0)" 
       title="{{ __('messages.common.view') }}"
       class="skill-show- px-4 py-2 rounded font-medium transition-colors text-info" 
       data-id="{{ $row->id }}" 
       data-bs-toggle="tooltip">
        <x-icons.view />
    </a>
    <button type="button" 
            title="{{ __('messages.common.delete') }}" 
            data-id="{{ $row->id }}"
            class="skill-delete- px-4 py-2 rounded font-medium transition-colors text-red-600" 
            data-bs-toggle="tooltip">
        <x-icons.delete />
    </button>
</div> 