@props([
    'class' => ''
])

@php
$classes = 'space-y-2 ' . $class;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div> 