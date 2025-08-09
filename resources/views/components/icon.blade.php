@props([
    'name' => null,
    'class' => '',
    'solid' => false,
])

@php
    $normalized = str_replace('_', '-', (string) $name);
    $component = ($solid ? 'heroicon-s-' : 'heroicon-o-') . $normalized;
@endphp

<x-dynamic-component :component="$component" :class="$class" />



    'chart-bar' => 'M3 3v18h18M9 17V9m6 8V5',
    'currency-dollar' => 'M12 8c-4 0-4 6 0 6 4 0 4 6 0 6M12 2v4m0 12v4',
    'clock' => 'M12 6v6l4 2M12 22a10 10 0 110-20 10 10 0 010 20z',
    'map' => 'M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3z',
    'map-pin' => 'M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z',
    'briefcase' => 'M4 7h16v13H4zM9 7V5h6v2',
    'document' => 'M7 3h8l4 4v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z',
    'document-text' => 'M9 12h6M9 16h6M9 8h3m-1-5h4l4 4v12a2 2 0 01-2 2H8',
    'eye' => 'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 3a3 3 0 100-6 3 3 0 000 6z',
    'star' => 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z',
    'magnifying-glass' => 'M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z',
    'envelope' => 'M4 6l8 6 8-6v12H4V6z',
    'phone' => 'M22 16.92V21a2 2 0 01-2.18 2A19.86 19.86 0 013 5.18 2 2 0 015 3h4.09a2 2 0 012 1.72l.7 4.24a2 2 0 01-.55 1.86l-2 2a16 16 0 006.36 6.36l2-2a2 2 0 011.86-.55l4.24.7A2 2 0 0122 16.92z',
    'globe-alt' => 'M12 2a10 10 0 100 20 10 10 0 000-20zm0 0s4 3 4 10-4 10-4 10-4-3-4-10 4-10 4-10zm-8 10h16',
    'building-office' => 'M3 21h18M9 21V9h6v12M12 3l6 6H6l6-6z',
    'arrow-trending-up' => 'M3 17l6-6 4 4 7-7M17 7h4v4',
    'arrow-trending-down' => 'M3 7l6 6 4-4 7 7M17 17h4v-4',
    'identification' => 'M8 7h8M8 11h8M8 15h5M4 5h16v14H4z',
    'calculator' => 'M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm2 4h8M8 10h2m4 0h2M8 14h2m4 0h2M8 18h8',
  ];
  $d = $paths[$name] ?? $paths['information-circle'];
@endphp

<svg {{ $attributes->merge(['class' => $class, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round']) }}>
  <path d="{{ $d }}" />
  {{-- Note: Add more icons to $paths map as needed --}}
</svg>


