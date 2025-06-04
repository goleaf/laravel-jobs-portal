@props([
    'class' => ''
])

@php
$classes = 'w-64 bg-white dark:bg-gray-800 shadow-sm ' . $class;
@endphp

<aside {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</aside> 