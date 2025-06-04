@props([
    'class' => '',
    'padding' => 'p-6'
])

@php
$classes = $padding . ' ' . $class;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div> 