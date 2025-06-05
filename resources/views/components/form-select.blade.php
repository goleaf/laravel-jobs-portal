@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'text-red-500' => false,
    'disabled' => false,
    'error' => null,
    'class' => '',
])

@php
    $selectId = $id ?? $name;
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $selectId }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md ' . $class]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $value => $label)
            <option value="{{ $value }}" @if($value == $selected) selected @endif>
                {{ $label }}
            </option>
        @endforeach
    </select>

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div> 