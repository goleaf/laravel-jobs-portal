@props([
    'name',
    'id' => null,
    'label' => null,
    'required' => false,
    'disabled' => false,
    'accept' => null,
    'multiple' => false,
    'error' => null,
    'class' => '',
    'help' => null,
])

@php
    $fileId = $id ?? $name;
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $fileId }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
        <div class="space-y-1 text-center">
            <x-icons.upload class="mx-auto h-12 w-12 text-gray-400" />
            <div class="flex text-sm text-gray-600">
                <label for="{{ $fileId }}" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                    <span>Upload a file</span>
                    <input 
                        id="{{ $fileId }}" 
                        name="{{ $name }}" 
                        type="file" 
                        class="sr-only"
                        @if($required) required @endif
                        @if($disabled) disabled @endif
                        @if($accept) accept="{{ $accept }}" @endif
                        @if($multiple) multiple @endif
                        {{ $attributes->merge(['class' => $class]) }}
                    >
                </label>
                <p class="pl-1">or drag and drop</p>
            </div>
            
            @if($help)
                <p class="text-xs text-gray-500">
                    {{ $help }}
                </p>
            @endif
        </div>
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div> 