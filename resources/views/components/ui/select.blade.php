@props([
    'name' => '',
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'size' => 'md',
    'variant' => 'default',
    'error' => null,
    'hint' => null,
    'label' => null,
    'icon' => null,
    'searchable' => false
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
    'error' => 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 dark:border-red-500 dark:text-red-100',
    'success' => 'border-green-300 text-green-900 focus:ring-green-500 focus:border-green-500 dark:border-green-500 dark:text-green-100',
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

if ($icon) {
    $classes .= ' pl-10';
}

$selectId = $id ?? $name;
@endphp

<div class="space-y-1">
    @if($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-md shadow-sm">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-icon :name="$icon" class="h-5 w-5 text-gray-400" />
            </div>
        @endif

        <select
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            id="{{ $selectId }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($multiple) multiple @endif
            {{ $attributes->merge(['class' => $classes]) }}
        >
            @if($placeholder && !$multiple)
                <option value="" {{ !$selected ? 'selected' : '' }} disabled>
                    {{ $placeholder }}
                </option>
            @endif

            @foreach($options as $value => $label)
                @if(is_array($label))
                    <!-- Option Group -->
                    <optgroup label="{{ $value }}">
                        @foreach($label as $groupValue => $groupLabel)
                            <option 
                                value="{{ $groupValue }}" 
                                @if($multiple)
                                    {{ in_array($groupValue, (array)$selected) ? 'selected' : '' }}
                                @else
                                    {{ $groupValue == $selected ? 'selected' : '' }}
                                @endif
                            >
                                {{ $groupLabel }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <!-- Regular Option -->
                    <option 
                        value="{{ $value }}" 
                        @if($multiple)
                            {{ in_array($value, (array)$selected) ? 'selected' : '' }}
                        @else
                            {{ $value == $selected ? 'selected' : '' }}
                        @endif
                    >
                        {{ $label }}
                    </option>
                @endif
            @endforeach
        </select>

        @if($error)
            <div class="absolute inset-y-0 right-0 pr-8 flex items-center pointer-events-none">
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

@if($searchable)
    @push('scripts')
    <script>
    // Enhanced searchable select functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('{{ $selectId }}');
        if (selectElement && typeof Choices !== 'undefined') {
            new Choices(selectElement, {
                searchEnabled: true,
                searchChoices: true,
                searchPlaceholderValue: '{{ __("ui.search_placeholder") }}',
                noResultsText: '{{ __("ui.no_results_found") }}',
                noChoicesText: '{{ __("ui.no_choices_available") }}',
                itemSelectText: '{{ __("ui.press_to_select") }}',
                removeItemButton: {{ $multiple ? 'true' : 'false' }},
                shouldSort: false
            });
        }
    });
    </script>
    @endpush
@endif 