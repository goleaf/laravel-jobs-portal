@props([
    'name',
    'id' => null,
    'label' => null,
    'value',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'class' => '',
])

@php
    $radioId = $id ?? "{$name}_{$value}";
@endphp

<div class="mb-4 flex items-start">
    <div class="flex items-center h-5">
        <input
            type="radio"
            name="{{ $name }}"
            id="{{ $radioId }}"
            value="{{ $value }}"
            @if($checked) checked @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => 'focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 ' . $class]) }}
        />
    </div>
    
    @if($label)
        <div class="ml-3 text-sm">
            <label for="{{ $radioId }}" class="font-medium text-gray-700">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        </div>
    @endif
</div>

@if($error)
    <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
@endif 