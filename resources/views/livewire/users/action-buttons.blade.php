<div class="flex space-x-2 justify-center">
    <a href="{{ route('users.edit', $row->id) }}" class="text-blue-500 hover:text-blue-700">
        <x-icons.edit class="w-5 h-5" />
    </a>
    
    <button 
        wire:click="confirmDelete({{ $row->id }})" 
        class="text-red-500 hover:text-red-700"
    >
        <x-icons.trash class="w-5 h-5" />
    </button>
    
    <a href="{{ route('users.show', $row->id) }}" class="text-gray-500 hover:text-gray-700">
        <x-icons.eye class="w-5 h-5" />
    </a>
</div> 