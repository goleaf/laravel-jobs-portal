<div class="w-full">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $filter['label'] }}
    </label>
    <div class="flex space-x-2">
        <div class="flex-1">
            <input 
                type="date" 
                wire:model.live="filters.{{ $filter['key'] }}_from" 
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                placeholder="{{ __('messages.common.from_date') }}">
        </div>
        <div class="flex-1">
            <input 
                type="date" 
                wire:model.live="filters.{{ $filter['key'] }}_to" 
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                placeholder="{{ __('messages.common.to_date') }}">
        </div>
    </div>
</div> 