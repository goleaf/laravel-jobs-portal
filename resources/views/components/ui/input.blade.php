@props([
    'type' => 'text',
    'name' => '',
    'id' => null,
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'size' => 'md',
    'variant' => 'default',
    'error' => null,
    'hint' => null,
    'label' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'autocomplete' => null
])

@php
$baseClasses = 'block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200';

$sizes = [
    'sm' => 'px-3 py-2 text-sm rounded-md',
    'md' => 'px-4 py-2.5 text-sm rounded-lg',
    'lg' => 'px-4 py-3 text-base rounded-lg',
];

$variants = [
    'default' => '',
    'error' => 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 dark:border-red-500 dark:text-red-100',
    'success' => 'border-green-300 text-green-900 placeholder-green-300 focus:ring-green-500 focus:border-green-500 dark:border-green-500 dark:text-green-100',
];

$classes = $baseClasses . ' ' . $sizes[$size];

if ($error) {
    $classes .= ' ' . $variants['error'];
} else {
    $classes .= ' ' . $variants[$variant];
}

if ($disabled) {
    $classes .= ' opacity-50 cursor-not-allowed';
}

if ($readonly) {
    $classes .= ' bg-gray-50 dark:bg-gray-600';
}

if ($icon) {
    $classes .= $iconPosition === 'left' ? ' pl-10' : ' pr-10';
}

$inputId = $id ?? $name;
@endphp

<div class="space-y-1">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-md shadow-sm">
        @if($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-icon :name="$icon" class="h-5 w-5 text-gray-400" />
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            @if($value) value="{{ $value }}" @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            {{ $attributes->merge(['class' => $classes]) }}
        >

        @if($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <x-icon :name="$icon" class="h-5 w-5 text-gray-400" />
            </div>
        @endif

        @if($error)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <x-icon name="exclamation-circle" class="h-5 w-5 text-red-500" />
            </div>
        @endif
    </div>

    @if($hint && !$error)
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div> 