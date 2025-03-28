@props(['class' => 'w-6 h-6'])

<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $class]) }}>
    <circle cx="11" cy="11" r="10" stroke="#1967D2" stroke-width="2" fill="white" />
    <line x1="11" y1="11" x2="11" y2="7" stroke="#1967D2" stroke-width="1.5" />
    <line x1="11" y1="11" x2="15" y2="11" stroke="#1967D2" stroke-width="1.5" />
    <circle cx="11" cy="11" r="1.5" fill="#1967D2" />
</svg> 