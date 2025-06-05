@props([
    'name',
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'options' => [],
    'text-red-500' => false,
    'disabled' => false,
    'multiple' => false,
    'error' => null,
    'helpText' => null,
    'id' => null,
    'class' => '',
    'labelClass' => '',
    'containerClass' => '',
    'emptyOption' => null
])

@php
    $id = $id ?? $name;
    $value = old($name, $value);
    $hasError = $error || $errors->has($name);
    $errorMessage = $error ?? $errors->first($name);
    
    $selectClasses = 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm';
    
    if ($hasError) {
        $selectClasses = 'block w-full px-3 py-2 border border-red-300 rounded-md shadow-sm placeholder-red-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm text-red-900';
    }
    
    if ($disabled) {
        $selectClasses .= ' bg-gray-50 text-gray-500 cursor-not-allowed';
    }
    
    $selectClasses .= ' ' . $class;
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
    
    <select 
        name="{{ $multiple ? $name . '[]' : $name }}"
        id="{{ $id }}"
        class="{{ $selectClasses }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        {{ $attributes }}
    >
        @if($emptyOption !== false && !$multiple)
            <option value="">{{ $emptyOption ?? ($placeholder ? $placeholder : __('messages.select_option')) }}</option>
        @endif
        
        @if(is_array($options) && !empty($options))
            @foreach($options as $optionValue => $optionLabel)
                @if(is_array($optionLabel))
                    <optgroup label="{{ $optionValue }}">
                        @foreach($optionLabel as $subValue => $subLabel)
                            <option value="{{ $subValue }}" 
                                @if(
                                    ($multiple && is_array($value) && in_array($subValue, $value)) ||
                                    (!$multiple && $subValue == $value)
                                ) selected @endif>
                                {{ $subLabel }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <option value="{{ $optionValue }}" 
                        @if(
                            ($multiple && is_array($value) && in_array($optionValue, $value)) ||
                            (!$multiple && $optionValue == $value)
                        ) selected @endif>
                        {{ $optionLabel }}
                    </option>
                @endif
            @endforeach
        @endif
        
        {{ $slot }}
    </select>
    
    @if($helpText && !$hasError)
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $helpText }}</p>
    @endif
    
    @if($hasError)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
    @endif
</div> 