<div>
    <div class="d-flex flex-column">
        <div>
            @if($this->showButtonOnHeader)
                @include($this->buttonComponent)
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            @foreach($columns as $column)
                                <th class="text-center">
                                    @if($column->isSortable())
                                        <a href="#" wire:click.prevent="sort('{{ $column->getField() }}')">
                                            {{ $column->getTitle() }}
                                            @if($this->getSort($column->getField()) == $column->getField())
                                                @if($this->getSortDirection($column->getField()) == 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    @else
                                        {{ $column->getTitle() }}
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $rowIndex => $row)
                            <tr>
                                @foreach($columns as $colIndex => $column)
                                    @php
                                        $attributes = $this->getTdAttributes($column, $row, $colIndex, $rowIndex);
                                        $attributeString = '';
                                        foreach ($attributes as $key => $value) {
                                            $attributeString .= "$key=\"$value\" ";
                                        }
                                    @endphp
                                    <td {!! trim($attributeString) !!}>
                                        {!! $column->renderContents($row) !!}
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) }}" class="text-center">
                                    {{ __('messages.flash.no_record') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    @if($rows->total() > 0)
                        <span>{{ __('Showing') }}</span>
                        <strong>{{ $rows->firstItem() }}</strong>
                        <span>{{ __('to') }}</span>
                        <strong>{{ $rows->lastItem() }}</strong>
                        <span>{{ __('of') }}</span>
                        <strong>{{ $rows->total() }}</strong>
                        <span>{{ __('results') }}</span>
                    @else
                        <span>{{ __('No results found') }}</span>
                    @endif
                </div>
                
                @if($rows->hasPages())
                    <div>
                        {{ $rows->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 