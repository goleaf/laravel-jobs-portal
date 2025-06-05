@props(['class' => 'w-16 h-16'])

<svg xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 16 16" style="color: #1967D2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="5" r="3" />
    <line x1="12" y1="12" x2="12" y2="19" />
    <line x1="12" y1="19" x2="9" y2="22" />
    <line x1="12" y1="19" x2="15" y2="22" />
</svg> 