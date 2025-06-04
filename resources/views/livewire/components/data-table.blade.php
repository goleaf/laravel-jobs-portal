<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="bg-white shadow rounded-lg overflow-hidden -header flex justify-between items-center">
        @if($showFilterOnHeader || $showButtonOnHeader)
            <div class="flex flex-wrap">
                @if($showFilterOnHeader && !empty($filterComponents))
                    <div class="me-3">
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
            </div>
        @endif

        <div class="flex items-center ms-auto">
            <div class="flex items-center">
                <label for="paginate" class="me-2">{{ __('messages.common.showing') }}</label>
                <select id="paginate" wire:model.live="perPage" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 -sm">
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <div class="ms-3">
                <input type="search" wire:model.live.debounce.{{ $searchDebounce }}ms="search" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 -sm" 
                    placeholder="{{ __('messages.common.search') }}">
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
        <div class="w-full divide-y divide-gray-200 -responsive">
            <table class="min-w-full divide-y divide-gray-200 w-full divide-y divide-gray-200 -striped align-middle mb-0">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            @if(!$column->isHidden())
                                <th class="{{ $column->isSortable() ? 'sorting' : '' }} {{ $sortField === $column->getField() ? 'sorting_' . $sortDirection : '' }}"
                                    @if($column->isSortable()) wire:click="sortBy('{{ $column->getField() }}')" @endif>
                                    {{ $column->getTitle() }}
                                </th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            @foreach($columns as $column)
                                @if(!$column->isHidden())
                                    <td>
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
                            <td colspan="{{ collect($columns)->reject->isHidden()->count() }}" class="text-center">
                                {{ __('messages.common.no_records_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden -footer flex justify-end">
        {{ $results->links() }}
    </div>
</div> 