@props([
    'type' => 'text',
    'invalid' => false,
    'class' => '',
    'size' => 'md'
])

@php
$baseClasses = 'block w-full border rounded-md shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2';

$sizes = [
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2 text-sm', 
    'lg' => 'px-4 py-3 text-base',
];

if ($invalid) {
    $stateClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500';
} else {
    $stateClasses = 'border-gray-300 focus:border-blue-500 focus:ring-blue-500';
}

$classes = $baseClasses . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $stateClasses . ' ' . $class;
@endphp

<input type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} /> 