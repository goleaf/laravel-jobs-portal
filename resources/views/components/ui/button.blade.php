@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left'
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 text-white border border-transparent focus:ring-blue-500 shadow-sm',
    'secondary' => 'bg-gray-100 hover:bg-gray-200 text-gray-900 border border-gray-300 focus:ring-gray-500 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100 dark:border-gray-600',
    'outline' => 'bg-transparent hover:bg-gray-50 text-gray-700 border border-gray-300 focus:ring-blue-500 dark:hover:bg-gray-800 dark:text-gray-300 dark:border-gray-600',
    'ghost' => 'bg-transparent hover:bg-gray-100 text-gray-700 border border-transparent focus:ring-gray-500 dark:hover:bg-gray-800 dark:text-gray-300',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white border border-transparent focus:ring-red-500 shadow-sm',
    'success' => 'bg-green-600 hover:bg-green-700 text-white border border-transparent focus:ring-green-500 shadow-sm',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white border border-transparent focus:ring-yellow-500 shadow-sm',
];

$sizes = [
    'xs' => 'px-2.5 py-1.5 text-xs gap-1',
    'sm' => 'px-3 py-2 text-sm gap-1.5',
    'md' => 'px-4 py-2.5 text-sm gap-2',
    'lg' => 'px-6 py-3 text-base gap-2',
    'xl' => 'px-8 py-4 text-lg gap-3',
];

$classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];

$tag = $href ? 'a' : 'button';
$attributes = $href ? ['href' => $href] : ['type' => $type];

if ($disabled) {
    $attributes['disabled'] = true;
    if ($href) {
        $attributes['href'] = '#';
        $classes .= ' pointer-events-none';
    }
}
@endphp

@if($href)
    <a 
        href="{{ $disabled ? '#' : $href }}" 
        class="{{ $classes }} {{ $disabled ? 'pointer-events-none' : '' }}"
        @if($loading) 
            aria-busy="true" 
            aria-describedby="loading-text"
        @endif
        {{-- {{ $attributes }} --}}
    >
        @if($loading)
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span id="loading-text" class="sr-only">{{ __('common.loading') }}</span>
        @elseif($icon && $iconPosition === 'left')
            <x-icon :name="$icon" class="h-4 w-4" />
        @endif

        {{ $slot }}

        @if($icon && $iconPosition === 'right' && !$loading)
            <x-icon :name="$icon" class="h-4 w-4" />
        @endif
    </a>
@else
    <button 
        type="{{ $type }}" 
        class="{{ $classes }}"
        @if($disabled) disabled @endif
        @if($loading) 
            aria-busy="true" 
            aria-describedby="loading-text"
        @endif
        {{-- {{ $attributes }} --}}
    >
        @if($loading)
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span id="loading-text" class="sr-only">{{ __('common.loading') }}</span>
        @elseif($icon && $iconPosition === 'left')
            <x-icon :name="$icon" class="h-4 w-4" />
        @endif

        {{ $slot }}

        @if($icon && $iconPosition === 'right' && !$loading)
            <x-icon :name="$icon" class="h-4 w-4" />
        @endif
    </button>
@endif 