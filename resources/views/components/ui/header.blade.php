@props([
    'class' => ''
])

@php
$classes = 'bg-white dark:bg-gray-800 shadow ' . $class;
@endphp

<header {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</header> 