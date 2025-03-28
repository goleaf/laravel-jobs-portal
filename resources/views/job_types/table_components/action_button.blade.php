<div class="flex space-x-2 justify-center">
    <button 
        type="button" 
        class="inline-flex items-center px-2 py-1 bg-blue-500 text-white text-xs font-medium rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
        title="{{ __('messages.common.edit') }}"
        x-data
        @click="window.livewire.dispatch('editJobType', { id: '{{ $row->id }}' })">
        <x-icons.edit class="h-4 w-4" />
    </button>
    
    <button 
        type="button" 
        class="inline-flex items-center px-2 py-1 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        title="{{ __('messages.common.delete') }}"
        x-data
        @click="if (confirm('{{ __('messages.flash.delete_warning') }}')) { window.livewire.dispatch('deleteJobType', { id: '{{ $row->id }}' }) }">
        <x-icons.delete class="h-4 w-4" />
    </button>
</div>
