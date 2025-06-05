@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
    'target' => null,
    'class' => '',
    'wire:loading' => null,
    'wire:target' => null
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    $variantClasses = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500',
        'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white focus:ring-yellow-500',
        'info' => 'bg-indigo-600 hover:bg-indigo-700 text-white focus:ring-indigo-500',
        'outline' => 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 focus:ring-blue-500',
        'ghost' => 'hover:bg-gray-100 text-gray-700 focus:ring-blue-500',
        'link' => 'text-blue-600 hover:text-blue-800 underline-offset-4 hover:underline focus:ring-blue-500'
    ];
    
    $sizeClasses = [
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2 text-base',
        'xl' => 'px-6 py-3 text-base'
    ];
    
    $classes = $baseClasses . ' ' . 
               ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . 
               ($sizeClasses[$size] ?? $sizeClasses['md']);
    
    if ($disabled || $loading) {
        $classes .= ' opacity-50 cursor-not-allowed';
    }
    
    $classes .= ' ' . $class;
    
    $tag = $href ? 'a' : 'button';
    $typeAttr = $href ? null : $type;
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    @if($target) target="{{ $target }}" @endif
    @if($typeAttr) type="{{ $typeAttr }}" @endif
    @if($disabled) disabled @endif
    class="{{ $classes }}"
    @if($attributes->has('wire:loading')) wire:loading @endif
    @if($attributes->has('wire:target')) wire:target="{{ $attributes->get('wire:target') }}" @endif
    {{ $attributes->except(['wire:loading', 'wire:target']) }}
>
    @if($loading)
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ __('messages.loading') }}...
    @else
        @if($icon && $iconPosition === 'left')
            <span class="mr-2">
                {{ $icon }}
            </span>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <span class="ml-2">
                {{ $icon }}
            </span>
        @endif
    @endif
</{{ $tag }}>

@if($attributes->has('wire:loading'))
    <div wire:loading wire:target="{{ $attributes->get('wire:target', '') }}" class="inline-flex items-center">
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ __('messages.processing') }}...
    </div>
@endif 