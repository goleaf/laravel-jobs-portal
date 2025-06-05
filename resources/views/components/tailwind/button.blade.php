@props(["variant" =>"primary","size" =>"md","type" =>"button"
])

@php
    $variants = ["primary" =>"bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500","secondary" =>"bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500","success" =>"bg-green-600 hover:bg-green-700 text-white focus:ring-green-500","danger" =>"bg-red-600 hover:bg-red-700 text-white focus:ring-red-500","outline" =>"border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-blue-500",
    ];
    
    $sizes = ["sm" =>"px-3 py-1.5 text-xs","md" =>"px-4 py-2 text-sm","lg" =>"px-6 py-3 text-base",
    ];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(["class" =>"inline-flex items-center font-medium rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2" . $variants[$variant] ."" . $sizes[$size]
    ]) }}
>
    {{ $slot }}
</button>