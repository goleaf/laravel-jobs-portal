@props([
    'name' => '',
    'id' => null,
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'rows' => 4,
    'maxlength' => null,
    'size' => 'md',
    'variant' => 'default',
    'error' => null,
    'hint' => null,
    'label' => null,
    'showCounter' => false,
    'autoResize' => false
])

@php
$baseClasses = 'block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none';

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

if ($autoResize) {
    $classes .= ' resize-none overflow-hidden';
}

$textareaId = $id ?? $name;
@endphp

<div class="space-y-1">
    @if($label)
        <div class="flex justify-between">
            <label for="{{ $textareaId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
            
            @if($showCounter && $maxlength)
                <span class="text-sm text-gray-500 dark:text-gray-400" id="{{ $textareaId }}-counter">
                    <span id="{{ $textareaId }}-current">{{ strlen($value) }}</span>/<span>{{ $maxlength }}</span>
                </span>
            @endif
        </div>
    @endif

    <div class="relative">
        <textarea
            name="{{ $name }}"
            id="{{ $textareaId }}"
            rows="{{ $rows }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            @if($autoResize) data-auto-resize="true" @endif
            {{ $attributes->merge(['class' => $classes]) }}
        >{{ $value }}</textarea>

        @if($error)
            <div class="absolute top-2 right-2">
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

@if($showCounter || $autoResize)
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('{{ $textareaId }}');
        
        @if($showCounter && $maxlength)
        // Character counter
        const counter = document.getElementById('{{ $textareaId }}-current');
        
        function updateCounter() {
            const currentLength = textarea.value.length;
            counter.textContent = currentLength;
            
            // Update counter color based on remaining characters
            const remaining = {{ $maxlength }} - currentLength;
            const counterContainer = document.getElementById('{{ $textareaId }}-counter');
            
            if (remaining < 20) {
                counterContainer.classList.add('text-red-500');
                counterContainer.classList.remove('text-yellow-500', 'text-gray-500');
            } else if (remaining < 50) {
                counterContainer.classList.add('text-yellow-500');
                counterContainer.classList.remove('text-red-500', 'text-gray-500');
            } else {
                counterContainer.classList.add('text-gray-500');
                counterContainer.classList.remove('text-red-500', 'text-yellow-500');
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter(); // Initialize
        @endif
        
        @if($autoResize)
        // Auto-resize functionality
        function autoResize() {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
        
        textarea.addEventListener('input', autoResize);
        textarea.addEventListener('change', autoResize);
        
        // Initialize
        autoResize();
        @endif
    });
    </script>
    @endpush
@endif 