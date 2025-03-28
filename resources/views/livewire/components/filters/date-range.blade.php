<div class="flex flex-col">
    <label class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $filter['title'] }}
    </label>
    <div class="flex space-x-2">
        <div class="relative">
            <input 
                type="date" 
                id="filter-{{ $filter['key'] }}-from" 
                wire:model="filters.{{ $filter['key'] }}.from"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="{{ __('messages.common.from_date') }}"
            >
        </div>
        <div class="relative">
            <input 
                type="date" 
                id="filter-{{ $filter['key'] }}-to" 
                wire:model="filters.{{ $filter['key'] }}.to"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="{{ __('messages.common.to_date') }}"
            >
        </div>
    </div>
</div> 