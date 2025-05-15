<div class="flex flex-col">
    <label class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $filter['title'] }}
    </label>
    <div class="flex flex-col space-y-2">
        @foreach($filter['options'] as $value => $label)
            <label class="inline-flex items-center">
                <input 
                    type="checkbox" 
                    wire:model="filters.{{ $filter['key'] }}.{{ $value }}"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                >
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
            </label>
        @endforeach
    </div>
</div> 