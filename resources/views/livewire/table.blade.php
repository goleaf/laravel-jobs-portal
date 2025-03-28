<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-2 md:space-y-0">
        <!-- Search Box -->
        <div class="w-full md:w-1/3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input 
                    type="search" 
                    wire:model.live.debounce.300ms="search" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2" 
                    placeholder="{{ __('messages.common.search') }}"
                >
            </div>
        </div>

        <!-- Per Page and Actions -->
        <div class="flex items-center space-x-2">
            <!-- Per Page Selector -->
            <select 
                wire:model.live="perPage" 
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2"
            >
                @foreach($this->perPageOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>

            <!-- Slot for custom actions -->
            @if(isset($actions))
                {{ $actions }}
            @endif
        </div>
    </div>

    <!-- Filters -->
    @if(count($filters) > 0)
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="mb-2 flex justify-between items-center">
                <h4 class="text-lg font-semibold">{{ __('messages.common.filters') }}</h4>
                <button 
                    type="button"
                    wire:click="resetFilters"
                    class="text-sm text-blue-600 hover:underline"
                >
                    {{ __('messages.common.reset_filters') }}
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($filters as $filter)
                    <div>
                        @if($filter['type'] === 'select')
                            @include('livewire.filters.select', ['filter' => $filter])
                        @elseif($filter['type'] === 'multiselect')
                            @include('livewire.filters.multiselect', ['filter' => $filter])
                        @elseif($filter['type'] === 'date')
                            @include('livewire.filters.date', ['filter' => $filter])
                        @elseif($filter['type'] === 'daterange')
                            @include('livewire.filters.daterange', ['filter' => $filter])
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Main Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @if(count($this->selectedRows) > 0 || $this->selectAll)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
                            <input 
                                type="checkbox" 
                                wire:model.live="selectAll"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                        </th>
                    @endif
                    
                    @foreach($columns as $column)
                        <th 
                            scope="col" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            @if($column->isSortable()) 
                                wire:click="sortBy('{{ $column->getField() }}')" 
                                style="cursor: pointer;"
                            @endif
                        >
                            <div class="flex items-center space-x-1">
                                <span>{{ $column->getLabel() }}</span>
                                
                                @if($column->isSortable())
                                    <span class="flex flex-col">
                                        @if($sortField === $column->getField())
                                            @if($sortDirection === 'asc')
                                                <svg class="h-3 w-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="h-3 w-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50">
                        @if(count($this->selectedRows) > 0 || $this->selectAll)
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input 
                                    type="checkbox" 
                                    value="{{ $row->id }}" 
                                    wire:model.live="selectedRows"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                >
                            </td>
                        @endif
                        
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {!! $column->render($row) !!}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + (count($this->selectedRows) > 0 || $this->selectAll ? 1 : 0) }}" class="px-6 py-4 text-center text-gray-500">
                            {{ __('messages.common.no_records') }}
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