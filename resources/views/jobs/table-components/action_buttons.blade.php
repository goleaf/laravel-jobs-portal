<div class="flex justify-center">
    <a href="{{ route('admin.job.edit', $row->id) }}" 
       title="{{ __('messages.common.edit') }}"
       class="inline-flex items-center justify-center p-2 mx-1 text-sm font-medium text-primary-700 bg-primary-100 rounded-full hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
        <x-icons.edit class="w-5 h-5" />
    </a>
    
    <button type="button" 
            title="{{ __('messages.common.delete') }}" 
            data-id="{{ $row->id }}"
            class="inline-flex items-center justify-center p-2 mx-1 text-sm font-medium text-red-700 bg-red-100 rounded-full hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
            id="deleteUser">
        <x-icons.delete class="w-5 h-5" />
    </button>
</div>
