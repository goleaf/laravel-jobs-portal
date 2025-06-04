@props([
    'class' => ''
])

@php
$classes = 'flex-1 ' . $class;
@endphp

<main {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</main> 