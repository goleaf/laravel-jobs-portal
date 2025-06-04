@props([
    'variant' => 'default',
    'size' => 'base',
    'class' => ''
])

@php
$baseClasses = '';

$variants = [
    'default' => 'text-gray-900 dark:text-gray-100',
    'muted' => 'text-gray-600 dark:text-gray-400',
    'success' => 'text-green-600 dark:text-green-400',
    'danger' => 'text-red-600 dark:text-red-400',
    'warning' => 'text-yellow-600 dark:text-yellow-400',
];

$sizes = [
    'xs' => 'text-xs',
    'sm' => 'text-sm',
    'base' => 'text-base',
    'lg' => 'text-lg',
    'xl' => 'text-xl',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['base']) . ' ' . $class;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span> 