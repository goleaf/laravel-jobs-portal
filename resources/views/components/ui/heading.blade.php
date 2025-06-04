@props([
    'size' => 'md',
    'level' => null,
    'class' => ''
])

@php
$sizes = [
    'xs' => 'text-xs font-semibold',
    'sm' => 'text-sm font-semibold',
    'md' => 'text-base font-semibold',
    'lg' => 'text-lg font-semibold',
    'xl' => 'text-xl font-bold',
    '2xl' => 'text-2xl font-bold',
    '3xl' => 'text-3xl font-bold',
];

$classes = 'text-gray-900 dark:text-gray-100 ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $class;

// Determine heading level
$tag = $level ?? match($size) {
    'xs', 'sm' => 'h6',
    'md' => 'h5',
    'lg' => 'h4',
    'xl' => 'h3',
    '2xl' => 'h2',
    '3xl' => 'h1',
    default => 'h5'
};
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}> 