@props(['title'])

<div class="px-6 py-4 border-b border-gray-200">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">
            {{ $title }}
        </h3>
        
        <button 
            @click="close()"
            type="button" 
            class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
        >
            <span class="sr-only">{{ __('close') }}</span>
            <x-icons.x class="h-6 w-6" />
        </button>
    </div>
</div> 