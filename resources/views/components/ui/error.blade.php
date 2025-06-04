@props([
    'class' => ''
])

@php
$classes = 'mt-1 text-sm text-red-600 dark:text-red-400 ' . $class;
@endphp

<p {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</p> 