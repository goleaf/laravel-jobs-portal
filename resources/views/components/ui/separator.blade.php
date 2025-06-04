@props([
    'class' => ''
])

@php
$classes = 'border-t border-gray-200 dark:border-gray-700 ' . $class;
@endphp

<hr {{ $attributes->merge(['class' => $classes]) }} /> 