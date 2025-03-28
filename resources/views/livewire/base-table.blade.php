<div>
    <div class="flex justify-between items-center mb-4">
        @if($showFilterOnHeader)
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <input 
                    type="text" 
                    wire:model.debounce.300ms="search" 
                    placeholder="Search..."
                    class="border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                @if($search)
                <button 
                    wire:click="$set('search', '')" 
                    class="ml-2 text-gray-500 hover:text-gray-700"
                >
                    <x-icons.x class="w-4 h-4" />
                </button>
                @endif
            </div>
            
            <div>
                <select 
                    wire:model="perPage" 
                    class="border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    @foreach($perPageOptions as $option)
                    <option value="{{ $option }}">{{ $option }} per page</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        @if($showButtonOnHeader && $buttonComponent)
            @include($buttonComponent)
        @endif
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto">
        <table class="{{ $this->getTableClass() }}">
            <thead class="{{ $this->getTheadClass() }}">
                <tr>
                    @foreach($columns as $column)
                    <th class="{{ $this->getThClass() }}">
                        @if(isset($column['sortable']) && $column['sortable'])
                        <button 
                            wire:click="sortBy('{{ $column['field'] }}')" 
                            class="flex items-center space-x-1 text-left"
                        >
                            <span>{{ $column['label'] }}</span>
                            
                            @if($sortField === $column['field'])
                                @if($sortDirection === 'asc')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                                @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                @endif
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
                @if($data->count() > 0)
                    @foreach($data as $index => $row)
                    <tr class="{{ $this->getTrClass($row, $index) }}">
                        @foreach($columns as $column)
                        <td class="{{ $this->getTdClass() }}">
                            @if(isset($column['view']))
                                @include($column['view'], ['row' => $row])
                            @elseif(isset($column['field']))
                                {{ $row->{$column['field']} }}
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="{{ count($columns) }}" class="p-3 text-center text-gray-500">
                        No records found.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $data->links() }}
    </div>
</div> 