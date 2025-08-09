@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
])

<x-ui.button
    :variant="$variant"
    :size="$size"
    :type="$type"
    :href="$href"
    :disabled="$disabled"
    :loading="$loading"
    :icon="$icon"
    :icon-position="$iconPosition"
    {{ $attributes }}
>
    {{ $slot }}
</x-ui.button>