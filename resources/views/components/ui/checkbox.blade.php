@props([
    'name' => '',
    'id' => '',
    'checked' => false,
    'label' => '',
    'class' => ''
])

@php
$classes = 'h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 ' . $class;
@endphp

<div class="flex items-center">
    <input 
        type="checkbox" 
        name="{{ $name }}" 
        id="{{ $id }}" 
        @if($checked) checked @endif
        {{ $attributes->merge(['class' => $classes]) }}
    />
    @if($label)
        <label for="{{ $id }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
            {{ $label }}
        </label>
    @endif
</div> 