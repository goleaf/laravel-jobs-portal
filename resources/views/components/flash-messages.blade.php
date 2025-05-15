@if (session()->has('success'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 4000)" 
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 mb-4 shadow-md"
         role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <x-icons.success />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false" class="inline-flex text-green-500 hover:bg-green-100 rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <span class="sr-only">Dismiss</span>
                        <x-icons.x class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if (session()->has('error'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 4000)" 
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 mb-4 shadow-md"
         role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <x-icons.error />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false" class="inline-flex text-red-500 hover:bg-red-100 rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <span class="sr-only">Dismiss</span>
                        <x-icons.x class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if (session()->has('warning'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 4000)" 
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-4 mb-4 shadow-md"
         role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <x-icons.warning />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">{{ session('warning') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false" class="inline-flex text-yellow-500 hover:bg-yellow-100 rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <span class="sr-only">Dismiss</span>
                        <x-icons.x class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif 