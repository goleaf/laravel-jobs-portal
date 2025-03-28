<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            @if(isset($title))
                <h2 class="text-lg font-medium text-gray-900">{{ $title }}</h2>
            @endif

            <div class="flex items-center space-x-4">
                @if($showFilterOnHeader && !empty($filterComponents))
                    <div class="flex items-center">
                        @foreach($filterComponents as $component)
                            @if(is_string($component))
                                @include($component)
                            @endif
                        @endforeach
                    </div>
                @endif

                @if($showButtonOnHeader && !empty($buttonComponent))
                    <div>
                        @include($buttonComponent)
                    </div>
                @endif

                <div class="flex items-center space-x-2">
                    <label for="table-search" class="sr-only">{{ __('messages.common.search') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-icons.search class="w-5 h-5 text-gray-400" />
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="search" id="table-search" class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('messages.common.search') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($columns as $column)
                            @if(!$column->isHidden())
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider 
                                    @if($column->isSortable()) cursor-pointer hover:bg-gray-100 @endif
                                    @if($sortField === $column->getField()) bg-gray-100 @endif"
                                    @if($column->isSortable()) wire:click="sortBy('{{ $column->getField() }}')" @endif>
                                    <div class="flex items-center">
                                        {{ $column->getTitle() }}
                                        @if($column->isSortable())
                                            <span class="ml-2">
                                                @if($sortField === $column->getField())
                                                    @if($sortDirection === 'asc')
                                                        <x-icons.sort-asc class="w-4 h-4" />
                                                    @else
                                                        <x-icons.sort-desc class="w-4 h-4" />
                                                    @endif
                                                @else
                                                    <x-icons.sort class="w-4 h-4 opacity-0 group-hover:opacity-100" />
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($results as $result)
                        <tr class="hover:bg-gray-50">
                            @foreach($columns as $column)
                                @if(!$column->isHidden())
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($column->getViewComponent())
                                            @include($column->getViewComponent(), ['row' => $result])
                                        @else
                                            {{ data_get($result, $column->getField()) }}
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ collect($columns)->reject->isHidden()->count() }}" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                {{ __('messages.common.no_records_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm text-gray-700">
                        {{ __('messages.common.showing') }} {{ $results->firstItem() ?? 0 }} {{ __('messages.common.to') }} {{ $results->lastItem() ?? 0 }} {{ __('messages.common.of') }} {{ $results->total() }} {{ __('messages.common.results') }}
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <div>
                        <label for="perPage" class="sr-only">{{ __('messages.common.per_page') }}</label>
                        <select id="perPage" wire:model.live="perPage" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @foreach($perPageOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{ $results->links() }}
                </div>
            </div>
        </div>
    </div>
</div> 