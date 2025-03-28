<div x-data="{ open: false }">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
    </label>
    
    <div class="relative">
        <button
            type="button"
            @click="open = !open"
            class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        >
            <span class="block truncate">
                @if(empty($activeFilters[$name] ?? []))
                    {{ __('Select options') }}
                @else
                    {{ count($activeFilters[$name] ?? []) }} {{ __('selected') }}
                @endif
            </span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </span>
        </button>

        <div 
            x-show="open" 
            @click.away="open = false"
            class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        >
            <div class="p-2 border-b">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        x-ref="searchInput"
                        x-init="$watch('open', value => { if (value) $nextTick(() => { $refs.searchInput.focus() }) })"
                        placeholder="{{ __('Search options') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>
            </div>
            
            <div class="p-2">
                @foreach($options as $value => $label)
                    <div class="flex items-center p-2 hover:bg-gray-100 rounded-md">
                        <input
                            type="checkbox"
                            id="filter-{{ $name }}-{{ $value }}"
                            value="{{ $value }}"
                            @if(in_array($value, $activeFilters[$name] ?? []))
                                checked
                            @endif
                            wire:model.live="activeFilters.{{ $name }}"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="filter-{{ $name }}-{{ $value }}" class="ml-3 block text-sm font-medium text-gray-700">
                            {{ $label }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div> 