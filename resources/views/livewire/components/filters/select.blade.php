<div class="flex flex-col">
    <label for="filter-{{ $filter['key'] }}" class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $filter['title'] }}
    </label>
    <select 
        id="filter-{{ $filter['key'] }}" 
        wire:model="filters.{{ $filter['key'] }}"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
    >
        @foreach($filter['options'] as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div> 