<div class="flex items-center space-x-2">
    <button
        onclick="Livewire.emit('editJobType', {{ $row->id }})"
        class="p-1 text-blue-600 hover:bg-blue-100 rounded-full"
        title="{{ __('messages.common.edit') }}">
        <x-icons.edit class="w-5 h-5" />
    </button>
    
    <button
        onclick="confirmDelete({{ $row->id }})"
        class="p-1 text-red-600 hover:bg-red-100 rounded-full"
        title="{{ __('messages.common.delete') }}">
        <x-icons.delete class="w-5 h-5" />
    </button>
</div>

@once
<script>
    function confirmDelete(id) {
        if (confirm("{{ __('messages.common.delete_confirm') }}")) {
            Livewire.emit('deleteJobType', id);
        }
    }
</script>
@endonce 