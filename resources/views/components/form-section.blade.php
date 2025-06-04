@props([
    'title' => null,
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6'])  }}>
    @if($title || $description)
        <div class="md: flex-1 -span-1">
            @if($title)
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $title  }}</h3>
            @endif
            
            @if($description)
                <p class="mt-1 text-sm text-gray-600">{{ $description  }}</p>
            @endif
        </div>
    @endif

    <div class="mt-5 md:mt-0 md: flex-1 -span-2">
        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md">
            {{ $slot  }}
        </div>
    </div>
</div> 