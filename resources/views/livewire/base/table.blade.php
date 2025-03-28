<div>
    <div class="d-flex flex-column">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @if($showButtonOnHeader && !empty($buttonComponent))
                @include($buttonComponent)
            @endif

            @if($showSearchInput)
                <div class="ms-auto">
                    <div class="input-group">
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="{{ __('messages.common.search') }}" 
                            wire:model.debounce.300ms="search"
                        >
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                @if($showHeader)
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th class="{{ $column['headerClass'] ?? 'text-center' }}">
                                @if(isset($column['sortable']) && $column['sortable'])
                                    <a href="#" wire:click.prevent="sortBy('{{ $column['field'] }}')">
                                        {{ $column['label'] }}
                                        @if($sortField === $column['field'])
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort" style="opacity: 0.4"></i>
                                        @endif
                                    </a>
                                @else
                                    {{ $column['label'] }}
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                @endif
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            @foreach($columns as $column)
                                <td class="{{ $column['cellClass'] ?? '' }}">
                                    @if(isset($column['view']))
                                        @include($column['view'], ['row' => $row])
                                    @elseif(isset($column['format']))
                                        {!! $column['format']($row) !!}
                                    @elseif(isset($column['field']))
                                        {{ $row->{$column['field']} }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="text-center">
                                {{ __($emptyMessage) }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($showPagination && $data->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    @if($data->total() > 0)
                        <span>{{ __('Showing') }}</span>
                        <strong>{{ $data->firstItem() }}</strong>
                        <span>{{ __('to') }}</span>
                        <strong>{{ $data->lastItem() }}</strong>
                        <span>{{ __('of') }}</span>
                        <strong>{{ $data->total() }}</strong>
                        <span>{{ __('results') }}</span>
                    @else
                        <span>{{ __('No results found') }}</span>
                    @endif
                </div>
                
                @if($showPerPageOptions)
                    <div class="d-flex align-items-center">
                        <span class="me-2">{{ __('Per page') }}:</span>
                        <select class="form-select form-select-sm" wire:model="perPage">
                            @foreach($perPageOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                <div>
                    {{ $data->links() }}
                </div>
            </div>
        @endif
    </div>
</div> 