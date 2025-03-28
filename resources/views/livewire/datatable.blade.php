<div class="overflow-x-auto">
    <!-- Search and Filter Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
        <!-- Left side: Search -->
        <div class="w-full md:w-1/3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <x-icons.search class="w-5 h-5 text-gray-400" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.{{ $debounce }}ms="search"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                    placeholder="{{ __('Search') }}"
                >
            </div>
        </div>

        <!-- Right side: Filters and per page -->
        <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Filters dropdown if any -->
            @if(count($filters) > 0)
                <div class="relative w-full md:w-auto" x-data="{ open: false }">
                    <button 
                        @click="open = !open" 
                        class="flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-0 focus:ring-blue-500 w-full md:w-auto"
                    >
                        <x-icons.filter class="w-4 h-4 mr-2" />
                        {{ __('Filters') }}
                        @if(count($activeFilters) > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ count($activeFilters) }}
                            </span>
                        @endif
                    </button>
                    
                    <div
                        class="absolute right-0 z-10 mt-2 bg-white rounded-md shadow-lg w-72 md:w-96"
                        x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                    >
                        <div class="p-3">
                            <div class="flex justify-between items-center pb-2 border-b">
                                <h3 class="text-sm font-medium text-gray-900">{{ __('Filters') }}</h3>
                                @if(count($activeFilters) > 0)
                                    <button
                                        wire:click="clearAllFilters"
                                        type="button"
                                        class="text-xs text-blue-600 hover:text-blue-800"
                                    >
                                        {{ __('Clear all') }}
                                    </button>
                                @endif
                            </div>
                            
                            <div class="mt-2 max-h-80 overflow-y-auto">
                                @foreach($filters as $filter)
                                    <div class="py-2">
                                        {!! $filter->render() !!}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Per page selector -->
            <div class="flex items-center w-full md:w-auto">
                <label for="perPage" class="mr-2 text-sm font-medium text-gray-700">{{ __('Show') }}:</label>
                <select
                    id="perPage"
                    wire:model.live="perPage"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2"
                >
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Actions Bar -->
    @if(count($actions) > 0)
        <div class="mb-4 flex gap-2">
            @foreach($actions as $action)
                {!! $action->render() !!}
            @endforeach
        </div>
    @endif

    <!-- Table -->
    <div class="relative overflow-x-auto rounded-lg shadow">
        <table class="{{ $tableClass }}">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if($column->sortable)
                                <button
                                    wire:click="sort('{{ $column->field }}')"
                                    class="flex items-center group"
                                >
                                    {{ $column->label }}
                                    
                                    @if($sortField === $column->field)
                                        <span class="ml-1">
                                            @if($sortDirection === 'asc')
                                                <x-icons.sort-asc class="w-4 h-4 text-blue-600" />
                                            @else
                                                <x-icons.sort-desc class="w-4 h-4 text-blue-600" />
                                            @endif
                                        </span>
                                    @else
                                        <span class="ml-1 opacity-0 group-hover:opacity-100">
                                            <x-icons.sort class="w-4 h-4 text-gray-400" />
                                        </span>
                                    @endif
                                </button>
                            @else
                                {{ $column->label }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data as $row)
                    <tr class="hover:bg-gray-50">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {!! $column->render($row) !!}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-6 py-4 text-center text-gray-500">
                            {{ __('No records found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $data->links() }}
    </div>
</div> 