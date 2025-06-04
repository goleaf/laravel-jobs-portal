<div>
    @if($showAddButton)
    <div class="flex justify-content-end mb-3">
        <button wire:click="$emit('{{ $addButtonEvent }}')" class="btn px-4 py-2 rounded font-medium transition-colors -primary">
            {{ $addButtonTitle }}
        </button>
    </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-white shadow rounded-lg overflow-hidden -body">
            @if($showSearch)
            <div class="flex flex-wrap mb-3">
                <div class="flex-1 -md-6">
                    <input 
                        wire:model.debounce.300ms="searchTerm" 
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" 
                        placeholder="Search..."
                    />
                </div>
                <div class="flex-1 -md-6 flex justify-content-end">
                    @if(count($filters) > 0)
                    <div class="dropdown">
                        <button class="btn px-4 py-2 rounded font-medium transition-colors -secondary dropdown-toggle" type="button" id="filtersDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filters
                            @if(count($appliedFilters) > 0)
                                <span class="badge bg-info">{{ count($appliedFilters) }}</span>
                            @endif
                        </button>
                        <div class="dropdown-menu p-3" aria-labelledby="filtersDropdown" style="min-width: 250px;">
                            @foreach($filters as $name => $filter)
                                <div class="mb-3">
                                    <label for="filter-{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $filter['label'] }}</label>
                                    
                                    @if($filter['type'] === 'select')
                                        <select 
                                            id="filter-{{ $name }}" 
                                            wire:model="appliedFilters.{{ $name }}" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                        >
                                            <option value="">-- Select --</option>
                                            @foreach($filter['options'] as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input 
                                            type="{{ $filter['type'] }}" 
                                            id="filter-{{ $name }}" 
                                            wire:model="appliedFilters.{{ $name }}" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                        />
                                    @endif
                                </div>
                            @endforeach
                            
                            <div class="flex justify-between">
                                <button wire:click="resetFilters" class="btn px-3 py-1.5 text-sm px-4 py-2 rounded font-medium transition-colors -outline-secondary">
                                    Clear filters
                                </button>
                                <button wire:click="$refresh" class="btn px-3 py-1.5 text-sm px-4 py-2 rounded font-medium transition-colors -primary">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="w-full divide-y divide-gray-200 -responsive">
                <table class="{{ $tableClass }}">
                    @if($showHeader)
                    <thead>
                        <tr>
                            @foreach($columns as $key => $column)
                                <th class="{{ $thClass }}" wire:click="sortBy('{{ $key }}')">
                                    {{ $column['label'] }}
                                    @if($sortColumn === $key)
                                        <span>
                                            @if($sortDirection === 'asc')
                                                <i class="fa-solid fa-arrow-up-short-wide"></i>
                                            @else
                                                <i class="fa-solid fa-arrow-down-wide-short"></i>
                                            @endif
                                        </span>
                                    @endif
                                </th>
                            @endforeach
                            @if(isset($actions))
                                <th class="{{ $thClass }}">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    @endif
                    
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                @foreach($columns as $key => $column)
                                    <td class="{{ $tdClass }}">
                                        @if(isset($column['view']))
                                            @include($column['view'], ['row' => $item])
                                        @elseif(isset($column['format']))
                                            {{ $column['format']($item) }}
                                        @else
                                            {{ data_get($item, $key) }}
                                        @endif
                                    </td>
                                @endforeach

                                @if($actions ?? false)
                                    <td class="{{ $tdClass }}">
                                        <div class="flex justify-center">
                                            @include($actions, ['row' => $item])
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + ($actions ?? false ? 1 : 0) }}" class="text-center">
                                    No records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($showPagination && $items->hasPages())
                <div class="flex justify-content-end mt-3">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</div> 