<div class="flex justify-center">
    <button type="button" 
            class="inline-flex items-center justify-center p-2 mx-1 text-sm font-medium text-primary-700 bg-primary-100 rounded-full hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            title="{{ __('messages.common.edit') }}"
            data-id="{{ $row->id }}" 
            id="editLanguage">
        <x-icons.edit class="w-5 h-5" />
    </button>
    
    @if(!$row->is_default)
        <button type="button" 
                class="inline-flex items-center justify-center p-2 mx-1 text-sm font-medium text-red-700 bg-red-100 rounded-full hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                title="{{ __('messages.common.delete') }}"
                data-id="{{ $row->id }}" 
                id="deleteLanguage">
            <x-icons.delete class="w-5 h-5" />
        </button>
    @endif
</div> 