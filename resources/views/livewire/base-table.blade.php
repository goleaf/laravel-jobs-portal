<div>
    <div class="flex flex-col md:flex-row justify-between mb-4 space-y-4 md:space-y-0">
        <!-- Search Box -->
        @if($this->hasSearchableColumns())
        <div class="relative w-full md:w-1/3">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <x-icons.search class="w-5 h-5 text-gray-500" />
            </div>
            <input type="text" 
                wire:model.debounce.300ms="search" 
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5" 
                placeholder="{{ __('messages.table.search') }}">
            @if($search)
                <button wire:click="clearSearch" class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="w-5 h-5 text-gray-500 hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>
        @endif

        <!-- Per page and buttons -->
        <div class="flex flex-wrap items-center justify-end gap-2">
            @if($this->hasFilterableColumns())
                <button 
                    wire:click="toggleFilters"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <x-icons.filter class="w-5 h-5 mr-2 text-gray-500" />
                    {{ __('messages.common.filters') }}
                    @if(count($activeFilters))
                        <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                            {{ count($activeFilters) }}
                        </span>
                    @endif
                </button>
            @endif
            
            <div class="flex items-center space-x-2">
                <label for="perPage" class="text-sm font-medium text-gray-700">
                    {{ __('messages.table.per_page') }}:
                </label>
                <select id="perPage" 
                    wire:model="perPage"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 p-2.5">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
            </div>
            
            <!-- Slot for action buttons -->
            @if(isset($tableActions))
                <div class="flex items-center space-x-2">
                    {{ $tableActions }}
                </div>
            @endif
        </div>
    </div>

    <!-- Filters section -->
    @if($this->hasFilterableColumns() && $showFilters)
        <div class="mb-4 p-4 bg-gray-50 rounded-md shadow-sm">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-lg font-medium text-gray-900">{{ __('messages.common.filters') }}</h3>
                @if(count($activeFilters))
                    <button 
                        wire:click="clearFilters"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        {{ __('messages.common.clear_filters') }}
                    </button>
                @endif
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($filterableColumns as $column)
                    <div>
                        <label for="filter-{{ $column['field'] }}" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $column['label'] }}
                        </label>
                        
                        @if(isset($column['filter_type']) && $column['filter_type'] === 'select')
                            <select 
                                wire:model="activeFilters.{{ $column['field'] }}" 
                                id="filter-{{ $column['field'] }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-primary-500 focus:border-primary-500 block w-full p-2">
                                <option value="">{{ __('messages.common.select_all') }}</option>
                                @foreach($column['filter_options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif(isset($column['filter_type']) && $column['filter_type'] === 'date_range')
                            <div class="flex space-x-2">
                                <input 
                                    wire:model="activeFilters.{{ $column['field'] }}_from" 
                                    type="date" 
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-primary-500 focus:border-primary-500 block w-full p-2"
                                    placeholder="{{ __('messages.common.from_date') }}">
                                <input 
                                    wire:model="activeFilters.{{ $column['field'] }}_to" 
                                    type="date" 
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-primary-500 focus:border-primary-500 block w-full p-2"
                                    placeholder="{{ __('messages.common.to_date') }}">
                            </div>
                        @else
                            <input 
                                wire:model.debounce.300ms="activeFilters.{{ $column['field'] }}" 
                                type="text" 
                                id="filter-{{ $column['field'] }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-primary-500 focus:border-primary-500 block w-full p-2"
                                placeholder="{{ __('messages.common.search') }}...">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Active Filters -->
    @if(count($activeFilters))
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($activeFilters as $field => $value)
                @if(!empty($value))
                    @php
                        $column = collect($columns)->firstWhere('field', $field);
                        $label = $column ? $column['label'] : $field;
                        
                        if (isset($column['filter_options'][$value])) {
                            $displayValue = $column['filter_options'][$value];
                        } else {
                            $displayValue = $value;
                        }
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        {{ $label }}: {{ $displayValue }}
                        <button wire:click="removeFilter('{{ $field }}')" class="ml-1.5 inline-flex items-center justify-center h-5 w-5 rounded-full bg-gray-200 hover:bg-gray-300 focus:outline-none">
                            <span class="sr-only">{{ __('messages.common.remove') }}</span>
                            <svg class="h-3 w-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </span>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" class="px-6 py-3">
                            @if(isset($column['sortable']) && $column['sortable'])
                                <button wire:click="sortBy('{{ $column['field'] }}')" class="flex items-center">
                                    {{ $column['label'] }}
                                    
                                    @if($sortField === $column['field'])
                                        <span class="ml-1">
                                            @if($sortDirection === 'asc')
                                                <x-icons.chevron-up class="w-4 h-4" />
                                            @else
                                                <x-icons.chevron-down class="w-4 h-4" />
                                            @endif
                                        </span>
                                    @endif
                                </button>
                            @else
                                {{ $column['label'] }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(count($items) > 0)
                    @foreach($items as $item)
                        <tr class="bg-white border-b hover:bg-gray-50" wire:key="row-{{ $item->id }}">
                            @foreach($columns as $column)
                                <td class="px-6 py-4">
                                    @if(isset($column['component']))
                                        @include($column['component'], ['row' => $item])
                                    @elseif(isset($column['format']))
                                        {!! $column['format']($item) !!}
                                    @else
                                        {{ $item->{$column['field']} }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-6 py-4 text-center">
                            {{ __('messages.table.no_records') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div> 