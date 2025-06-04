@props([
    'type' => 'text',
    'name',
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => null,
    'error' => null,
    'helpText' => null,
    'id' => null,
    'class' => '',
    'labelClass' => '',
    'containerClass' => '',
    'icon' => null,
    'iconPosition' => 'left'
])

@php
    $id = $id ?? $name;
    $value = old($name, $value);
    $hasError = $error || $errors->has($name);
    $errorMessage = $error ?? $errors->first($name);
    
    $inputClasses = 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm';
    
    if ($hasError) {
        $inputClasses = 'block w-full px-3 py-2 border border-red-300 rounded-md shadow-sm placeholder-red-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm text-red-900';
    }
    
    if ($disabled) {
        $inputClasses .= ' bg-gray-50 text-gray-500 cursor-not-allowed';
    }
    
    if ($readonly) {
        $inputClasses .= ' bg-gray-50';
    }
    
    if ($icon) {
        $inputClasses .= $iconPosition === 'left' ? ' pl-10' : ' pr-10';
    }
    
    $inputClasses .= ' ' . $class;
@endphp

<div class="space-y-1 {{ $containerClass }}">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                {!! $icon !!}
            </div>
        @endif
        
        <input 
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            class="{{ $inputClasses }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            {{ $attributes }}
        />
        
        @if($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                {!! $icon !!}
            </div>
        @endif
    </div>
    
    @if($helpText && !$hasError)
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $helpText }}</p>
    @endif
    
    @if($hasError)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
    @endif
</div> 