@props([
    'name',
    'id' => null,
    'value' => null,
    'label' => null,
    'placeholder' => null,
    'rows' => 3,
    'text-red-500' => false,
    'disabled' => false,
    'readonly' => false,
    'autofocus' => false,
    'error' => null,
    'class' => '',
])

@php
    $textareaId = $id ?? $name;
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $textareaId }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $textareaId }}"
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($autofocus) autofocus @endif
        {{ $attributes->merge(['class' => 'mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md ' . $class]) }}
    >{{ $value }}</textarea>

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div> 