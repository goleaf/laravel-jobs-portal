@props([
    'column',
    'sortField',
    'sortDirection',
])

<div class="flex items-center">
    <button {{ $attributes }} class="flex items-center text-left">
        {{ $slot }}
        
        @if(isset($column['sortable']) && $column['sortable'])
            <span class="ml-1 flex-shrink-0">
                @if($sortField === $column['field'])
                    @if($sortDirection === 'asc')
                        <x-icons.chevron-up class="w-4 h-4 text-gray-400" />
                    @else
                        <x-icons.chevron-down class="w-4 h-4 text-gray-400" />
                    @endif
                @else
                    <x-icons.chevron-down class="w-4 h-4 opacity-0 group-hover:opacity-100 text-gray-400" />
                @endif
            </span>
        @endif
    </button>
    
    @if(isset($column['filterable']) && $column['filterable'])
        <div class="ml-2">
            <x-table.filter-dropdown :column="$column['field']" />
        </div>
    @endif
</div> 