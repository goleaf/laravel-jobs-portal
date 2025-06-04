@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class])  }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
    <path d="M2 21h20v-2H2v2zm9-9h3l-4-4-4 4h3v7h2v-7zm11-2.53l-4-4L18 2l-4 4-4-4v2.53l4 4L10 8l4 4h2l4-4v4h2V9.47z"/>
</svg> 