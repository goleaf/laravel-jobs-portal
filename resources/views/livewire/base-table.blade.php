<div>
    <div class="mb-5 flex flex-col md:flex-row justify-between items-center">
        <!-- Search Box -->
        <div class="w-full md:w-1/3 mb-4 md:mb-0">
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <x-icons.search />
                </div>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="search" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                    placeholder="{{ __('messages.common.search') }}"
                >
            </div>
        </div>

        <!-- Right side controls -->
        <div class="flex items-center space-x-4">
            <!-- Per Page Selector -->
            <div class="flex items-center">
                <label for="perPage" class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-300 hidden md:inline">{{ __('messages.common.per_page') }}:</label>
                <select 
                    wire:model.live="perPage" 
                    id="perPage" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                >
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Button (if filters are available) -->
            @if(count($filters) > 0 && $showFilterOnHeader)
                <button 
                    wire:click="$toggle('showFilters')" 
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                >
                    <x-icons.filter />
                    {{ __('messages.common.filters') }}
                </button>
            @endif

            <!-- Add Button (if button component is provided) -->
            @if($showButtonOnHeader && $buttonComponent)
                @include($buttonComponent)
            @endif
        </div>
    </div>

    <!-- Filters Section -->
    @if(count($filters) > 0 && $showFilters)
        <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($filters as $filter)
                    <div class="mb-4">
                        <label for="filter-{{ $filter['key'] }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $filter['label'] }}
                        </label>
                        
                        @if($filter['type'] === 'select')
                            <select 
                                wire:model.live="filters.{{ $filter['key'] }}" 
                                id="filter-{{ $filter['key'] }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                                <option value="">{{ __('messages.common.select_all') }}</option>
                                @foreach($filter['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif($filter['type'] === 'multi-select')
                            <select 
                                wire:model.live="filters.{{ $filter['key'] }}" 
                                id="filter-{{ $filter['key'] }}" 
                                multiple
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                                @foreach($filter['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif($filter['type'] === 'date-range')
                            <div class="flex space-x-2">
                                <input 
                                    wire:model.live="filters.{{ $filter['key'] }}.from" 
                                    type="date" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="{{ __('messages.common.from_date') }}"
                                >
                                <input 
                                    wire:model.live="filters.{{ $filter['key'] }}.to" 
                                    type="date" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="{{ __('messages.common.to_date') }}"
                                >
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <div class="flex justify-end mt-4">
                <button 
                    wire:click="resetFilters" 
                    class="px-4 py-2 mr-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                >
                    {{ __('messages.common.reset') }}
                </button>
            </div>
        </div>
    @endif

    <!-- Table Section -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="{{ $this->getTableClass() }}">
            <thead class="{{ $this->getTheadClass() }}">
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" class="{{ $this->getThClass() }} {{ isset($column['class']) ? $column['class'] : '' }}">
                            @if(isset($column['sortable']) && $column['sortable'])
                                <button wire:click="sortBy('{{ $column['field'] }}')" class="flex items-center">
                                    {{ $column['label'] }}
                                    
                                    @if($sortField === $column['field'])
                                        <span>
                                            @if($sortDirection === 'asc')
                                                <x-icons.sort-asc />
                                            @else
                                                <x-icons.sort-desc />
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
            <tbody class="{{ $this->getTbodyClass() }}">
                @if(count($rows) > 0)
                    @foreach($rows as $row)
                        <tr class="{{ $this->getTrClass() }}">
                            @foreach($columns as $column)
                                <td class="{{ $this->getTdClass() }} {{ isset($column['class']) ? $column['class'] : '' }}">
                                    @if(isset($column['view']))
                                        @include($column['view'], ['row' => $row])
                                    @elseif(isset($column['format']) && is_callable($column['format']))
                                        {!! $column['format']($row) !!}
                                    @elseif(isset($column['field']))
                                        {{ data_get($row, $column['field']) }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                            {{ __('messages.common.no_records_found') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div class="{{ $this->getPaginationClass() }}">
        {{ $rows->links() }}
    </div>
</div> 