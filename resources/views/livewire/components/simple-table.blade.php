<div>
    @if($showAddButton)
    <div class="d-flex justify-content-end mb-3">
        <button wire:click="$emit('{{ $addButtonEvent }}')" class="btn btn-primary">
            {{ $addButtonTitle }}
        </button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($showSearch)
            <div class="row mb-3">
                <div class="col-md-6">
                    <input 
                        wire:model.debounce.300ms="searchTerm" 
                        type="text" 
                        class="form-control" 
                        placeholder="Search..."
                    />
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    @if(count($filters) > 0)
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="filtersDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filters
                            @if(count($appliedFilters) > 0)
                                <span class="badge bg-info">{{ count($appliedFilters) }}</span>
                            @endif
                        </button>
                        <div class="dropdown-menu p-3" aria-labelledby="filtersDropdown" style="min-width: 250px;">
                            @foreach($filters as $name => $filter)
                                <div class="mb-3">
                                    <label for="filter-{{ $name }}" class="form-label">{{ $filter['label'] }}</label>
                                    
                                    @if($filter['type'] === 'select')
                                        <select 
                                            id="filter-{{ $name }}" 
                                            wire:model="appliedFilters.{{ $name }}" 
                                            class="form-select"
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
                                            class="form-control"
                                        />
                                    @endif
                                </div>
                            @endforeach
                            
                            <div class="d-flex justify-content-between">
                                <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary">
                                    Clear filters
                                </button>
                                <button wire:click="$refresh" class="btn btn-sm btn-primary">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="table-responsive">
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
                                        <div class="d-flex justify-content-center">
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
                <div class="d-flex justify-content-end mt-3">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</div> 