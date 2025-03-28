@section('add-button')
    <button id="addJobTypeBtn" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
        <x-icons.add class="w-5 h-5 mr-2" />
        {{ __('messages.job_type.new_job_type') }}
    </button>
@endsection

<div>
    <div class="flex flex-col md:flex-row justify-between mb-4 space-y-4 md:space-y-0">
        <!-- Search Box -->
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

        <!-- Per page and buttons -->
        <div class="flex flex-wrap items-center justify-end gap-2">
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
            
            <div class="flex items-center space-x-2">
                @yield('add-button')
            </div>
        </div>
    </div>

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
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
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