@props(["show" => false,"title" => null,"size" =>"md"
])

@php
    $sizes = ["sm" =>"max-w-md","md" =>"max-w-lg","lg" =>"max-w-2xl","xl" =>"max-w-4xl",
    ];
@endphp

<div 
    x-data="{ show: @json($show) }"
    x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>
        
        <div 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative inline-block w-full {{ $sizes[$size] }} bg-white rounded-lg shadow-xl transform transition-all"
        >
            @if($title)
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
                </div>
            @endif
            
            <div class="px-6 py-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>