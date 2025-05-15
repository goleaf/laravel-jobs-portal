<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        @if($showFilterOnHeader || $showButtonOnHeader)
            <div class="d-flex flex-wrap">
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

        <div class="d-flex align-items-center ms-auto">
            <div class="d-flex align-items-center">
                <label for="paginate" class="me-2">{{ __('messages.common.showing') }}</label>
                <select id="paginate" wire:model.live="perPage" class="form-select form-select-sm">
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <div class="ms-3">
                <input type="search" wire:model.live.debounce.{{ $searchDebounce }}ms="search" class="form-control form-control-sm" 
                    placeholder="{{ __('messages.common.search') }}">
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
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

    <div class="card-footer d-flex justify-content-end">
        {{ $results->links() }}
    </div>
</div> 