@props(['id' => 'theme-switch'])

<!-- Theme Switch Toggle Button -->
<div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
    x-init="$watch('darkMode', val => { 
        localStorage.setItem('darkMode', val); 
        if (val) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })" 
    class="flex items-center">
    <button @click="darkMode = !darkMode" 
        class="rounded-full p-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
        <span x-show="!darkMode" class="text-yellow-500">
            <x-icons.sun class="w-5 h-5" />
        </span>
        <span x-show="darkMode" class="text-gray-300">
            <x-icons.moon class="w-5 h-5" />
        </span>
    </button>
</div>

@once

@endonce 
@push('scripts')
    @vite('resources/js/components/theme-switch.js')
@endpush
