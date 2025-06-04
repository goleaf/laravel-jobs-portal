@props([
    'type' => 'button',
    'color' => 'primary',
    'size' => 'md',
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center border font-medium focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    $colors = [
        'primary' => 'border-transparent text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
        'secondary' => 'border-transparent text-white bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
        'success' => 'border-transparent text-white bg-green-600 hover:bg-green-700 focus:ring-green-500',
        'danger' => 'border-transparent text-white bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'warning' => 'border-transparent text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
        'info' => 'border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        'light' => 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:ring-indigo-500',
        'dark' => 'border-transparent text-white bg-gray-800 hover:bg-gray-900 focus:ring-gray-500',
    ];
    
    $sizes = [
        'xs' => 'px-2.5 py-1.5 text-xs rounded',
        'sm' => 'px-3 py-2 text-sm leading-4 rounded-md',
        'md' => 'px-4 py-2 text-sm rounded-md',
        'lg' => 'px-4 py-2 text-base rounded-md',
        'xl' => 'px-6 py-3 text-base rounded-md',
    ];
    
    $colorClasses = $colors[$color] ?? $colors['primary'];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
    
    $classes = $baseClasses . ' ' . $colorClasses . ' ' . $sizeClasses;
    
    if ($disabled) {
        $classes .= ' opacity-50 cursor-not-allowed';
    }
@endphp

<button
    type="{{ $type  }}"
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => $classes])  }}
>
    {{ $slot  }}
</button> 