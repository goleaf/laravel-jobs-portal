@props([
    'headers' => [],
    'rows' => [],
    'striped' => true,
    'hoverable' => true,
    'bordered' => true,
    'responsive' => true,
    'loading' => false,
    'emptyMessage' => null,
    'class' => '',
    'actions' => []
])

@php
    $tableClasses = 'min-w-full divide-y divide-gray-200';
    
    if ($bordered) {
        $tableClasses .= ' border border-gray-200';
    }
    
    $tableClasses .= ' ' . $class;
    
    $emptyMessage = $emptyMessage ?? __('messages.no_data_available');
@endphp

<div class="{{ $responsive ? 'overflow-x-auto' : '' }}">
    <div class="align-middle inline-block min-w-full">
        <div class="shadow overflow-hidden border-b border border border-gray-300 -gray-300 -gray-200 sm: rounded -lg">
            @if($loading)
                <div class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded -full h-8 w-8 border-b-2 border border border-gray-300 -gray-300 -blue-600"></div>
                    <span class="ml-2 text-gray-600">{{ __('messages.loading') }}...</span>
                </div>
            @else
                <table class="{{ $tableClasses }}">
                    @if(!empty($headers))
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach($headers as $header)
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $header["class'] ?? '' }}">
                                        @if(isset($header['sortable']) && $header['sortable'])
                                            <button class="group inline-flex items-center text-left font-medium text-gray-500 hover:text-gray-700">
                                                {{ $header['label'] ?? $header }}
                                                <svg class="ml-1 h-4 w-4 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                </svg>
                                            </button>
                                        @else
                                            {{ $header['label'] ?? $header }}
                                        @endif
                                    </th>
                                @endforeach
                                
                                @if(!empty($actions))
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">{{ __('messages.actions') }}</span>
                                    </th>
                                @endif
                            </tr>
                        </thead>
                    @endif
                    
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(!empty($rows))
                            @foreach($rows as $index => $row)
                                <tr class="{{ $hoverable ? 'hover:bg-gray-50' : '' }} {{ $striped && $index % 2 == 1 ? 'bg-gray-50' : '' }}">
                                    @if(is_array($row))
                                        @foreach($row as $cell)
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $$cell }}
                                            </td>
                                        @endforeach
                                    @else
                                        <td colspan="{{ count($headers) + (!empty($actions) ? 1 : 0) }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $$row }}
                                        </td>
                                    @endif
                                    
                                    @if(!empty($actions) && is_array($row))
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @foreach($actions as $action)
                                                    @if(isset($action['condition']) && !$action['condition']($row, $index))
                                                        @continue
                                                    @endif
                                                    
                                                    @if($action['type'] === 'link')
                                                        <a href="{{ $action['url']($row, $index) }}" 
                                                           class="text-{{ $action['color'] ?? 'blue' }}-600 hover:text-{{ $action['color'] ?? 'blue' }}-900 {{ $action['class'] ?? '' }}"
                                                           @if(isset($action['target'])) target="{{ $action['target'] }}" @endif>
                                                            @if(isset($action['icon']))
                                                                {!! $action['icon'] !!}
                                                            @endif
                                                            {{ $action['label'] }}
                                                        </a>
                                                    @elseif($action['type'] === 'button')
                                                        <button type="button" 
                                                                onclick="{{ $action['onclick']($row, $index) }}"
                                                                class="text-{{ $action['color'] ?? 'blue' }}-600 hover:text-{{ $action['color'] ?? 'blue' }}-900 {{ $action['class'] ?? '' }}">
                                                            @if(isset($action['icon']))
                                                                {!! $action['icon'] !!}
                                                            @endif
                                                            {{ $action['label'] }}
                                                        </button>
                                                    @elseif($action['type'] === 'form')
                                                        <form method="{{ $action['method'] ?? 'POST' }}" action="{{ $action['url']($row, $index) }}" class="inline">
                                                            @csrf
                                                            @if(isset($action['method']) && $action['method'] !== 'POST')
                                                                @method($action['method'])
                                                            @endif
                                                            <button type="submit" 
                                                                    class="text-{{ $action['color'] ?? 'red' }}-600 hover:text-{{ $action['color'] ?? 'red' }}-900 {{ $action['class'] ?? '' }}"
                                                                    @if(isset($action['confirm'])) 
                                                                        onclick="return confirm('{{ $action['confirm'] }}')" 
                                                                    @endif>
                                                                @if(isset($action['icon']))
                                                                    {!! $action['icon'] !!}
                                                                @endif
                                                                {{ $action['label'] }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ count($headers) + (!empty($actions) ? 1 : 0) }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <div class="flex flex- flex-1 items-center justify-center py-8">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900 mb-1">{{ __('messages.no_data') }}</p>
                                        <p class="text-gray-500">{{ $emptyMessage }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                
                {{ $slot }}
            @endif
        </div>
    </div>
</div> 