<div>
    <div class="bg-white shadow rounded -lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex- flex-1 md:flex- flex flex-wrap md:items-center md:justify-between mb-4">
                <!-- Left side - Search and Per Page -->
                <div class="flex items-center space-x-4 mb-4 md: mb-0">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('Search...') }}"
                            class="form-input w-64"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <x-icons.search />
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-2 text-sm">{{ __('Per Page') }}:</span>
                        <select wire:model.live="perPage" class="form-input text-sm py-1">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                
                <!-- Right side - Filters -->
                <div class="flex items-center space-x-2">
                    @if(count($filters) > 0)
                    <div class="relative" x-data="{ open: false }">
                        <div x-on:click="filtersOpen = !filtersOpen" class="flex items-center cursor-pointer">
                            <span class="mr-1 text-sm text-gray-700 dark:text-gray-300 tracking-wider">{{ __('messages.common.filter') }}</span>
                            <x-icons.chevron-down />
                        </div>
                        
                        <!-- Filter dropdown -->
                        <div 
                            x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="origin-top-right absolute right-0 mt-2 w-64 rounded -md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                        >
                            <div class="p-4 space-y-4">
                                <div class="border border border-gray-300 -gray-300 -b pb-2 mb-2">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ __('Filter by') }}</h3>
                                </div>
                                
                                @foreach($filters as $filter)
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ $filter['label'] ?? $filter['key'] }}
                                        </label>
                                        
                                        @if($filter['type'] === 'select')
                                            <select 
                                                wire:model.live="filters.{{ $filter['key'] }}" 
                                                class="form-input block w-full text-sm"
                                            >
                                                <option value="">{{ __('All') }}</option>
                                                @foreach($filter['options'] as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($filter['type'] === 'multi-select')
                                            <div class="space-y-2">
                                                @foreach($filter['options'] as $value => $label)
                                                    <div class="flex items-center">
                                                        <input 
                                                            type="checkbox" 
                                                            id="{{ $filter['key'] }}_{{ $value }}"
                                                            wire:model.live="filters.{{ $filter['key'] }}" 
                                                            value="{{ $value }}"
                                                            class="h-4 w-4 text-indigo-600 -600 -600 border border border-gray-300 -gray-300 -gray-300 rounded"
                                                        >
                                                        <label for="{{ $filter['key'] }}_{{ $value }}" class="ml-2 text-sm text-gray-700">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($filter['type'] === 'date-range')
                                            <div class="flex space-x-2">
                                                <input 
                                                    type="date" 
                                                    wire:model.live="filters.{{ $filter['key'] }}.from" 
                                                    class="form-input block w-full text-sm"
                                                    placeholder="{{ __('From') }}"
                                                >
                                                <input 
                                                    type="date" 
                                                    wire:model.live="filters.{{ $filter['key'] }}.to" 
                                                    class="form-input block w-full text-sm"
                                                    placeholder="{{ __('To') }}"
                                                >
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                
                                <div class="flex justify-between pt-2 border border border-gray-300 -gray-300 -t">
                                    <button 
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-sm text-gray-600 hover:text-gray-900"
                                    >
                                        {{ __('Reset') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(count($bulkActions) > 0 && count($selected) > 0)
                    <div class="relative" x-data="{ open: false }">
                        <div x-on:click="open = !open" class="flex items-center cursor-pointer">
                            <span class="mr-1 text-sm text-gray-700 dark:text-gray-300 tracking-wider">{{ __('messages.common.action') }}</span>
                            <x-icons.chevron-down class="h-5 w-5 mr-2 text-gray-400" />
                        </div>
                        
                        <!-- Actions dropdown -->
                        <div 
                            x-show="open" 
                            @click.away="open = false"
                            class="origin-top-right absolute right-0 mt-2 w-48 rounded -md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                        >
                            <div class="py-1">
                                @foreach($bulkActions as $key => $action)
                                <button
                                    wire:click="{{ $action['method'] }}({{ json_encode($selected) }})"
                                    type="button"
                                    class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    {{ $action['label'] }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Filter Pills -->
            @if(count($filterPills) > 0)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($filterPills as $key => $pill)
                <span class="inline-flex rounded -full items-center py-1 pl-3 pr-1 text-sm font-medium bg-indigo-600 -600 -100 text-indigo-600 -600 -800">
                    {{ $pill['label'] }}: {{ $pill['value'] }}
                    <button 
                        wire:click="$set('filters.{{ $key }}', null)" 
                        type="button" 
                        class="flex-shrink-0 ml-0.5 h-5 w-5 rounded -full inline-flex items-center justify-center text-indigo-600 -600 hover: bg-indigo-600 -600 -200 hover: text-indigo-600 -600 -800 focus:outline-none"
                    >
                        <span class="sr-only">{{ __('Remove filter') }}</span>
                        <x-icons.close />
                    </button>
                </span>
                @endforeach
            </div>
            @endif
            
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if(count($bulkActions) > 0)
                            <th scope="col" class="px-3 py-3 w-12">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="selectAll"
                                    class="h-4 w-4 text-indigo-600 -600 -600 border border border-gray-300 -gray-300 -gray-300 rounded"
                                >
                            </th>
                            @endif
                            
                            @foreach($columns as $column)
                            <th 
                                scope="col" 
                                class="w-full divide-y divide-gray-200 head"
                                @if($column['sortable'] ?? false)
                                    wire:click="sortBy('{{ $column['key'] }}')"
                                    style="cursor: pointer;"
                                @endif
                            >
                                <div class="flex items-center space-x-1">
                                    <span>{{ $column['label'] }}</span>
                                    
                                    @if(($column['sortable'] ?? false) && $sortField === $column['key'])
                                    <div class="p-2">
                                        <div class="flex items-center cursor-pointer text-gray-700" wire:click="sortBy('{{ $column['key'] }}', 'asc')">
                                            <span class="mr-1">{{ __('messages.common.asc') }}</span>
                                            <x-icons.chevron-down class="h-4 w-4 text-gray-400" />
                                        </div>
                                        <div class="flex items-center cursor-pointer text-gray-700 dark:text-gray-300" wire:click="sortBy('{{ $column['key'] }}', 'desc')">
                                            <span class="mr-1">{{ __('messages.common.desc') }}</span>
                                            <x-icons.chevron-down class="h-4 w-4 text-gray-400" transform="rotate-180" />
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </th>
                            @endforeach
                            
                            @if(method_exists($this, 'getRowActions'))
                            <th scope="col" class="relative px-3 py-3">
                                <span class="sr-only">{{ __('Actions') }}</span>
                            </th>
                            @endif
                        </tr>
                    </thead>
                    
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rows as $row)
                        <tr wire:key="{{ $row->id }}" class="hover:bg-gray-50">
                            @if(count($bulkActions) > 0)
                            <td class="w-full divide-y divide-gray-200 cell">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="selected" 
                                    value="{{ $row->id }}"
                                    class="h-4 w-4 text-indigo-600 -600 -600 border border border-gray-300 -gray-300 -gray-300 rounded"
                                >
                            </td>
                            @endif
                            
                            @foreach($columns as $column)
                            <td class="w-full divide-y divide-gray-200 cell">
                                @if(isset($column['format']))
                                    {!! $column['format']($row) !!}
                                @else
                                    {{ $row->{$column['key']} }}
                                @endif
                            </td>
                            @endforeach
                            
                            @if(method_exists($this, 'getRowActions'))
                            <td class="w-full divide-y divide-gray-200 cell">
                                <div class="flex items-center space-x-3">
                                    @foreach($this->getRowActions($row) as $action)
                                    <button 
                                        wire:click="{{ $action['method'] }}({{ $row->id }})"
                                        type="button"
                                        class="text-sm text-{{ $action['color'] ?? 'primary' }}-600 hover:text-{{ $action['color'] ?? 'primary' }}-900"
                                    >
                                        {{ $action['label'] }}
                                    </button>
                                    @endforeach
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($columns) + (count($bulkActions) > 0 ? 1 : 0) + (method_exists($this, 'getRowActions') ? 1 : 0) }}" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No records found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $rows->links() }}
            </div>
        </div>
    </div>
</div> 