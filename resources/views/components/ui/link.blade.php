@props([
    'href' => '#',
    'variant' => 'default',
    'class' => ''
])

@php
$baseClasses = 'transition duration-150 ease-in-out';

$variants = [
    'default' => 'text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300',
    'muted' => 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200',
    'danger' => 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . $class;
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a> 