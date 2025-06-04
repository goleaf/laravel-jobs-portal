@props([
    'href' => '/',
    'name' => '',
    'logo' => null,
    'class' => ''
])

@php
$classes = 'inline-flex items-center space-x-2 text-xl font-bold text-gray-900 dark:text-gray-100 hover:text-gray-700 dark:hover:text-gray-300 transition duration-150 ease-in-out ' . $class;
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($logo)
        <img src="{{ $logo }}" alt="{{ $name }}" class="h-8 w-8" />
    @endif
    @if($name)
        <span>{{ $name }}</span>
    @else
        {{ $slot }}
    @endif
</a> 