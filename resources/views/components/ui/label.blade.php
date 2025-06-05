@props([
    'for' => '',
    'text-red-500' => false,
    'class' => ''
])

@php
$classes = 'block text-sm font-medium text-gray-700 dark:text-gray-300 ' . $class;
@endphp

<label for="{{ $for }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
</label> 