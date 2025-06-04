<div class="w-full" x-data="{ open: false }">
    <label for="{{ $filter['key'] }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $filter['label'] }}
    </label>
    <div class="relative">
        <button 
            type="button" 
            @click="open = !open" 
            class="mt-1 relative w-full bg-white border border-gray-300 border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        >
            <span class="block truncate">
                @if(!empty($filters[$filter['key']]['values']))
                    {{ count($filters[$filter['key']]['values']) }} {{ __('messages.common.selected') }}
                @else
                    {{ __('messages.common.select') }}...
                @endif
            </span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <x-icons.chevron-down />
            </span>
        </button>

        <div 
            x-show="open" 
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100" 
            x-transition:enter-start="transform opacity-0 scale-95" 
            x-transition:enter-end="transform opacity-100 scale-100" 
            x-transition:leave="transition ease-in duration-75" 
            x-transition:leave-start="transform opacity-100 scale-100" 
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        >
            <div class="sticky top-0 z-10 bg-white p-2 border-b">
                <div class="flex items-center space-x-2">
                    <input 
                        type="checkbox" 
                        id="{{ $filter['key'] }}_select_all"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        @click="
                            let allChecked = $event.target.checked;
                            document.querySelectorAll('[id^={{ $filter['key'] }}_option_]').forEach(checkbox => {
                                checkbox.checked = allChecked;
                                if (allChecked) {
                                    @this.set('filters.{{ $filter['key'] }}.values', Object.keys(@this.get('filters.{{ $filter['key'] }}.options')));
                                } else {
                                    @this.set('filters.{{ $filter['key'] }}.values', []);
                                }
                            });
                        "
                    >
                    <label for="{{ $filter['key'] }}_select_all" class="block text-sm font-medium text-gray-700">
                        {{ __('messages.common.select_all') }}
                    </label>
                </div>
            </div>
            
            @foreach($filter['options'] as $optionValue => $optionLabel)
                <div class="relative px-4 py-2 hover:bg-gray-100">
                    <label class="flex items-center space-x-3 w-full cursor-pointer">
                        <input 
                            type="checkbox" 
                            id="{{ $filter['key'] }}_option_{{ $optionValue }}"
                            value="{{ $optionValue }}"
                            wire:model.live="filters.{{ $filter['key'] }}.values"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <span class="block truncate">{{ $optionLabel }}</span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div> 