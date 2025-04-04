<div class="w-full">
    <label for="{{ $filter['key'] }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $filter['label'] }}
    </label>
    <input 
        type="date" 
        id="{{ $filter['key'] }}" 
        wire:model.live="filters.{{ $filter['key'] }}"
        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
</div>
