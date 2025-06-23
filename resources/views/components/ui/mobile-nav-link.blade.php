@props([
    'href' => '#',
    'active' => false
])

@php
$classes = $active 
    ? 'bg-blue-50 text-blue-700 border-blue-500 dark:bg-blue-900/50 dark:text-blue-300' 
    : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-gray-800';

$classes .= ' block px-3 py-2 rounded-md text-base font-medium border-l-4 border-transparent transition-all duration-200';
@endphp

<a 
    href="{{ $href }}" 
    {{ $attributes->merge(['class' => $classes]) }}
    @if($active)
        aria-current="page"
    @endif
>
    {{ $slot }}
</a> 