@props([
    'href' => '#',
    'active' => false,
    'external' => false
])

@php
$classes = $active 
    ? 'bg-blue-50 text-blue-700 border-blue-500 dark:bg-blue-900/50 dark:text-blue-300 dark:border-blue-400' 
    : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50 border-transparent dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-gray-800';

$classes .= ' px-3 py-2 rounded-md text-sm font-medium border-b-2 transition-all duration-200';
@endphp

<a 
    href="{{ $href }}" 
    {{ $attributes->merge(['class' => $classes]) }}
    @if($external) 
        target="_blank" 
        rel="noopener noreferrer"
    @endif
    @if($active)
        aria-current="page"
    @endif
>
    {{ $slot }}
</a> 