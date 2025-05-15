<div
    x-data="{
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        }
    }"
    @click.away="close"
    class="relative inline-block"
>
    <button
        @click="toggle"
        type="button"
        class="flex items-center text-gray-500 hover:text-gray-700 focus:outline-none"
        aria-expanded="true"
        aria-haspopup="true"
    >
        <span class="sr-only">{{ __('open_filter_menu') }}</span>
        <x-icons.filter class="w-5 h-5" />
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="filter-menu-button"
        tabindex="-1"
    >
        @if($slot->isNotEmpty())
            {{ $slot }}
        @else
            <div class="p-3">
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700">{{ __('filter_by') }}</label>
                    <input
                        type="text"
                        placeholder="{{ __('enter_value') }}"
                        wire:keydown.enter="applyFilter('{{ $column }}',$event.target.value)"
                        @keydown.enter="close"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                </div>
                <div class="flex justify-between">
                    <button
                        type="button"
                        @click="close"
                        class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        {{ __('cancel') }}
                    </button>
                    <button
                        type="button"
                        onclick="this.previousElementSibling.previousElementSibling.dispatchEvent(new KeyboardEvent('keydown', {'key': 'Enter'}))"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-2 py-1 text-xs font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        {{ __('apply') }}
                    </button>
                </div>
            </div>
        @endif
    </div>
</div> 